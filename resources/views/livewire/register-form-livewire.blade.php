<div class="px-8 rounded-xl">
        @if($errors->has('login'))
            <h1 class="font-medium text-xl mt-3 text-center text-red-500">{{ $errors->first('login') }}</h1>
        @endif
        <form wire:submit.prevent="register" class="mt-6">
            <div class="flex flex-col md:flex-row text-sm md:my-5 md:space-x-2">
                <div class="w-full md:w-1/2">
                    <label for="name" class="block text-black">{{__('Name')}}</label>
                    <input wire:model.lazy="name" type="text" autofocus id="name"
                           class="rounded-sm px-4 py-3 mt-3 focus:outline-none bg-gray-100 w-full"
                           placeholder="{{__('Name')}}"
                           required
                    />
                    @error('name')
                     <span class="text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div class="w-full md:w-1/2">
                    <label for="last_name" class="block text-black">{{__('Last Name')}}</label>
                    <input wire:model.lazy="last_name" type="text" autofocus id="last_name"
                           class="rounded-sm px-4 py-3 mt-3 focus:outline-none bg-gray-100 w-full"
                           placeholder="{{__('Last Name')}}"
                           required
                    />
                    @error('last_name')
                    <span class="text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="flex flex-col md:flex-row text-sm md:my-5 md:space-x-2">
                <div class="w-full md:w-1/2">
                    <label for="email" class="block text-black">{{__('E-Mail')}}</label>
                    <input wire:model.lazy="email" type="text" autofocus id="email"
                           class="rounded-sm px-4 py-3 mt-3 focus:outline-none bg-gray-100 w-full"
                           placeholder="{{__('E-Mail')}}"
                           required
                    />
                    @error('email')
                    <span class="text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div class="w-full md:w-1/2">
                    <label for="phone" class="block text-black">{{__('Phone')}}</label>
                    <input wire:model.lazy="phone" type="text" autofocus id="phone"
                           class="rounded-sm px-4 py-3 mt-3 focus:outline-none bg-gray-100 w-full"
                           placeholder="{{__('Phone')}}"
                           required
                    />
                    @error('phone')
                    <span class="text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="flex flex-col md:flex-row text-sm md:my-5 md:space-x-2">
                @if(setting('auth.username_in_registration') == 'yes')
                <div class="w-full
                            md:w-1/3
                    ">
                    <label for="username" class="block text-black">{{__('Username')}}</label>
                    <input wire:model.lazy="username" type="text" id="username"
                           class="rounded-sm px-4 py-3 mt-3 focus:outline-none bg-gray-100 w-full"
                           placeholder="{{__('Username')}}"
                           required
                    />
                    @error('username')
                    <span class="text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>
                @endif
                <div class="w-full
                        md:{{setting('auth.username_in_registration') == 'yes' ? 'w-1/3' : 'w-1/2'}}
                    ">
                    <label for="password" class="block text-black">{{__('Password')}}</label>
                    <input wire:model.lazy="password" type="password" id="password"
                           class="rounded-sm px-4 py-3 mt-3 focus:outline-none bg-gray-100 w-full"
                           placeholder="{{__('Password')}}"
                           required
                    />
                    @error('password')
                        <span class="text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div class="w-full
                        md:{{setting('auth.username_in_registration') == 'yes' ? 'w-1/3' : 'w-1/2'}}
                    ">
                    <label for="password" class="block text-black">{{__('Password Confirmation')}}</label>
                    <input wire:model.lazy="password_confirmation" type="password" id="password_confirmation"
                           class="rounded-sm px-4 py-3 mt-3 focus:outline-none bg-gray-100 w-full"
                           placeholder="{{__('Password Confirmation')}}"
                           required
                    />
                    @error('password_confirmation')
                        <span class="text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <button type="submit"
                    class="block text-center text-white bg-gray-800 p-3 duration-300 rounded-sm hover:bg-black w-full">{{__('Register')}}</button>
        </form>

        <p class="mt-12 text-sm text-center font-light text-gray-400"> ¿Ya tiene cuenta?
            {{-- <button wire:click="$set('signup', true)">Registrese</button> --}}
            <button wire:click="$emitUp('isSignup', false)">Ingresar</button>
        </p>
</div>
