<?php

namespace Wave\Http\Controllers;

use App\Rules\Rfc;
use App\Services\UserService;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Wave\User;
use Wave\KeyValue;
use Wave\ApiKey;
use TCG\Voyager\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function index($section = ''){
        if(empty($section) || !view()->exists("theme::settings.partials." . $section)){
            return redirect(route('wave.settings', 'profile'));
        }
    	return view('theme::settings.index', compact('section'));
    }

    public function addressUpdatePut(Request $request)
    {
        $request->validate([
            'line1' => 'required|max:255|string',
            'line2' => 'required|max:255|string',
            'line3' => 'required|sometimes|max:255|string',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country_code' => 'required|string|alpha|max:2',
            'postal_code' => 'required|string|max:5',
            'type' => ['required',Rule::in(['shipping','billing'])],
        ]);

        try {
            app(UserService::class)->updateAddress($request->all(), auth()->user());
        } catch(\Exception $e)
        {
            return back()->with(['message' => $e->getMessage(), 'message_type' => 'danger']);
        }

        return back()->with(['message' => 'Successfully updated ' . $request->type . ' address', 'message_type' => 'success']);

    }

    public function profilePut(Request $request){
        $request->validate([
            'name' => 'required|string',
            'email' => 'sometimes|required|email|unique:users,email,' . Auth::user()->id,
            'username' => 'sometimes|required|unique:users,username,' . Auth::user()->id,
            'rfc' => ['sometimes', 'required', new Rfc()],
            'company' => 'sometimes|required|string'
        ]);

    	$authed_user = auth()->user();

    	$authed_user->name = $request->name;
    	$authed_user->email = $request->email;
        if($request->avatar){
    	   $authed_user->avatar = $this->saveAvatar($request->avatar, $authed_user->username);
        }

        /**
         * Vemos los campos dinamicos permitidos en el archivo de configuración
         * wave.php y revisamos si vienen en el Request, en caso de que esten
         * y el usuario tenga definidos accesors (getFieldAttribute()), isset
         * marcara true, en caso de que no, marcara false, entonces podremos
         * guardar el valor y removerlo de nuestro request para evitar volver a
         * guardarlo de forma dinamica.
         */
        foreach(config('wave.profile_fields') as $key) {
            if (isset($request->{$key}) && isset($authed_user->{$key})) {
                $authed_user->{$key} = $request->{$key};
                $request->request->remove($key);
            }
        }
    	$authed_user->save();

        //TODO: Quitar eest code ugly
    	foreach(config('wave.profile_fields') as $key){
    		if(isset($request->{$key})){
	    		$type = $key . '_type__wave_keyvalue';
	    		if($request->{$type} == 'checkbox'){
	                if(!isset($request->{$key})){
	                    $request->request->add([$key => null]);
	                }
	            }

	            $row = (object)['field' => $key, 'type' => $request->{$type}, 'details' => ''];
	            $value = $this->getContentBasedOnType($request, 'themes', $row);

	    		if(!is_null($authed_user->keyValue($key))){
	    			$keyValue = KeyValue::where('keyvalue_id', '=', $authed_user->id)->where('keyvalue_type', '=', 'users')->where('key', '=', $key)->first();
	    			$keyValue->value = $value;
	    			$keyValue->type = $request->{$type};
	    			$keyValue->save();
	    		} else {
	    			KeyValue::create(['type' => $request->{$type}, 'keyvalue_id' => $authed_user->id, 'keyvalue_type' => 'users', 'key' => $key, 'value' => $value]);
	    		}
	    	}
    	}

    	return back()->with(['message' => 'Successfully updated user profile', 'message_type' => 'success']);
    }

    public function securityPut(Request $request){

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|confirmed|min:'.config('wave.auth.min_password_length'),
        ]);

        if ($validator->fails()) {
            return back()->with(['message' => $validator->errors()->first(), 'message_type' => 'danger']);
        }

        if (! Hash::check($request->current_password, $request->user()->password)) {
            return back()->with(['message' => 'Incorrect current password entered.', 'message_type' => 'danger']);
        }

        auth()->user()->forceFill([
            'password' => bcrypt($request->password)
        ])->save();

        return back()->with(['message' => 'Successfully updated your password.', 'message_type' => 'success']);
    }

    public function paymentPost(Request $request){
        $subscribed = auth()->user()->updateCard($request->paymentMethod);
    }

    public function apiPost(Request $request){
        $request->validate([
            'key_name' => 'required'
        ]);

        $apiKey = auth()->user()->createApiKey(str_slug($request->key_name));
        if(isset($apiKey->id)){
            return back()->with(['message' => 'Successfully created new API Key', 'message_type' => 'success']);
        } else {
            return back()->with(['message' => 'Error Creating API Key, please make sure you entered a valid name.', 'message_type' => 'danger']);
        }
    }

    public function apiPut(Request $request, $id = null){
        if(is_null($id)){
            $id = $request->id;
        }
        $apiKey = ApiKey::findOrFail($id);
        if($apiKey->user_id != auth()->user()->id){
            return back()->with(['message' => 'Canot update key name. Invalid User', 'message_type' => 'danger']);
        }
        $apiKey->name = str_slug($request->key_name);
        $apiKey->save();
        return back()->with(['message' => 'Successfully update API Key name.', 'message_type' => 'success']);
    }

    public function apiDelete(Request $request, $id = null){
        if(is_null($id)){
            $id = $request->id;
        }
        $apiKey = ApiKey::findOrFail($id);
        if($apiKey->user_id != auth()->user()->id){
            return back()->with(['message' => 'Canot delete Key. Invalid User', 'message_type' => 'danger']);
        }
        $apiKey->delete();
        return back()->with(['message' => 'Successfully Deleted API Key', 'message_type' => 'success']);
    }

    private function saveAvatar($avatar, $filename){
    	$path = 'avatars/' . $filename . '.png';
    	Storage::disk(config('voyager.storage.disk'))->put($path, file_get_contents($avatar));
    	return $path;
    }

    public function invoice(Request $request, $invoiceId) {
        return $request->user()->downloadInvoice($invoiceId, [
            'vendor'  => setting('site.title', 'Wave'),
            'product' => ucfirst(auth()->user()->role->name) . ' Subscription Plan',
        ]);
    }
}
