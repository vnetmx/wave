<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserCreationRequest;
use App\Rules\Rfc;
use App\Services\OpenpayService;
use App\Services\UserService;
use App\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;

/**
 * Haremos un poco menos dinamico, ya que sabemos que estamos trabajando
 * con Users y no tanto el metodo dinamico de Voyager
 *
 * Class VoyagerUserController
 * @package App\Http\Controllers\Admin
 */
class VoyagerUserController extends VoyagerBaseController
{
    public static $slug = 'users';
    protected $dataType;

    public function __construct()
    {
        $this->dataType = Voyager::model('DataType')->where('slug', '=', static::$slug)->first();
    }

    /**
     * Metodo 'destroy' con codigo adicional para eliminar
     * el usuario de openpay
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        $ids = [];
        if (empty($id)) {
            // Bulk delete, get IDs from POST
            $ids = explode(',', $request->ids);
        } else {
            // Single item delete, get ID from URL
            $ids[] = $id;
        }
        foreach ($ids as $id) {
            app(OpenpayService::class)->deleteCustomer(User::findOrFail($id));
        }

        return parent::destroy($request, $id);
    }

    /**
     * Updates user
     *
     * @param Request $request
     * @param User $id
     * @return JsonResponse|RedirectResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(Request $request, $id)
    {
        // Get the User
        $user = app(UserService::class)->find($id);
        // Authorize User
        $this->authorize('edit', $user);
        // Validate Data
        $validated = $this->doValidation($request, true, ['email', 'username']);
        // Update User with Validated Data
        app(UserService::class)->update($user, $validated);
        // Dispatch Event Data Updated
        event(new BreadDataUpdated($this->dataType, $validated));

        // Go Away
        return $this->goBack($user);
    }

    public function goBack($user = null)
    {
        if (!request()->has('_tagging')) {
            if (auth()->user()->can('browse', $user)) {
                $redirect = redirect()->route("voyager.{$this->dataType->slug}.index");
            } else {
                $redirect = redirect()->back();
            }

            return $redirect->with([
                'message' => __('voyager::generic.successfully_added_new') . " {$this->dataType->getTranslatedAttribute('display_name_singular')}",
                'alert-type' => 'success',
            ]);
        } else {
            return response()->json(['success' => true, 'data' => $user]);
        }
    }

    /**
     * Creates a new user
     *
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        // Autorizamos
        $this->authorize('add', app($this->dataType->model_name));
        // Validación.
        $validated = $this->doValidation($request);
        // Creando el usuario
        $user = app(UserService::class)->create($validated);
        // Creando direcciones
        foreach (['shipping', 'billing'] as $addressType) {
            if(isset($validated[$addressType])) {
                app(UserService::class)->updateAddress($validated[$addressType], $user);
            }
        }
        // Dispatch Event
        event(new BreadDataAdded($this->dataType, $user));
        // Go Away
        return $this->goBack($user);
    }


    /**
     * Validates Request() data and discard null's
     *
     * @param Request $request
     * @param false $update
     * @param array $except
     * @return array
     * @throws ValidationException
     */
    protected function doValidation(Request $request, bool $update = false, array $except = []): array
    {
        $input = $request->except($except);
        return Validator::make(collect($input)
            ->map(function ($value, $key) {
                if (is_array($value)) {
                    return collect($value)->filter()->all();
                }
                return $value;
            })->filter()
            ->all(),
            $this->rules($update))
            ->validate();
    }

    /**
     * @param false $update
     * @return array
     */
    protected function rules(bool $update = false): array
    {
        $sometimes = $update ? '|sometimes' : '';
        return [
            'name' => 'required|string|max:255',
            'last_name' => 'required|sometimes|string|max:255',
            'phone' => 'required|sometimes|string|max:20',
            'email' => 'required|string|email|max:255|unique:users' . $sometimes,
            'username' => 'required|sometimes|string|max:20|unique:users' . $sometimes,
            'password' => 'required|string|min:8' . $sometimes,
            'company' => 'required|sometimes|string|max:255',
            'rfc' => ['required', 'sometimes', new Rfc()],
            'shipping.type' => ['required', Rule::in(['shipping','billing'])],
            'shipping.line1' => 'required|sometimes|string|max:255',
            'shipping.line2' => 'required|sometimes|string|max:255',
            'shipping.line3' => 'required|sometimes|nullable|string|max:255',
            'shipping.city' => 'required|sometimes|string|max:255',
            'shipping.state' => 'required|sometimes|string|max:255',
            'shipping.country_code' => 'required|sometimes|string|max:2',
            'shipping.postal_code' => 'required|sometimes|string|max:5',
            'billing.line1' => 'required|sometimes|string|max:255',
            'billing.line2' => 'required|sometimes|string|max:255',
            'billing.line3' => 'required|sometimes|nullable|string|max:255',
            'billing.city' => 'required|sometimes|string|max:255',
            'billing.state' => 'required|sometimes|string|max:255',
            'billing.country_code' => 'required|sometimes|string|max:2',
            'billing.postal_code' => 'required|sometimes|string|max:5',
            'billing.type' => ['required', Rule::in(['shipping','billing'])],

        ];
    }
}
