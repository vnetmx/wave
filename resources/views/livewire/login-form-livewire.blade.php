<div class="px-8 rounded-xl">
    @if($errors->has('login'))
        <h1 class="font-medium text-xl mt-3 text-center text-red-500">{{ $errors->first('login') }}</h1>
    @endif
    <form wire:submit.prevent="login" class="mt-6">
        <div class="my-5 text-sm">
            <label for="username" class="block text-black">{{__('E-Mail')}}</label>
            <input wire:model.lazy="user" type="text" autofocus id="email"
                   class="rounded-sm px-4 py-3 mt-3 focus:outline-none bg-gray-100 w-full"
                   placeholder="{{__('E-Mail')}}"/>
            @if($errors->has('email'))
                <span class="text-red-500 mt-1">{{ $errors->first('email') }}</span>
            @endif
        </div>
        <div class="my-5 text-sm">
            <label for="password" class="block text-black">{{__('Password')}}</label>
            <input wire:model.lazy="password" type="password" id="password"
                   class="rounded-sm px-4 py-3 mt-3 focus:outline-none bg-gray-100 w-full"
                   placeholder="{{__('Password')}}"/>
            @if($errors->has('password'))
                <span class="text-red-500 mt-1">{{ $errors->first('password') }}</span>
            @endif

            <div class="flex justify-end mt-2 text-xs text-gray-600">
                <a href="../../pages/auth/forget_password.html hover:text-black">{{__('Forget Password?')}}</a>
            </div>
        </div>
        <button type="submit"
                class="block text-center text-white bg-gray-800 p-3 duration-300 rounded-sm hover:bg-black w-full">{{__('Login')}}</button>
    </form>

    <p class="mt-12 text-xs text-center font-light text-gray-400"> ¿No tiene una cuenta?
        {{-- <button wire:click="$set('signup', true)">Registrese</button> --}}
        <button wire:click="$emitUp('isSignup', true)">Registrese</button>
    </p>
</div>
