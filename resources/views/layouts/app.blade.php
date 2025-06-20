<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'AL-INSAF'))</title>
    
    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>الإنصاف</text></svg>">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        body {
            font-family: 'Inter', 'Noto Sans Arabic', sans-serif;
            background-color: #f0f2f5;
        }
        
        /* RTL Support */
        [dir="rtl"] {
            font-family: 'Noto Sans Arabic', 'Inter', sans-serif;
        }
        
        .rtl {
            direction: rtl;
            text-align: right;
        }
        
        /* Arabic Logo Styling */
        .arabic-logo {
            font-family: 'Amiri', serif;
            font-weight: 700;
        }
        
        /* Forum Cards */
        .forum-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            border-left: 4px solid #1e40af;
        }
        
        .forum-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        /* Custom Buttons */
        .btn-primary {
            @apply inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        
        /* Header Enhancements */
        header {
            position: relative;
        }
        
        header::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #3b82f6, #1e40af, #3b82f6);
        }
        
        /* Arabic Background Element */
        .arabic-background {
            position: fixed;
            top: 50%;
            right: -5%;
            transform: translateY(-50%);
            font-size: 20rem;
            opacity: 0.02;
            color: #1e3a8a;
            z-index: -1;
            pointer-events: none;
        }
    </style>
