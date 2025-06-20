@extends('layouts.app')

@section('title', __('main.new_message') . ' - ' . config('app.name'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-700">✉️ {{ __('main.new_message') }}</h1>
        </div>

        <form method="POST" action="{{ route('messages.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Получатель -->
            <div class="mb-6">
                <label for="recipient" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('main.recipient') }} *
                </label>
                
                @if(request('to'))
                    @php
                        $recipientUser = \App\Models\User::find(request('to'));
                    @endphp
                    @if($recipientUser)
                        <div class="flex items-center p-3 bg-blue-50 border border-blue-200 rounded-md">
                            @if($recipientUser->avatar)
                                <img src="{{ $recipientUser->avatar_url }}" alt="{{ $recipientUser->name }}" 
                                     class="w-10 h-10 rounded-full object-cover mr-3">
                            @else
                                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold mr-3">
                                    {{ strtoupper(substr($recipientUser->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="flex-grow">
                                <div class="font-medium text-gray-900">{{ $recipientUser->name }}</div>
                                <div class="text-sm text-gray-500">{{ $recipientUser->email }}</div>
                            </div>
                            <button type="button" onclick="clearRecipient()" 
                                    class="text-gray-400 hover:text-red-500">
                                ❌
                            </button>
                        </div>
                        <input type="hidden" name="recipient_id" value="{{ $recipientUser->id }}">
                    @endif
                @else
                    <div class="relative">
                        <input type="text" id="recipient-search" 
                               placeholder="{{ __('main.select_recipient') }}..."
                               class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        
                        <!-- Результаты поиска -->
                        <div id="search-results" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg mt-1 hidden max-h-60 overflow-y-auto">
                            <!-- Динамически заполняется -->
                        </div>
                        
                        <input type="hidden" name="recipient_id" id="recipient-id">
                    </div>
                @endif
                
                @error('recipient_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Тема -->
            <div class="mb-6">
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('main.message_subject') }} *
                </label>
                <input type="text" name="subject" id="subject" required
                       value="{{ old('subject', request('subject')) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="{{ __('main.message_subject') }}">
                @error('subject')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Содержимое сообщения -->
            <div class="mb-6">
                <label for="body" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('main.message_content') }} *
                </label>
                <div class="relative">
                    <textarea name="body" id="body" rows="12" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="{{ __('main.message_content') }}">{{ old('body') }}</textarea>
                    
                    <!-- Счетчик символов -->
                    <div class="absolute bottom-2 right-2 text-xs text-gray-400">
                        <span id="char-count">0</span> символов
                    </div>
                </div>
                @error('body')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Вложения -->
            <div class="mb-6">
                <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('main.attachments') }}
                </label>
                <input type="file" name="attachments[]" id="attachments" multiple
                       class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1 text-sm text-gray-500">{{ __('main.attachments_limit') }}</p>
                
                <!-- Предварительный просмотр вложений -->
                <div id="attachments-preview" class="mt-3 space-y-2 hidden">
                    <!-- Динамически заполняется -->
                </div>
                
                @error('attachments.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Предварительный просмотр -->
            <div class="mb-6">
                <button type="button" onclick="togglePreview()" 
                        class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                    👁️ {{ __('main.preview') }}
                </button>
                
                <div id="preview" class="hidden mt-4 p-4 bg-gray-50 border border-gray-200 rounded-md">
                    <h3 class="font-medium text-gray-700 mb-2">{{ __('main.message_preview') }}:</h3>
                    
                    <div class="bg-white p-4 rounded border">
                        <div class="border-b pb-3 mb-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-gray-900">{{ __('main.from') }}: {{ auth()->user()->name }}</div>
                                    <div class="text-sm text-gray-500" id="preview-recipient">{{ __('main.to') }}: {{ __('main.select_recipient') }}</div>
                                </div>
                                <div class="text-sm text-gray-500">{{ now()->format('d.m.Y H:i') }}</div>
                            </div>
                            <div class="mt-2">
                                <div class="font-semibold text-gray-800" id="preview-subject">{{ __('main.message_subject') }}</div>
                            </div>
                        </div>
                        
                        <div class="prose max-w-none">
                            <div id="preview-body" class="text-gray-700">{{ __('main.message_content') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Черновики -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
                <h3 class="font-medium text-blue-800 mb-2">💾 {{ __('main.autosave_draft') }}</h3>
                <div class="text-sm text-blue-700">
                    <p>{{ __('main.autosave_explanation') }}</p>
                    <div class="flex items-center justify-between mt-2">
                        <span id="draft-status" class="text-xs text-blue-600">{{ __('main.draft_not_saved') }}</span>
                        <button type="button" onclick="saveDraft()" 
                                class="px-3 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700">
                            {{ __('main.save_now') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Кнопки -->
            <div class="flex flex-col sm:flex-row gap-4 justify-end">
                <a href="{{ route('messages.index') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors text-center">
                    ❌ {{ __('main.cancel') }}
                </a>
                
                <button type="button" onclick="saveDraft()" 
                        class="px-6 py-3 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                    💾 Сохранить в черновики
                </button>
                
                <button type="submit" 
                        class="px-8 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-medium">
                    📤 {{ __('main.send_message') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let searchTimeout;
let selectedRecipient = null;

// Поиск получателей
document.addEventListener('DOMContentLoaded', function() {
    const recipientSearch = document.getElementById('recipient-search');
    const searchResults = document.getElementById('search-results');
    const recipientId = document.getElementById('recipient-id');
    let debounceTimer;
    
    // Поиск пользователей
    if (recipientSearch) {
        recipientSearch.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            
            debounceTimer = setTimeout(() => {
                const query = recipientSearch.value.trim();
                
                if (query.length < 2) {
                    searchResults.classList.add('hidden');
                    return;
                }
                
                fetch(`{{ route('api.users.search') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            searchResults.innerHTML = '';
                            
                            data.forEach(user => {
                                const item = document.createElement('div');
                                item.className = 'p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-200';
                                
                                const avatar = user.avatar_url 
                                    ? `<img src="${user.avatar_url}" alt="${user.name}" class="w-8 h-8 rounded-full object-cover">`
                                    : `<div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                        ${user.name.charAt(0).toUpperCase()}
                                       </div>`;
                                
                                item.innerHTML = `
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 mr-3">
                                            ${avatar}
                                        </div>
                                        <div class="flex-grow">
                                            <div class="font-medium text-gray-900">${user.name}</div>
                                            <div class="text-xs text-gray-500">${user.email}</div>
                                        </div>
                                    </div>
                                `;
                                
                                item.addEventListener('click', function() {
                                    selectRecipient(user);
                                });
                                
                                searchResults.appendChild(item);
                            });
                            
                            searchResults.classList.remove('hidden');
                        } else {
                            searchResults.innerHTML = `
                                <div class="p-3 text-center text-gray-500">
                                    Пользователи не найдены
                                </div>
                            `;
                            searchResults.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error searching users:', error);
                    });
            }, 300);
        });
    }
});

// Выбор получателя
function selectRecipient(user) {
    recipientSearch.parentNode.innerHTML = `
        <div class="flex items-center p-3 bg-blue-50 border border-blue-200 rounded-md">
            ${user.avatar_url 
              ? `<img src="${user.avatar_url}" alt="${user.name}" class="w-10 h-10 rounded-full object-cover mr-3">`
              : `<div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold mr-3">
                    ${user.name.charAt(0).toUpperCase()}
                 </div>`
            }
            <div class="flex-grow">
                <div class="font-medium text-gray-900">${user.name}</div>
                <div class="text-sm text-gray-500">${user.email}</div>
            </div>
            <button type="button" onclick="clearRecipient()" 
                    class="text-gray-400 hover:text-red-500">
                ❌
            </button>
        </div>
        <input type="hidden" name="recipient_id" value="${user.id}">
    `;
    
    // Обновляем предпросмотр
    document.getElementById('preview-recipient').textContent = `Кому: ${user.name}`;
}

// Очистка получателя
function clearRecipient() {
    const container = document.querySelector('input[name="recipient_id"]').parentNode;
    container.innerHTML = `
        <div class="relative">
            <input type="text" id="recipient-search" 
                   placeholder="{{ __('main.select_recipient') }}..."
                   class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            
            <div id="search-results" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg mt-1 hidden max-h-60 overflow-y-auto">
                <!-- Динамически заполняется -->
            </div>
            
            <input type="hidden" name="recipient_id" id="recipient-id">
        </div>
    `;
    
    // Переподключаем обработчики событий
    const newRecipientSearch = document.getElementById('recipient-search');
    if (newRecipientSearch) {
        newRecipientSearch.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            
            debounceTimer = setTimeout(() => {
                const query = newRecipientSearch.value.trim();
                searchUsers(query);
            }, 300);
        });
    }
    
    // Обновляем предпросмотр
    document.getElementById('preview-recipient').textContent = 'Кому: Выберите получателя';
}

// Отдельная функция для поиска пользователей
function searchUsers(query) {
    const searchResults = document.getElementById('search-results');
    
    if (query.length < 2) {
        searchResults.classList.add('hidden');
        return;
    }
    
    fetch(`{{ route('api.users.search') }}?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            if (data && data.length > 0) {
                searchResults.innerHTML = '';
                
                data.forEach(user => {
                    const item = document.createElement('div');
                    item.className = 'p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-200';
                    
                    const avatar = user.avatar_url 
                        ? `<img src="${user.avatar_url}" alt="${user.name}" class="w-8 h-8 rounded-full object-cover">`
                        : `<div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                            ${user.name.charAt(0).toUpperCase()}
                           </div>`;
                    
                    item.innerHTML = `
                        <div class="flex items-center">
                            <div class="flex-shrink-0 mr-3">
                                ${avatar}
                            </div>
                            <div class="flex-grow">
                                <div class="font-medium text-gray-900">${user.name}</div>
                                <div class="text-xs text-gray-500">${user.email}</div>
                            </div>
                        </div>
                    `;
                    
                    item.addEventListener('click', function() {
                        selectRecipient(user);
                    });
                    
                    searchResults.appendChild(item);
                });
                
                searchResults.classList.remove('hidden');
            } else {
                searchResults.innerHTML = `
                    <div class="p-3 text-center text-gray-500">
                        Пользователи не найдены
                    </div>
                `;
                searchResults.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error searching users:', error);
        });
}

// Счетчик символов
const bodyTextarea = document.getElementById('body');
const charCount = document.getElementById('char-count');

function updateCharCount() {
    const count = bodyTextarea.value.length;
    charCount.textContent = count.toLocaleString();
    
    if (count > 5000) {
        charCount.className = 'text-red-500';
    } else if (count > 4000) {
        charCount.className = 'text-orange-500';
    } else {
        charCount.className = 'text-gray-400';
    }
}

bodyTextarea.addEventListener('input', updateCharCount);
updateCharCount();

// Предварительный просмотр вложений
document.getElementById('attachments').addEventListener('change', function() {
    const files = this.files;
    const previewContainer = document.getElementById('attachments-preview');
    
    if (files.length === 0) {
        previewContainer.classList.add('hidden');
        return;
    }
    
    previewContainer.innerHTML = '';
    previewContainer.classList.remove('hidden');
    
    Array.from(files).forEach((file, index) => {
        const fileDiv = document.createElement('div');
        fileDiv.className = 'flex items-center justify-between p-2 bg-gray-100 rounded';
        fileDiv.innerHTML = `
            <div class="flex items-center">
                <span class="text-gray-600 mr-2">📎</span>
                <span class="text-sm font-medium">${file.name}</span>
                <span class="text-xs text-gray-500 ml-2">(${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
            </div>
            <button type="button" onclick="removeAttachment(${index})" class="text-red-500 hover:text-red-700">
                ❌
            </button>
        `;
        previewContainer.appendChild(fileDiv);
    });
});

// Удаление вложения
function removeAttachment(index) {
    const attachmentsInput = document.getElementById('attachments');
    const dt = new DataTransfer();
    
    Array.from(attachmentsInput.files).forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });
    
    attachmentsInput.files = dt.files;
    attachmentsInput.dispatchEvent(new Event('change'));
}

// Предварительный просмотр сообщения
function togglePreview() {
    const preview = document.getElementById('preview');
    const subject = document.getElementById('subject').value || 'Тема сообщения';
    const body = document.getElementById('body').value || 'Содержимое сообщения';
    
    if (preview.classList.contains('hidden')) {
        document.getElementById('preview-subject').textContent = subject;
        document.getElementById('preview-body').innerHTML = body.replace(/\n/g, '<br>');
        preview.classList.remove('hidden');
    } else {
        preview.classList.add('hidden');
    }
}

// Автосохранение черновика
let draftTimeout;
const draftStatus = document.getElementById('draft-status');

function saveDraft() {
    const formData = new FormData(document.querySelector('form'));
    
    fetch('{{ route("messages.save-draft") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            draftStatus.textContent = `Черновик сохранен: ${new Date().toLocaleTimeString()}`;
            draftStatus.className = 'text-xs text-green-600';
        } else {
            draftStatus.textContent = 'Ошибка сохранения черновика';
            draftStatus.className = 'text-xs text-red-600';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        draftStatus.textContent = 'Ошибка сохранения черновика';
        draftStatus.className = 'text-xs text-red-600';
    });
}

// Автосохранение каждые 30 секунд
function scheduleAutoSave() {
    clearTimeout(draftTimeout);
    draftTimeout = setTimeout(() => {
        if (document.getElementById('subject').value.trim() || document.getElementById('body').value.trim()) {
            saveDraft();
        }
        scheduleAutoSave();
    }, 30000);
}

// Запускаем автосохранение при изменении полей
['subject', 'body'].forEach(fieldId => {
    document.getElementById(fieldId).addEventListener('input', () => {
        draftStatus.textContent = 'Есть несохраненные изменения';
        draftStatus.className = 'text-xs text-orange-600';
        scheduleAutoSave();
    });
});

// Предупреждение о несохраненных изменениях
window.addEventListener('beforeunload', function(e) {
    if (document.getElementById('subject').value.trim() || document.getElementById('body').value.trim()) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Очистка предупреждения при отправке формы
document.querySelector('form').addEventListener('submit', function() {
    window.removeEventListener('beforeunload', arguments.callee);
});

// Скрытие результатов поиска при клике вне
document.addEventListener('click', function(e) {
    if (!e.target.closest('#recipient-search') && !e.target.closest('#search-results')) {
        document.getElementById('search-results')?.classList.add('hidden');
    }
});

// Инициализация
scheduleAutoSave();
</script>
@endpush

@push('styles')
<style>
.prose {
    max-width: none;
}
</style>
@endpush
@endsection