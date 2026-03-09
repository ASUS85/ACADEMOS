<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <!-- En-tête avec icônes -->
            <div class="flex justify-between items-center mb-8">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold">
                        <span alt="Logo CONSENT" class="text-xs">C</span>
                    </div>
                    <span class="text-sm font-medium text-gray-600">CONSENT</span>
                </div>
                <div class="flex items-center">
                    <span class="text-lg font-semibold text-gray-800">AcaDemo</span>
                    <div class="ml-2 w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white font-bold">
                        <span alt="Logo AcaDemo">A</span>
                    </div>
                </div>
            </div>

            <!-- Message de session -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Adresse mail -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Adresse mail
                    </label>
                    <input id="email" type="email" name="email" :value="old('email')" required autofocus
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Entrer votre adresse mail">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Bouton s'inscrire -->
                <div class="mb-6">
                   <a href="{{ route('register') }}"> <button type="button" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200 font-medium" >
                         s'inscrire
                    </button>
                    </a>
                </div>

                <!-- Mot de passe -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Mot de passe
                    </label>
                    <input id="password" type="password" name="password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Entrer votre mot de passe">
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Bouton se connecter -->
                <div class="mb-6">
                    <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition duration-200 font-medium">
                        se connecter
                    </button>
                </div>

                <!-- Remember Me et Forgot Password (conservés du layout original) -->
                <div class="flex items-center justify-between mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                        <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-gray-600 hover:text-gray-900 underline" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
