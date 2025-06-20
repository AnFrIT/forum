@extends('layouts.app')

@section('title', __('main.forum_settings') . ' - ' . config('app.name'))

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Заголовок -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6 mt-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-700">⚙️ {{ __('main.forum_settings') }}</h1>
                <p class="text-gray-600 mt-2">{{ __('main.forum_settings_desc') }}</p>
            </div>
            
            <div class="mt-4 lg:mt-0 flex flex-wrap gap-2">
                <a href="{{ route('admin.dashboard') }}" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    ← {{ __('main.back') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Основные настройки -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="border-b border-gray-200">
            <div class="px-6 py-4">
                <h2 class="text-xl font-semibold text-gray-700">🏠 {{ __('main.general_settings') }}</h2>
            </div>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="tab" value="general">
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Название форума -->
                    <div>
                        <label for="site_name" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('main.forum_name') }} *
                        </label>
                        <input type="text" name="site_name" id="site_name" required
                               value="{{ old('site_name', $settings['site_name'] ?? config('app.name')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Описание форума -->
                    <div>
                        <label for="site_description" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('main.forum_description') }}
                        </label>
                        <input type="text" name="site_description" id="site_description"
                               value="{{ old('site_description', $settings['site_description'] ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="{{ __('main.forum_description_placeholder') }}">
                    </div>

                    <!-- URL форума -->
                    <div>
                        <label for="site_url" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('main.forum_url') }} *
                        </label>
                        <input type="url" name="site_url" id="site_url" required
                               value="{{ old('site_url', $settings['site_url'] ?? config('app.url')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Часовой пояс -->
                    <div>
                        <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('main.timezone') }}
                        </label>
                        <select name="timezone" id="timezone"
                                class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="UTC" {{ ($settings['timezone'] ?? 'UTC') === 'UTC' ? 'selected' : '' }}>UTC</option>
                            <option value="Europe/Moscow" {{ ($settings['timezone'] ?? '') === 'Europe/Moscow' ? 'selected' : '' }}>{{ __('main.moscow_time') }}</option>
                            <option value="Europe/Kiev" {{ ($settings['timezone'] ?? '') === 'Europe/Kiev' ? 'selected' : '' }}>{{ __('main.kiev_time') }}</option>
                            <option value="Asia/Dubai" {{ ($settings['timezone'] ?? '') === 'Asia/Dubai' ? 'selected' : '' }}>{{ __('main.dubai_time') }}</option>
                        </select>
                    </div>

                    <!-- Язык по умолчанию -->
                    <div>
                        <label for="default_language" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('main.default_language') }}
                        </label>
                        <select name="default_language" id="default_language"
                                class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="ru" {{ ($settings['default_language'] ?? 'ru') === 'ru' ? 'selected' : '' }}>{{ __('main.language_russian') }}</option>
                            <option value="en" {{ ($settings['default_language'] ?? '') === 'en' ? 'selected' : '' }}>{{ __('main.language_english') }}</option>
                        </select>
                    </div>

                    <!-- Статус форума -->
                    <div>
                        <label for="site_status" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('main.forum_status') }}
                        </label>
                        <select name="site_status" id="site_status"
                                class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="online" {{ ($settings['site_status'] ?? 'online') === 'online' ? 'selected' : '' }}>🟢 {{ __('main.online') }}</option>
                            <option value="maintenance" {{ ($settings['site_status'] ?? '') === 'maintenance' ? 'selected' : '' }}>🔧 {{ __('main.maintenance') }}</option>
                            <option value="offline" {{ ($settings['site_status'] ?? '') === 'offline' ? 'selected' : '' }}>🔴 {{ __('main.offline') }}</option>
                        </select>
                    </div>
                </div>

                <!-- Сообщение об обслуживании -->
                <div class="mt-6">
                    <label for="maintenance_message" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('main.maintenance_message') }}
                    </label>
                    <textarea name="maintenance_message" id="maintenance_message" rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="{{ __('main.maintenance_message_placeholder') }}">{{ old('maintenance_message', $settings['maintenance_message'] ?? '') }}</textarea>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        💾 {{ __('main.save_settings') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
console.log('Admin settings page loaded');
</script>
@endpush
@endsection