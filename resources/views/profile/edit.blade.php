@extends('layouts.app')

@section('title', __('main.edit_profile'))

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-3xl font-bold text-gray-700 mb-6">{{ __('main.edit_profile') }}</h1>

        <!-- Основная информация -->
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Аватар -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('main.avatar') }}</label>
                <div class="flex items-center space-x-6">
                    <div class="flex-shrink-0">
                        @if(auth()->user()->avatar)
                            <img id="avatar-preview" src="{{ auth()->user()->avatar_url }}" alt="{{ __('main.current_avatar') }}" 
                                 class="w-20 h-20 rounded-full object-cover border-2 border-gray-300">
                        @else
                            <div id="avatar-preview" 
                                 class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-xl font-bold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-grow">
                        <input type="file" name="avatar" id="avatar" accept="image/*"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="mt-1 text-xs text-gray-500">{{ __('main.avatar_requirements') }}</p>
                        @if(auth()->user()->avatar)
                            <label class="flex items-center mt-2">
                                <input type="checkbox" name="remove_avatar" value="1" class="mr-2">
                                <span class="text-sm text-red-600">{{ __('main.remove_current_avatar') }}</span>
                            </label>
                        @endif
                    </div>
                </div>
                @error('avatar')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Имя пользователя -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('main.username') }} *
                </label>
                <input type="text" name="name" id="name" required
                       value="{{ old('name', auth()->user()->name) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1 text-xs text-gray-500">{{ __('main.username_display_note') }}</p>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Кнопки -->
            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('profile.show', auth()->user()) }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                    {{ __('main.cancel') }}
                </a>
                <button type="submit" 
                        class="px-8 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-medium">
                    {{ __('main.save_changes') }}
                </button>
            </div>
        </form>

        <!-- Смена пароля -->
        <div class="mt-8 pt-8 border-t">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">{{ __('main.change_password') }}</h2>
            
            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('main.current_password') }} *
                    </label>
                    <input type="password" name="current_password" id="current_password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('main.new_password') }} *
                    </label>
                    <input type="password" name="password" id="password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('main.confirm_password') }} *
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            class="px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                        {{ __('main.update_password') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Удаление аккаунта -->
        <div class="mt-8 pt-8 border-t">
            <h2 class="text-xl font-semibold text-red-600 mb-4">{{ __('main.danger_zone') }}</h2>
            
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h3 class="font-medium text-red-800 mb-2">{{ __('main.delete_account') }}</h3>
                <p class="text-sm text-red-700 mb-4">
                    {{ __('main.delete_account_warning') }}
                </p>
                
                <button type="button" onclick="showDeleteModal()" 
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                    {{ __('main.delete_account') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для удаления аккаунта -->
<div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-red-600 mb-4">{{ __('main.delete_confirmation') }}</h3>
        <p class="text-gray-700 mb-4">
            {{ __('main.delete_account_confirmation') }}
        </p>
        
        <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf
            @method('DELETE')
            
            <div class="mb-4">
                <label for="delete_password" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('main.enter_password_to_confirm') }} *
                </label>
                <input type="password" name="password" id="delete_password" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div class="flex justify-end space-x-4">
                <button type="button" onclick="hideDeleteModal()" 
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                    {{ __('main.cancel') }}
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    {{ __('main.delete_account_permanent') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Предварительный просмотр аватара
    document.getElementById('avatar')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatar-preview');
            
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                // Если раньше не было аватара, создаем элемент img
                const img = document.createElement('img');
                img.src = e.target.result;
                img.id = 'avatar-preview';
                img.className = 'w-20 h-20 rounded-full object-cover border-2 border-gray-300';
                img.alt = "{{ __('main.preview') }}";
                
                preview.parentNode.replaceChild(img, preview);
            }
        };
        
        reader.readAsDataURL(file);
    });
    
    // Показать/скрыть модальное окно удаления аккаунта
    function showDeleteModal() {
        const modal = document.getElementById('delete-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    
    function hideDeleteModal() {
        const modal = document.getElementById('delete-modal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
</script>
@endpush
@endsection