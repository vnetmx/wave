{{--
MODULO FRONTEND DE PAYMENT

Existen casos de modulos de Payment como Paddle, que el mismo JS hace todo
el procedimiento de mostrar el modal y guiar al usuario por un proceso de
inscripción y pago el producto o suscripción, que son los más sencillos por
así decirlos, donde no existen tantas variables.

Hay casos como OpenPay donde tenemos nosotros que mostrar un formulario de
pago y guiar al usuario por nosotros al proceso de compra, en estos casos
necesitaremos mostrar un Modal donde tendremos nuestro formulario o la guia
paso a paso que llevaremos. Hasta ahora, lo que hemos hecho es crear un
Modal basado en Alpine el cual usa en el x-data una función que podemos
encontrar en app.js, la cual nos cargara los valores iniciales del componente
de Alpine y en donde tendremos dos casos, el usar x-on o @ en el modal, donde
haremos referencia al 'evento' que escucharemos en el componente AlpineModalPlans:

@alpine-modal-plans.window="funcion($event.detail)"

o bien, podemos usar x-init="init()" donde llamara a la función AlpineModalVanilla(event)
que debera estar definida en éste Blade en Vanilla JS entre los tags de <script>.


<button x-data @click="$dispatch('alpine-modal-plans', {plan_id:'{{ $plan->plan_id }}'})">
    Get Started
</button>

comunicarnos con el componente AlpineModalPlans o bi
--}}


<div x-data="AlpineModalPlans()" x-init="init()" @alpine-modal-plans.window="showMsg($event.detail.plan_id)">
    <div x-show="open"  class="flex justify-center h-screen items-center bg-gray-200 antialiased">
        <div class="flex flex-col w-11/12 sm:w-5/6 lg:w-1/2 max-w-2xl mx-auto rounded-lg border border-gray-300 shadow-xl">
            hOLA
        </div>
    </div>
</div>
    {{--
    <!-- This example requires Tailwind CSS v2.0+ -->
    <div x-show="open" class="fixed z-30 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!--
              Background overlay, show/hide based on modal state.

              Entering: "ease-out duration-300"
                From: "opacity-0"
                To: "opacity-100"
              Leaving: "ease-in duration-200"
                From: "opacity-100"
                To: "opacity-0"
            -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <!-- This element is to trick the browser into centering the modal contents. -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!--
              Modal panel, show/hide based on modal state.

              Entering: "ease-out duration-300"
                From: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                To: "opacity-100 translate-y-0 sm:scale-100"
              Leaving: "ease-in duration-200"
                From: "opacity-100 translate-y-0 sm:scale-100"
                To: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            -->
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                style="max-width: 56rem;">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                @auth
                                    Realizar pago
                                @endauth
                                @guest
                                    Iniciar sesión
                                @endguest
                            </h3>
                            <div class="mt-2">
                                <livewire:payment-prueba />
                            </div>
                        </div>
                    </div>
                </div>

                {{--
                <!-- Button Bar -->
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Deactivate
                    </button>
                    <button type="button"
                            @click = "open = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
                --}
            </div>
        </div>
    </div>
</div>
--}}
<script>

    OpenPay.setId('{{ config('openpay.merchant_id') }}');
    OpenPay.setApiKey('{{ config('openpay.public_api_key') }}');

    @if(config('openpay.production'))
    OpenPay.setSandboxMode(false);
    @else
    OpenPay.setSandboxMode(true);
    @endif

    function AlpineModalVanilla(event)
    {
        console.log(event);
    }

    let checkoutBtns = document.getElementsByClassName("checkout");
    /*
    for (var i = 0; i < checkoutBtns.length; i++) {
        checkoutBtns[i].addEventListener('click', function () {
            let event = new CustomEvent("alpine-modal", {
                open: true
            });
            window.dispatchEvent(event);
            //waveCheckout(this.dataset.plan)
        }, false);
    }*/

    let updateBtns = document.getElementsByClassName("checkout-update");
    for (var i = 0; i < updateBtns.length; i++) {
        updateBtns[i].addEventListener('click', waveUpdate, false);
    }

    let cancelBtns = document.getElementsByClassName("checkout-cancel");
    for (var i = 0; i < cancelBtns.length; i++) {
        cancelBtns[i].addEventListener('click', waveCancel, false);
    }


    function waveCheckout(plan_id) {
        let product = parseInt(plan_id);
        let OpenPayForm = document.getElementById('openpay-form');
        OpenPay.token.extractFormAndCreate(OpenPayForm, "checkoutComplete", "checkoutComplete", '@if(!auth()->guest()){{ auth()->user()->email }}@endif');
    }

    function waveUpdate() {
        Paddle.Checkout.open({
            override: this.dataset.url,
            successCallback: "checkoutUpdate",
        });
    }

    function waveCancel() {
        Paddle.Checkout.open({
            override: this.dataset.url,
            successCallback: "checkoutCancel",
        });
    }
</script>