</head>
<body class="font-sans antialiased">
    {{-- Arabic background decoration --}}
    <div class="arabic-background arabic-logo">الإنصاف</div>
    
    <div class="min-h-screen bg-gray-100">

        {{-- Header --}}
        <header class="bg-gradient-to-r from-blue-700 to-blue-900 shadow-md border-b border-gray-200">
            <div class="container mx-auto px-4 py-4">
                <div class="flex flex-wrap items-center justify-between">
                    {{-- Enhanced Logo Area --}}
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="flex flex-col items-center mr-6">
                            <div class="flex items-center">
                                <span class="arabic-logo text-3xl font-bold text-white">الإنصاف</span>
                                <span class="text-2xl font-bold text-white ml-2">AL-INSAF</span>
                            </div>
                            <span class="text-xs text-blue-100 mt-1">справедливость, объективность, беспристрастность</span>
                        </a>
                    </div>

                    {{-- Search Form --}}
                    <div class="hidden md:flex items-center ml-6 mr-auto">
                        <form action="{{ route('search') }}" method="GET" class="flex items-center relative">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input type="text" name="q" placeholder="{{ __('main.search') }}..." 
                                    class="pl-10 pr-4 py-2 bg-blue-800/50 text-white placeholder-blue-200/80 border border-blue-500/30 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-transparent text-sm w-48 lg:w-64 backdrop-blur-sm transition-all duration-200 hover:bg-blue-800/70"
                                    value="{{ request('q') }}">
                            </div>
                        </form>
                    </div>

                    {{-- Navigation --}}
                    <nav class="hidden md:flex items-center space-x-6 mr-12">
                        @auth
                            <a href="{{ route('messages.index') }}" class="text-blue-100 hover:text-white transition-colors font-medium">
                                {{ __('main.messages') }}
                            </a>
                            <a href="{{ route('profile.show', auth()->user()) }}" class="text-blue-100 hover:text-white transition-colors font-medium">
                                {{ __('main.profile') }}
                            </a>
                        @endauth
                    </nav>

                    {{-- Right side buttons --}}
                    <div class="flex items-center space-x-6">
                        {{-- Telegram Button --}}
                        <a href="https://t.me/insaf_ry" target="_blank" rel="noopener noreferrer" 
                           class="flex items-center text-white bg-gradient-to-r from-[#229ED9] to-[#1c93cc] hover:from-[#31A9E1] hover:to-[#2A9FD0] transition-all duration-300 px-4 py-2 rounded-lg shadow-md hover:shadow-lg border border-[#1c93cc]/30 transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161c-.18.716-.565 2.462-.806 3.667l-1.125 5.089c-.088.377-.566.847-.904.847-.503 0-.828-.415-1.254-.729-.706-.519-1.147-.845-1.834-1.346-.81-.596-1.714-.177-1.98.177-1.551 1.127-1.025.847-1.513.847s-.415-.387-.566-.703c-.128-.27-1.663-5.449-1.875-6.802-.06-.387.088-.847.503-.847h1.578c.326 0 .619.232.708.54.328 1.129 1.682 5.542 1.831 5.872.088.177.326.177.414 0l2.95-2.693c.177-.177.266-.387.089-.566l-3.195-3.348c-.177-.177-.088-.54.177-.54h1.663c.177 0 .354.06.503.232l3.4 3.019c.177.177.414.06.503-.117z"/>
                            </svg>
                            <span class="font-medium">Telegram</span>
                        </a>
                        
                        {{-- Language Switcher --}}
                        <div class="relative ml-4" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    @click.away="open = false"
                                    class="flex items-center px-3 py-2 text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-800 rounded-lg transition-all duration-200 border border-blue-400 hover:border-blue-300">
                                {{-- Current language flag/icon --}}
                                @switch(app()->getLocale())
                                    @case('ru')
                                        <span class="mr-2">🇷🇺</span>
                                        <span>{{ __('main.language_russian') }}</span>
                                        @break
                                    @case('en') 
                                        <span class="mr-2">🇺🇸</span>
                                        <span>{{ __('main.language_english') }}</span>
                                        @break
                                    @default
                                        <span class="mr-2">🌐</span>
                                        <span>{{ __('main.language') }}</span>
                                @endswitch
                                
                                <svg class="ml-2 h-4 w-4 transition-transform duration-200" 
                                     :class="{ 'rotate-180': open }" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            {{-- Dropdown menu --}}
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
                                 style="display: none;">
                                
                                {{-- Russian --}}
                                <a href="{{ route('language.switch', 'ru') }}" 
                                   class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors {{ app()->getLocale() == 'ru' ? 'bg-blue-50 text-blue-600 font-medium' : '' }}">
                                    <span class="mr-3">🇷🇺</span>
                                    <span>{{ __('main.language_russian') }}</span>
                                    @if(app()->getLocale() == 'ru')
                                        <svg class="ml-auto h-4 w-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </a>
                                
                                {{-- English --}}
                                <a href="{{ route('language.switch', 'en') }}" 
                                   class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors {{ app()->getLocale() == 'en' ? 'bg-blue-50 text-blue-600 font-medium' : '' }}">
                                    <span class="mr-3">🇺🇸</span>
                                    <span>{{ __('main.language_english') }}</span>
                                    @if(app()->getLocale() == 'en')
                                        <svg class="ml-auto h-4 w-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </a>
                            </div>
                        </div>

                        {{-- Auth buttons --}}
                        @guest
                            <a href="{{ route('login') }}" 
                               class="text-blue-100 hover:text-white transition-colors font-medium">
                                {{ __('auth.login') }}
                            </a>
                            <a href="{{ route('register') }}" 
                               class="px-4 py-2 bg-white text-blue-700 rounded-lg hover:bg-blue-50 transition-colors font-medium shadow-md hover:shadow-lg">
                                {{ __('auth.register') }}
                            </a>
                        @else
                            {{-- User menu --}}
                            <div class="relative ml-2" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        @click.away="open = false"
                                        class="flex items-center space-x-2 text-blue-100 hover:text-white transition-colors">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full object-cover">
                                    @else
                                        <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-blue-700 font-semibold text-sm">
                                            {{ substr(auth()->user()->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <span class="font-medium">{{ auth()->user()->name }}</span>
                                    <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
                                     style="display: none;">
                                    
                                    <a href="{{ route('profile.show', auth()->user()) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        {{ __('main.my_profile') }}
                                    </a>
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        {{ __('main.settings') }}
                                    </a>
                                    <a href="{{ route('messages.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        {{ __('main.messages') }}
                                    </a>
                                    
                                    @if(auth()->user()->isAdmin())
                                        <hr class="my-1">
                                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 font-medium">
                                            {{ __('main.admin_panel') }}
                                        </a>
                                    @endif
                                    
                                    <hr class="my-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            {{ __('auth.logout') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endguest

                        {{-- Mobile menu toggle button --}}
                        <button onclick="toggleMobileMenu()" class="md:hidden p-2 rounded-md text-blue-100 hover:text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-300 transition-colors">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        
                        {{-- Mobile search icon --}}
                        <a href="{{ route('search') }}" class="md:hidden p-2 text-blue-100 hover:text-white hover:bg-blue-800 rounded-full transition-all duration-200 flex items-center justify-center w-9 h-9 bg-blue-800/80 backdrop-blur-sm border border-blue-500/20">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Mobile menu --}}
                <div id="mobile-menu" class="md:hidden hidden mt-4 pb-4">
                    <div class="flex flex-col space-y-2 bg-blue-800 rounded-lg p-3">
                        {{-- Mobile search in menu --}}
                        <form action="{{ route('search') }}" method="GET" class="mb-2">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input type="text" name="q" placeholder="{{ __('main.search') }}..." 
                                    class="w-full pl-10 pr-4 py-2 bg-blue-700/50 text-white placeholder-blue-200/80 border border-blue-500/30 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-transparent text-sm backdrop-blur-sm">
                            </div>
                        </form>
                        
                        <a href="{{ route('home') }}" class="px-3 py-2 text-blue-100 hover:text-white hover:bg-blue-700 rounded-md transition-colors">{{ __('main.home') }}</a>
                        <a href="{{ route('home') }}" class="px-3 py-2 text-blue-100 hover:text-white hover:bg-blue-700 rounded-md transition-colors">{{ __('main.forums') }}</a>
                        
                        @auth
                            <a href="{{ route('messages.index') }}" class="px-3 py-2 text-blue-100 hover:text-white hover:bg-blue-700 rounded-md transition-colors">{{ __('main.messages') }}</a>
                            <a href="{{ route('profile.show', auth()->user()) }}" class="px-3 py-2 text-blue-100 hover:text-white hover:bg-blue-700 rounded-md transition-colors">{{ __('main.profile') }}</a>
                        @else
                            <a href="{{ route('login') }}" class="px-3 py-2 text-blue-100 hover:text-white hover:bg-blue-700 rounded-md transition-colors">{{ __('auth.login') }}</a>
                            <a href="{{ route('register') }}" class="px-3 py-2 bg-white text-blue-700 hover:bg-blue-50 rounded-md transition-colors">{{ __('auth.register') }}</a>
                        @endauth
                        
                        {{-- Telegram Link in Mobile Menu --}}
                        <a href="https://t.me/insaf_ry" target="_blank" rel="noopener noreferrer" class="mt-4 flex items-center justify-center py-2.5 bg-gradient-to-r from-[#229ED9] to-[#1c93cc] hover:from-[#31A9E1] hover:to-[#2A9FD0] text-white rounded-md shadow-md border border-[#1c93cc]/30 transition-all duration-300">
                            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161c-.18.716-.565 2.462-.806 3.667l-1.125 5.089c-.088.377-.566.847-.904.847-.503 0-.828-.415-1.254-.729-.706-.519-1.147-.845-1.834-1.346-.81-.596-1.714-.177-1.98.177-1.551 1.127-1.025.847-1.513.847s-.415-.387-.566-.703c-.128-.27-1.663-5.449-1.875-6.802-.06-.387.088-.847.503-.847h1.578c.326 0 .619.232.708.54.328 1.129 1.682 5.542 1.831 5.872.088.177.326.177.414 0l2.95-2.693c.177-.177.266-.387.089-.566l-3.195-3.348c-.177-.177-.088-.54.177-.54h1.663c.177 0 .354.06.503.232l3.4 3.019c.177.177.414.06.503-.117z"/>
                            </svg>
                            <span class="font-medium">Подписаться на Telegram</span>
                        </a>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>

    {{-- Дополнительные скрипты --}}
    @stack('scripts')
    
    <!-- Компонент для формы жалобы -->
    @include('components.report-form')
</body>
</html>