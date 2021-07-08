<div id="openpay-payment-livewire">
    <!-- Title -->
    <span class="font-bold block text-2xl mb-3 text-center">
        @auth Pago con tarjeta @endauth
        @guest
            @if($signup)
                Registro
                @else
                Ingresar
                @endif
        @endguest
    </span>
    @auth
        <div class="flex flex-wrap">
            <div class="w-full md:w-1/3 max-h-full p-4 bg-gray-50 border-2 border-gray-200">
                <h1 class="font-bold block text-xl mt-4 mb-6 text-center">
                    <span>Detalles de la Compra</span>
                </h1>
                <p class="font-bold text-gray-900 block my-1">CLIENTE:</p>
                <p class="block text-sm">{{auth()->user()->name}} {{auth()->user()->last_name ?? ''}}</p>
                <p class="block text-sm">{{auth()->user()->email}}</p>
                <p class="font-bold text-gray-900 block mt-4 mb-1">CONCEPTO:</p>
                @if($product->exists)
                    <ul class="flex flex-col space-y-2.5">
                        @foreach($product->plan_features as $feature)
                            <li class="relative text-xs">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-3 text-green-500 fill-current"
                                         xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M0 11l2-2 5 5L18 3l2 2L7 18z"></path>
                                    </svg>

                                    <span>
                                        {{ $feature }}
                                    </span>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <p class="block mt-4 font-bold text-gray-900 mb-1">IMPORTE:</p>
                <div class="block text-center">
                    <p class="font-bold text-xl text-gray-900">$ {{$product->price ?? ''}} MXN</p>
                </div>
                <div class="flex flex-col md:flex-row mt-3 items-center md:items-start space-y-2 md:space-y-0">
                    <div class="flex flex-col md:flex-row md:space-x-1 space-y-2 md:space-y-0 items-center">
                        <img src="{{ asset('themes/' . $theme->folder . '/images/security.png') }}"
                             class="w-7 md:w-auto" alt="256 bits">
                        <p class="text-xs font-light text-center">Tus pagos se realizan de forma segura con encriptación
                            de 256 bits.</p>
                    </div>
                    <div class="pl-2 md:border-l md:border-gray-200">
                        <img src="{{ asset('themes/' . $theme->folder . '/images/openpay.png') }}"
                             class="w-12 md:w-auto" alt="OpenPay, S.A. de C.V.">
                    </div>
                </div>
            </div>
            <div class="w-full md:w-2/3 max-h-full pl-4 pb-4 pr-4">
                <form class="flex flex-col max-w-xl" id="openpay-form" wire:submit.prevent="charge">
                    <div>
                        @error('request')
                        <div class="p-3 mb-2 rounded border border-red-800 text-red-800 bg-red-200 font-red-700"><p>{{__($message)}}</p></div>
                        @enderror
                        <!-- Tipos de Tarjeta Aceptadas -->
                        <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 items-center md:items-start">
                            <div class="w-1/2 flex md:flex-col">
                                <h3 class="text-xs hidden md:block font-bold">Tarjetas de Crédito</h3>
                                <div>
                                    <img src="{{ asset('themes/' . $theme->folder . '/images/cards1.png') }}" alt="">
                                </div>
                            </div>
                            <div class="w-1/2 flex md:flex-col">
                                <h3 class="text-xs hidden md:block font-bold">Tarjetas de Débito</h3>
                                <div>
                                    <img src="{{ asset('themes/' . $theme->folder . '/images/cards3.png') }}" alt="">
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- Nombre Tarjeta -->
                    <div>
                        <label class="text-xs text-gray-600 font-light">Nombre</label>
                        <input type='text'
                               required=""
                               data-openpay-card="holder_name"
                               wire:model="name"
                               autocomplete="off"
                               placeholder="Como aparece en la tarjeta"
                               class="w-full my-2 px-4 py-2 border rounded-lg text-gray-700 focus:outline-none focus:border-blue-500"/>
                    </div>
                    <!-- Número de Tarjeta -->
                    <div>
                        <label class="text-xs text-gray-600 font-light">Número de tarjeta</label>
                        <input type='text'
                               required=""
                               data-openpay-card="card_number"
                               autocomplete="off"
                               placeholder="XXXX XXXX XXXX XXXX"
                               maxlength="16"
                               pattern="[0-9]{16}"
                               class="w-full my-2 px-4 py-2 border rounded-lg text-gray-700 focus:outline-none focus:border-blue-500"/>
                    </div>
                    <div class="flex space-x-1 flex-col md:flex-row">
                        <div class="block md:inline-block md:w-1/2">
                            <label class="text-xs text-gray-600 font-light">Fecha de expiración</label>
                            <select
                                required=""
                                data-openpay-card="expiration_month"
                                class="w-full my-2 px-4 py-2 border rounded-lg text-gray-700 focus:outline-none focus:border-blue-500">
                                <option value="01">01 - Enero</option>
                                <option value="02">02 - Febrero</option>
                                <option value="03">03 - Marzo</option>
                                <option value="04">04 - Abril</option>
                                <option value="05">05 - Mayo</option>
                                <option value="06">06 - Junio</option>
                                <option value="07">07 - Julio</option>
                                <option value="08">08 - Agosto</option>
                                <option value="09">09 - Septiembre</option>
                                <option value="10">10 - Octubre</option>
                                <option value="11">11 - Noviembre</option>
                                <option value="12">12 - Diciembre</option>
                            </select>
                        </div>
                        <div class="block md:inline-block md:w-1/2">
                            <label class="invisible md:visible text-xs text-gray-600 font-light">&nbsp;</label>
                            <select
                                required=""
                                data-openpay-card="expiration_year"
                                class="w-full my-2 px-4 py-2 border rounded-lg text-gray-700 focus:outline-none focus:border-blue-500">
                                <option value="21">2021</option>
                                <option value="22">2022</option>
                                <option value="23">2023</option>
                                <option value="24">2024</option>
                                <option value="25">2025</option>
                                <option value="26">2026</option>
                                <option value="27">2027</option>
                                <option value="28">2028</option>
                                <option value="29">2029</option>
                            </select>
                        </div>
                    </div>
                    <!-- Código de Seguridad -->
                    <div class="flex flex-col md:flex-row space-x-1 items-center">
                        <div class="block md:inline-block mt-2 md:w-1/2">
                            <label class="text-xs text-gray-600 font-light">Código de seguridad</label>
                            <input type='password'
                                   required=""
                                   data-openpay-card="cvv2"
                                   autocomplete="off"
                                   placeholder="***"
                                   maxlength="3"
                                   pattern="[0-9]{3}"
                                   class="w-full my-2 px-4 py-2 border rounded-lg text-gray-700 focus:outline-none focus:border-blue-500"/>
                        </div>
                        <!-- Código de seguridad Imagén -->
                        <div class="mt-2 w-1/2 flex flex-col items-center">
                            <div>
                                <img src="{{ asset('themes/' . $theme->folder . '/images/cvv.png') }}"
                                     alt="Código de Seguridad">
                            </div>
                            <div class="flex space-x-4 justify-between">
                                <p class="text-xs text-center">Reverso<br> Vista y MasteCard</p>
                                <p class="text-xs text-center">Parte Frontal <br>American Express</p>
                            </div>
                        </div>
                    </div>
                    <!-- Dirección
                    <p class="mt-4 text-gray-800 font-medium">Address</p>
                    <div class="inline-block mt-2 w-2/3">
                        <label class="hidden block text-sm text-gray-600" for="line1">Street</label>
                        <input class="w-full px-2 py-2 text-gray-700 bg-gray-200 rounded" id="line1"
                               data-openpay-card-address="line1" type="text" required="" placeholder="Street"
                               aria-label="Street">
                    </div>
                    <div class="inline-block mt-2 w-1/3">
                        <label class="hidden block text-sm text-gray-600" for="line2">Number</label>
                        <input class="w-full px-2 py-2 text-gray-700 bg-gray-200 rounded" id="line2"
                               data-openpay-card-address="line2" type="text" required="" placeholder="Number"
                               aria-label="Number">
                    </div>
                    <div class="mt-2">
                        <label class="hidden text-sm block text-gray-600" for="line3">References</label>
                        <input class="w-full px-2 py-2 text-gray-700 bg-gray-200 rounded" id="line3"
                               data-openpay-card-address="line3" type="text" required="" placeholder="References"
                               aria-label="References">
                    </div>
                    <div class="inline-block mt-2 w-1/3 pr-1">
                        <label class="hidden block text-sm text-gray-600" for="city">City</label>
                        <input class="w-full px-2 py-2 text-gray-700 bg-gray-200 rounded" id="city"
                               data-openpay-card-address="city" type="text" required="" placeholder="City"
                               aria-label="City">
                    </div>
                    <div class="inline-block mt-2 -mx-1 pl-1 w-1/3">
                        <label class="hidden block text-sm text-gray-600" for="state">State</label>
                        <input class="w-full px-2 py-2 text-gray-700 bg-gray-200 rounded" id="state"
                               data-openpay-card-address="state" type="text" required="" placeholder="State"
                               aria-label="State">
                    </div>
                    <div class="inline-block mt-2 -mx-1 pl-2 w-1/3">
                        <label class="hidden block text-sm text-gray-600" for="postal_code">Zip</label>
                        <input class="w-full px-2 py-2 text-gray-700 bg-gray-200 rounded" id="postal_code"
                               data-openpay-card-address="postal_code" type="text" required="" placeholder="Zip Code"
                               aria-label="Zip">
                    </div>
                    <input data-openpay-card-address="country_code" size="3" type="hidden" value="MX">
                    -->
                    <button type="submit"
                            class="
                            mt-2 px-4 py-2
                            text-sm text-white focus:text-indigo font-bold
                            bg-gradient-to-r from-wave-600 to-indigo-500 hover:from-wave-500 hover:to-indigo-400
                            rounded-xl border border-gray-200 transition-colors duration-150
                            ease-linear focus:outline-none focus:ring-0
                            ">
                        Realizar pago
                    </button>
                </form>
            </div>
        </div>
    @endauth
    @guest
        @if(!$signup)
            <div class="bg-white w-full m-auto">
                <livewire:login-form-livewire />
            </div>
        @else
            <div class="bg-white w-full m-auto">
                <livewire:register-form-livewire />
            </div>
            {{--<button wire:click="$set('signup', false)">Iniciar Sesión</button>--}}
        @endif
@endguest
<!-- Buttons -->
    <div class="text-right space-x-5 mt-5">
        <button @click="open = !open"
                class="px-4 py-2 text-sm bg-red-600 rounded-xl border transition-colors duration-150 ease-linear
                       border-red-400 text-gray-100 focus:outline-none focus:ring-0 font-bold hover:bg-red-500
                       focus:bg-red-500 focus:text-indigo">
            Cancelar
        </button>
    </div>

    <!-- Loader -->
    <div wire:loading
         class="fixed top-0 left-0 right-0 bottom-0 w-full h-screen z-50 overflow-hidden
                bg-gray-500 opacity-75 flex flex-col items-center justify-center">
        <div class="flex items-center m-auto justify-center mt-5 pt-10">
            <svg class="animate-spin h-12 w-12 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        <h2 class="text-center text-white text-xl font-semibold mt-5">Procesando</h2>
        <p class="text-center text-white mt-2">Esto puedo tardar un momento.</p>
    </div>
</div>
