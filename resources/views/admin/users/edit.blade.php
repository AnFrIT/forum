@extends('layouts.app')

@section('title', __('main.edit_user') . ': ' . $user->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-700">✏️ Редактировать пользователя</h1>
            <p class="text-gray-600 mt-2">Редактирование данных пользователя: <strong>{{ $user->name }}</strong></p>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Основная информация -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">👤 Основная информация</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Имя -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Имя пользователя *
                            </label>
                            <input type="text" name="name" id="name" required
                                   value="{{ old('name', $user->name) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email *
                            </label>
                            <input type="email" name="email" id="email" required
                                   value="{{ old('email', $user->email) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Новый пароль -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Новый пароль
                            </label>
                            <input type="password" name="password" id="password"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Оставьте пустым, если не хотите менять пароль</p>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Язык -->
                        <div>
                            <label for="preferred_language" class="block text-sm font-medium text-gray-700 mb-2">
                                Предпочитаемый язык
                            </label>
                            <select name="preferred_language" id="preferred_language"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="ru" {{ old('preferred_language', $user->preferred_language) === 'ru' ? 'selected' : '' }}>Русский</option>
                                <option value="en" {{ old('preferred_language', $user->preferred_language) === 'en' ? 'selected' : '' }}>English</option>
                                <option value="ar" {{ old('preferred_language', $user->preferred_language) === 'ar' ? 'selected' : '' }}>العربية</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Дополнительная информация -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">📝 Дополнительная информация</h3>
                    
                    <div class="space-y-4">
                        <!-- Биография -->
                        <div>
                            <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                                О себе
                            </label>
                            <textarea name="bio" id="bio" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Расскажите о себе...">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Сайт -->
                            <div>
                                <label for="website" class="block text-sm font-medium text-gray-700 mb-2">
                                    Веб-сайт
                                </label>
                                <input type="url" name="website" id="website"
                                       value="{{ old('website', $user->website) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="https://example.com">
                                @error('website')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Роли и права -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">🛡️ Роли и права</h3>
                    
                    <div class="space-y-4">
                        @if(auth()->user()->is_admin)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Администратор -->
                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_admin" value="1" 
                                               {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}
                                               class="mr-3 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                                               {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                        <span class="text-sm font-medium text-gray-700">👑 Администратор</span>
                                    </label>
                                    <p class="ml-6 text-xs text-gray-500">Полный доступ ко всем функциям</p>
                                    @if($user->id === auth()->id())
                                        <p class="ml-6 text-xs text-orange-600">Нельзя снять права с самого себя</p>
                                    @endif
                                </div>

                                <!-- Модератор -->
                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_moderator" value="1" 
                                               {{ old('is_moderator', $user->is_moderator) ? 'checked' : '' }}
                                               class="mr-3 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                        <span class="text-sm font-medium text-gray-700">🛡️ Модератор</span>
                                    </label>
                                    <p class="ml-6 text-xs text-gray-500">Модерация контента и пользователей</p>
                                </div>
                            </div>
                        @else
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <p class="text-blue-800 text-sm">
                                    ℹ️ Текущие роли: 
                                    @if($user->is_admin)
                                        <span class="font-medium">Администратор</span>
                                    @elseif($user->is_moderator)
                                        <span class="font-medium">Модератор</span>
                                    @else
                                        <span class="font-medium">Пользователь</span>
                                    @endif
                                </p>
                                <p class="text-blue-700 text-xs mt-1">Изменение ролей доступно только администраторам</p>
                            </div>
                        @endif

                        <!-- Бан -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_banned" id="is_banned" value="1" 
                                       {{ old('is_banned', $user->is_banned) ? 'checked' : '' }}
                                       class="mr-3 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                                       {{ $user->id === auth()->id() ? 'disabled' : '' }}
                                       onchange="toggleBanReason(this)">
                                <span class="text-sm font-medium text-gray-700">🚫 Заблокирован</span>
                            </label>
                            <p class="ml-6 text-xs text-gray-500">Пользователь не может входить на сайт</p>
                            @if($user->id === auth()->id())
                                <p class="ml-6 text-xs text-orange-600">Нельзя заблокировать самого себя</p>
                            @endif

                            <!-- Причина бана -->
                            <div id="banReasonContainer" class="mt-3 ml-6" style="{{ old('is_banned', $user->is_banned) ? '' : 'display: none;' }}">
                                <label for="ban_reason" class="block text-sm font-medium text-gray-700 mb-2">
                                    Причина блокировки
                                </label>
                                <select name="ban_reason" id="ban_reason"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                                    <option value="">Выберите причину</option>
                                    <option value="spam" {{ old('ban_reason', $user->ban_reason) === 'spam' ? 'selected' : '' }}>Спам</option>
                                    <option value="inappropriate_content" {{ old('ban_reason', $user->ban_reason) === 'inappropriate_content' ? 'selected' : '' }}>Неподобающий контент</option>
                                    <option value="harassment" {{ old('ban_reason', $user->ban_reason) === 'harassment' ? 'selected' : '' }}>Домогательства</option>
                                    <option value="multiple_accounts" {{ old('ban_reason', $user->ban_reason) === 'multiple_accounts' ? 'selected' : '' }}>Множественные аккаунты</option>
                                    <option value="violation_rules" {{ old('ban_reason', $user->ban_reason) === 'violation_rules' ? 'selected' : '' }}>Нарушение правил</option>
                                    <option value="other" {{ old('ban_reason', $user->ban_reason) === 'other' ? 'selected' : '' }}>Другое</option>
                                </select>

                                <label for="ban_expires_at" class="block text-sm font-medium text-gray-700 mt-3 mb-2">
                                    Срок блокировки
                                </label>
                                <select name="ban_duration" id="ban_duration"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                                    <option value="1">1 день</option>
                                    <option value="3">3 дня</option>
                                    <option value="7">1 неделя</option>
                                    <option value="14">2 недели</option>
                                    <option value="30">1 месяц</option>
                                    <option value="90">3 месяца</option>
                                    <option value="365">1 год</option>
                                    <option value="permanent">Навсегда</option>
                                </select>
                            </div>
                        </div>

                        @if($user->is_banned && $user->ban_reason)
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <h4 class="font-medium text-red-800 mb-2">Причина блокировки:</h4>
                                <p class="text-red-700 text-sm">{{ $user->ban_reason }}</p>
                                @if($user->ban_expires_at)
                                    <p class="text-red-600 text-xs mt-1">
                                        Заблокирован до: {{ $user->ban_expires_at->format('d.m.Y H:i') }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Статистика -->
                <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
                    <h3 class="text-lg font-semibold text-blue-800 mb-4">📊 Статистика</h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-blue-600">{{ $user->topics_count ?? 0 }}</div>
                            <div class="text-sm text-blue-700">Тем</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-blue-600">{{ $user->posts_count ?? 0 }}</div>
                            <div class="text-sm text-blue-700">Сообщений</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-blue-600">{{ $user->reputation ?? 0 }}</div>
                            <div class="text-sm text-blue-700">Репутация</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-blue-600">
                                {{ $user->created_at->diffInDays() }}
                            </div>
                            <div class="text-sm text-blue-700">Дней с нами</div>
                        </div>
                    </div>
                </div>

                <!-- Кнопки -->
                <div class="flex flex-col sm:flex-row gap-4 justify-end pt-6 border-t">
                    <a href="{{ route('admin.users.index') }}" 
                       class="px-6 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors text-center">
                        ❌ Отменить
                    </a>
                    
                    @if($user->id !== auth()->id())
                        <button type="button" onclick="deleteUser()" 
                                class="px-6 py-3 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                            🗑️ Удалить пользователя
                        </button>
                    @endif
                    
                    <button type="submit" 
                            class="px-8 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-medium">
                        💾 Сохранить изменения
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Форма удаления (скрытая) -->
@if($user->id !== auth()->id())
    <form id="delete-form" method="POST" action="{{ route('admin.users.destroy', $user) }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endif

@push('scripts')
<script>
function deleteUser() {
    if (confirm('Удалить пользователя {{ $user->name }}? Это действие необратимо!')) {
        if (confirm('Вы действительно уверены? Все данные пользователя будут удалены!')) {
            document.getElementById('delete-form').submit();
        }
    }
}

// Предупреждение о несохраненных изменениях
let originalFormData = new FormData(document.querySelector('form'));

window.addEventListener('beforeunload', function(e) {
    const currentFormData = new FormData(document.querySelector('form'));
    let hasChanges = false;
    
    for (let [key, value] of currentFormData.entries()) {
        if (originalFormData.get(key) !== value) {
            hasChanges = true;
            break;
        }
    }
    
    if (hasChanges) {
        e.preventDefault();
        e.returnValue = 'У вас есть несохраненные изменения. Вы уверены, что хотите покинуть страницу?';
    }
});

document.querySelector('form').addEventListener('submit', function() {
    window.removeEventListener('beforeunload', arguments.callee);
});

function toggleBanReason(checkbox) {
    const container = document.getElementById('banReasonContainer');
    if (checkbox.checked) {
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
    }
}
</script>
@endpush
@endsection