@if ($errors->any())
    @foreach ($errors->all() as $error)
        <div class="px-2 mx-auto mt-4 max-w-4xl">
            <div class="relative py-3 pl-4 pr-10 leading-normal text-red-700 bg-red-100 rounded-lg" role="alert">
                <p>{{$error}}</p>
            </div>
        </div>
    @endforeach
@endif
<form action="{{ route('wave.settings.address-update.put') }}" method="POST" enctype="multipart/form-data">
    <div class="relative flex flex-col px-10 py-8">
        <input type="hidden" name="type" value="{{$section}}">
        <div class="mt-5">
            <label for="line1" class="block text-sm font-medium leading-5 text-gray-700">{{__('Line1')}}</label>
            <div class="mt-1 rounded-md shadow-sm">
                <input id="line1" type="text" name="line1" placeholder="{{__('Line1')}}"
                       value="{{ Auth::user()->{$section}->line1 ?? ''}}"
                       class="w-full form-input"
                       maxlength="200"
                       required
                >
            </div>
        </div>
        <div class=mt-5>
            <label for="line2" class="block text-sm font-medium leading-5 text-gray-700">{{__('Line2')}}</label>
            <div class="mt-1 rounded-md shadow-sm">
                <input id="line2" type="text" name="line2" placeholder="{{__('Line2')}}"
                       value="{{ Auth::user()->{$section}->line2 ?? ''}}"
                       class="w-full form-input"
                       maxlength="200"
                       required
                >
            </div>
        </div>
        <div class="mt-5">
            <label for="line3" class="block text-sm font-medium leading-5 text-gray-700">{{__('Line3')}}</label>
            <div class="mt-1 rounded-md shadow-sm">
                <input id="line3" type="text" name="line3" placeholder="{{__('Line3')}}"
                       value="{{ Auth::user()->{$section}->line3 ?? ''}}"
                       class="w-full form-input"
                       maxlength="200"
                >
            </div>
        </div>

        <div class="mt-5 flex flex-col md:flex-row space-x-2">
            <div class="w-1/2">
                <label for="city" class="block text-sm font-medium leading-5 text-gray-700">{{__('City')}}</label>
                <div class="mt-1 rounded-md shadow-sm">
                    <input id="city" type="text" name="city" placeholder="{{__('City')}}"
                           value="{{ Auth::user()->{$section}->city ?? ''}}"
                           class="w-full form-input"
                           maxlength="200"
                           required
                    >
                </div>
            </div>
            <div class="w-1/2">
                <label for="state" class="block text-sm font-medium leading-5 text-gray-700">{{__('State')}}</label>
                <div class="mt-1 rounded-md shadow-sm">
                    <input id="state" type="text" name="state" placeholder="{{__('State')}}"
                           value="{{ Auth::user()->{$section}->state ?? ''}}"
                           class="w-full form-input"
                           maxlength="200"
                           required
                    >
                </div>
            </div>
        </div>

        <div class="mt-5 flex flex-col md:flex-row space-x-2">
            <div class="w-1/2">
                <label for="country_code"
                       class="block text-sm font-medium leading-5 text-gray-700">{{__('Country')}}</label>
                <div class="mt-1 rounded-md shadow-sm">
                    <select id="country_code" name="country_code" class="w-full form-input">
                        <option value="MX" selected>México</option>
                    </select>
                    {{--
                    <input id="country_code" type="text" name="country_code" placeholder="{{__('Country')}}"
                           value="{{ Auth::user()->{$section}->country_code ?? ''}}"
                           class="w-full form-input"
                           required
                    >
                    --}}
                </div>
            </div>
            <div class="w-1/2">
                <label for="postal_code"
                       class="block text-sm font-medium leading-5 text-gray-700">{{__('Postal Code')}}</label>
                <div class="mt-1 rounded-md shadow-sm">
                    <input id="postal_code" type="text" name="postal_code" placeholder="{{__('Postal Code')}}"
                           value="{{ Auth::user()->{$section}->postal_code ?? ''}}"
                           class="w-full form-input"
                           max="5"
                           required
                    >
                </div>
            </div>
        </div>

        <input type="hidden" name="_method" value="PUT">
        {{ csrf_field() }}
        <div class="flex justify-end w-full mt-2">
            <button
                class="flex self-end justify-center w-auto px-4 py-2 mt-5 text-sm font-medium text-white transition duration-150 ease-in-out border border-transparent rounded-md bg-wave-600 hover:bg-wave-500 focus:outline-none focus:border-wave-700 focus:shadow-outline-wave active:bg-wave-700"
                dusk="update-profile-button">{{__('Update')}}</button>
        </div>

    </div>
</form>
