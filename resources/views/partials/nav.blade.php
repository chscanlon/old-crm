<nav class="bg-blue-900 shadow mb-8 py-6">
    <div class="container mx-auto px-6 md:px-0">
        <div class="flex text-left">
            <div class="ml-6">
                <a href="{{ url('/dashboard') }}" class="no-underline hover:underline text-gray-300 text-sm p-3">
                    Dashboard
                </a>
                <a href="{{ url('/timelyCustomerImports') }}"
                    class="no-underline hover:underline text-gray-300 text-sm p-3">
                    Customer Import
                </a>
                <a href="{{ url('/timelyScheduleImports') }}"
                    class="no-underline hover:underline text-gray-300 text-sm p-3">
                    Schedule Import
                </a>
                <a href="{{ url('/customers') }}" class="no-underline hover:underline text-gray-300 text-sm p-3">
                    Customers
                </a>
            </div>
            <div class="flex-1 text-right">
                @guest
                <a class="no-underline hover:underline text-gray-300 text-sm p-3"
                    href="{{ route('login') }}">{{ __('Login') }}</a>
                @if (Route::has('register'))
                <a class="no-underline hover:underline text-gray-300 text-sm p-3"
                    href="{{ route('register') }}">{{ __('Register') }}</a>
                @endif
                @else
                <span class="text-gray-300 text-sm pr-4">{{ Auth::user()->name }}</span>

                <a href="{{ route('logout') }}" class="no-underline hover:underline text-gray-300 text-sm p-3" onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    {{ csrf_field() }}
                </form>
                @endguest
            </div>
        </div>
    </div>
</nav>