@extends('layouts.app')

@section('title', __('main.reports') . ' - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Заголовок -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-700">⚠️ Управление жалобами</h1>
                <p class="text-gray-600 mt-2">
                    Всего жалоб: {{ $reports->total() }} | 
                    Ожидающих рассмотрения: {{ $pendingCount ?? 0 }}
                </p>
            </div>
            
            <div class="mt-4 lg:mt-0 flex flex-wrap gap-2">
                <button onclick="resolveAllReports()" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    ✅ Отметить все решенными
                </button>
                <a href="{{ route('admin.dashboard') }}" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    ← Назад
                </a>
            </div>
        </div>
    </div>

    <!-- Фильтры -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Статус -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
                    <select name="status" id="status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Все статусы</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Ожидает</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Решена</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Отклонена</option>
                    </select>
                </div>

                <!-- Тип жалобы -->
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Причина</label>
                    <select name="reason" id="reason"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Все причины</option>
                        <option value="spam" {{ request('reason') === 'spam' ? 'selected' : '' }}>Спам</option>
                        <option value="inappropriate_content" {{ request('reason') === 'inappropriate_content' ? 'selected' : '' }}>Неприемлемый контент</option>
                        <option value="harassment" {{ request('reason') === 'harassment' ? 'selected' : '' }}>Домогательство</option>
                        <option value="fake_information" {{ request('reason') === 'fake_information' ? 'selected' : '' }}>Ложная информация</option>
                        <option value="copyright" {{ request('reason') === 'copyright' ? 'selected' : '' }}>Нарушение авторских прав</option>
                        <option value="other" {{ request('reason') === 'other' ? 'selected' : '' }}>Другое</option>
                    </select>
                </div>

                <!-- Автор жалобы -->
                <div>
                    <label for="reporter" class="block text-sm font-medium text-gray-700 mb-1">Автор жалобы</label>
                    <input type="text" name="reporter" id="reporter" 
                           value="{{ request('reporter') }}"
                           placeholder="Имя пользователя"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Дата -->
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Дата с</label>
                    <input type="date" name="date_from" id="date_from" 
                           value="{{ request('date_from') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex justify-between items-center">
                <div class="flex space-x-2">
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        🔍 Применить фильтры
                    </button>
                    <a href="{{ route('admin.reports.index') }}" 
                       class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                        Сбросить
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Статистика -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow text-center">
            <div class="text-2xl font-bold text-orange-600">{{ $stats['pending'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Ожидает</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow text-center">
            <div class="text-2xl font-bold text-green-600">{{ $stats['resolved'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Решено</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow text-center">
            <div class="text-2xl font-bold text-red-600">{{ $stats['rejected'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Отклонено</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Всего</div>
        </div>
    </div>

    <!-- Статус жалобы -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">📋 Объяснение статусов</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="border rounded p-3">
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">⏳ Ожидает</span>
                    <span class="px-2 py-1 bg-gray-100 text-gray-500 text-xs rounded-full">offtopic</span>
                </div>
                <p class="text-sm text-gray-600">Жалоба ожидает рассмотрения модератором.</p>
                <p class="text-sm text-gray-600 mt-2">Тег "offtopic" означает, что жалоба не относится к основным категориям.</p>
            </div>
            
            <div class="border rounded p-3">
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">🔥 Высокий приоритет</span>
                </div>
                <p class="text-sm text-gray-600">Жалобы с высоким приоритетом требуют скорейшего рассмотрения.</p>
            </div>
            
            <div class="border rounded p-3">
                <p class="text-sm text-gray-600">Статусы жалоб управляются на бэкенде.</p>
                <p class="text-sm text-gray-600 mt-2">Значения статусов и приоритетов хранятся в базе данных.</p>
            </div>
        </div>
    </div>

    <!-- Список жалоб -->
    <div class="space-y-4">
        @forelse($reports as $report)
            <div class="bg-white rounded-lg shadow-lg p-6 
                        {{ $report->status === 'pending' ? 'border-l-4 border-orange-500' : '' }}
                        {{ $report->status === 'resolved' ? 'border-l-4 border-green-500' : '' }}
                        {{ $report->status === 'rejected' ? 'border-l-4 border-red-500' : '' }}">
                
                <div class="flex items-start justify-between">
                    <div class="flex-grow">
                        <!-- Заголовок жалобы -->
                        <div class="flex items-center gap-3 mb-3">
                            <input type="checkbox" name="selected_reports[]" value="{{ $report->id }}" 
                                   class="report-checkbox text-blue-600 focus:ring-blue-500">
                            
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    {{ $report->status === 'pending' ? 'bg-orange-100 text-orange-800' : '' }}
                                    {{ $report->status === 'resolved' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $report->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                    @if($report->status === 'pending')
                                        ⏳ Ожидает
                                    @elseif($report->status === 'resolved')
                                        ✅ Решена
                                    @else
                                        ❌ Отклонена
                                    @endif
                                </span>
                                
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">
                                    {{ $reasonLabels[$report->reason] ?? $report->reason }}
                                </span>
                                
                                @if($report->priority === 'high')
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">
                                        🔥 {{ __('main.high_priority') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Информация о жалобе -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Автор жалобы -->
                            <div>
                                <h3 class="font-medium text-gray-700 mb-2">👤 {{ __('main.report_author') }}</h3>
                                <div class="flex items-center">
                                    @if($report->reporter && $report->reporter->avatar)
                                        <img src="{{ $report->reporter->avatar_url }}" alt="{{ $report->reporter->name }}" 
                                             class="w-8 h-8 rounded-full object-cover mr-3">
                                    @elseif($report->reporter)
                                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-bold mr-3">
                                            {{ strtoupper(substr($report->reporter->name, 0, 1)) }}
                                        </div>
                                    @else
                                        <div class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center text-white text-sm font-bold mr-3">
                                            <span>?</span>
                                        </div>
                                    @endif
                                    <div>
                                        @if($report->reporter)
                                        <a href="{{ route('profile.show', $report->reporter) }}" 
                                           class="font-medium text-blue-600 hover:text-blue-800">
                                            {{ $report->reporter->name }}
                                        </a>
                                        @else
                                        <span class="font-medium text-gray-600">
                                            Неизвестно
                                        </span>
                                        @endif
                                        <div class="text-xs text-gray-500">{{ $report->created_at->format('d.m.Y H:i') }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Объект жалобы -->
                            <div>
                                <h3 class="font-medium text-gray-700 mb-2">🎯 {{ __('main.report_object') }}</h3>
                                @if($report->post)
                                    <div class="border border-gray-200 rounded p-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium">{{ __('main.post_from') }} {{ $report->post->user->name }}</span>
                                            <a href="{{ route('topics.show', $report->post->topic) }}#post-{{ $report->post->id }}" 
                                               class="text-blue-600 hover:text-blue-800 text-sm">
                                                {{ __('main.go_to') }} →
                                            </a>
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            {{ __('main.in_topic') }}: <span class="font-medium">{{ $report->post->topic->title }}</span>
                                        </div>
                                        <div class="text-sm text-gray-700 mt-2 bg-gray-50 p-2 rounded">
                                            {{ Str::limit(strip_tags($report->post->content), 150) }}
                                        </div>
                                    </div>
                                @elseif($report->topic)
                                    <div class="border border-gray-200 rounded p-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium">{{ __('main.topic_from') }} {{ $report->topic->user->name }}</span>
                                            <a href="{{ route('topics.show', $report->topic) }}" 
                                               class="text-blue-600 hover:text-blue-800 text-sm">
                                                {{ __('main.go_to') }} →
                                            </a>
                                        </div>
                                        <div class="text-sm font-medium text-gray-700">{{ $report->topic->title }}</div>
                                    </div>
                                @elseif($report->user_reported)
                                    <div class="border border-gray-200 rounded p-3">
                                        <div class="flex items-center">
                                            @if($report->user_reported->avatar)
                                                <img src="{{ $report->user_reported->avatar_url }}" alt="{{ $report->user_reported->name }}" 
                                                     class="w-8 h-8 rounded-full object-cover mr-3">
                                            @else
                                                <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center text-white text-sm font-bold mr-3">
                                                    {{ strtoupper(substr($report->user_reported->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <a href="{{ route('profile.show', $report->user_reported) }}" 
                                                   class="font-medium text-blue-600 hover:text-blue-800">
                                                    {{ $report->user_reported->name }}
                                                </a>
                                                <div class="text-xs text-gray-500">{{ __('main.user') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Описание жалобы -->
                        @if($report->description)
                            <div class="mt-4">
                                <h3 class="font-medium text-gray-700 mb-2">📝 {{ __('main.description') }}</h3>
                                <div class="bg-gray-50 p-3 rounded text-sm text-gray-700">
                                    {{ $report->description }}
                                </div>
                            </div>
                        @endif

                        <!-- Ответ модератора -->
                        @if($report->response)
                            <div class="mt-4">
                                <h3 class="font-medium text-gray-700 mb-2">💬 {{ __('main.moderator_response') }}</h3>
                                <div class="bg-blue-50 p-3 rounded border border-blue-200">
                                    <div class="text-sm text-blue-700">{{ $report->response }}</div>
                                    @if($report->resolved_by)
                                        <div class="text-xs text-blue-600 mt-2">
                                            — {{ $report->resolved_by->name }}, {{ $report->resolved_at->format('d.m.Y H:i') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Действия -->
                    <div class="ml-4 flex flex-col space-y-2">
                        @if($report->status === 'pending')
                            <form action="{{ route('admin.reports.resolve', $report->id) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700 transition-colors w-full">
                                    ✅ Решить
                                </button>
                            </form>
                            <form action="{{ route('admin.reports.reject', $report->id) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700 transition-colors w-full">
                                    ❌ Отклонить
                                </button>
                            </form>
                        @endif
                        
                        <a href="{{ route('admin.reports.show', $report->id) }}" 
                           class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 transition-colors text-center">
                            👁️ Детали
                        </a>
                        
                        <form action="{{ route('admin.reports.destroy', $report->id) }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить эту жалобу?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="px-3 py-1 bg-gray-600 text-white rounded text-sm hover:bg-gray-700 transition-colors w-full">
                                🗑️ Удалить
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                <div class="text-6xl mb-4">📝</div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Нет жалоб</h3>
                @if(request()->hasAny(['status', 'reason', 'reporter', 'date_from']))
                    <p class="text-gray-600 mb-6">Нет жалоб, соответствующих выбранным фильтрам</p>
                    <a href="{{ route('admin.reports.index') }}" 
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Сбросить фильтры
                    </a>
                @else
                    <p class="text-gray-600">Отлично! Новых жалоб нет.</p>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Пагинация -->
    @if($reports->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $reports->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<!-- Модальные окна -->
<!-- Решение жалобы -->
<div id="resolve-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-green-600 mb-4">✅ {{ __('main.resolve_report') }}</h3>
        
        <form id="resolve-form" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="resolve_response" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('main.comment_optional') }}:
                </label>
                <textarea name="response" id="resolve_response" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                          placeholder="{{ __('main.describe_resolution') }}"></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="hideResolveModal()" 
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                    {{ __('main.cancel') }}
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    {{ __('main.resolve_report') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Отклонение жалобы -->
<div id="reject-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-red-600 mb-4">❌ {{ __('main.reject_report') }}</h3>
        
        <form id="reject-form" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="reject_response" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('main.rejection_reason') }}:
                </label>
                <textarea name="response" id="reject_response" rows="3" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                          placeholder="{{ __('main.specify_rejection_reason') }}"></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="hideRejectModal()" 
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                    {{ __('main.cancel') }}
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    {{ __('main.reject_report') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Выбрать все жалобы
function selectAllReports() {
    const checkboxes = document.querySelectorAll('.report-checkbox');
    const selectAllCheckbox = document.getElementById('select-all');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
}

// Решение жалобы
function resolveReport(reportId) {
    document.getElementById('resolve-form').action = `/admin/reports/${reportId}/resolve`;
    document.getElementById('resolve-modal').classList.add('flex');
    document.getElementById('resolve-modal').classList.remove('hidden');
}

function hideResolveModal() {
    document.getElementById('resolve-modal').classList.add('hidden');
    document.getElementById('resolve-modal').classList.remove('flex');
}

// Отклонение жалобы
function rejectReport(reportId) {
    document.getElementById('reject-form').action = `/admin/reports/${reportId}/reject`;
    document.getElementById('reject-modal').classList.add('flex');
    document.getElementById('reject-modal').classList.remove('hidden');
}

function hideRejectModal() {
    document.getElementById('reject-modal').classList.add('hidden');
    document.getElementById('reject-modal').classList.remove('flex');
}

// Детали жалобы
function viewReportDetails(reportId) {
    window.location.href = `/admin/reports/${reportId}`;
}

// Удаление жалобы
function deleteReport(reportId) {
    if (confirm('{{ __("main.confirm_delete_report") }}')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/reports/${reportId}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Отметка всех жалоб как решённых
function resolveAllReports() {
    if (confirm('{{ __("main.confirm_mark_all_resolved") }}')) {
        fetch('{{ route("admin.reports.resolve-all") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message);
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                alert('{{ __("main.error") }}: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('{{ __("main.error_occurred") }}');
        });
    }
}

// Массовые действия
function executeBulkAction() {
    const action = document.getElementById('bulk-action').value;
    const selected = document.querySelectorAll('.report-checkbox:checked');
    
    if (!action) {
        alert('{{ __("main.select_action") }}');
        return;
    }
    
    if (selected.length === 0) {
        alert('{{ __("main.select_reports") }}');
        return;
    }
    
    const reportIds = Array.from(selected).map(cb => cb.value);
    
    if (confirm(`{{ __("main.execute_action_confirm") }} "${action}" {{ __("main.for") }} ${reportIds.length} {{ __("main.reports") }}?`)) {
        fetch('/admin/reports/bulk-action', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                action: action,
                report_ids: reportIds
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('{{ __("main.error") }}: ' + data.message);
            }
        });
    }
}

// Закрытие модальных окон по клику вне их
document.addEventListener('click', function(e) {
    if (e.target.id === 'resolve-modal') {
        hideResolveModal();
    }
    if (e.target.id === 'reject-modal') {
        hideRejectModal();
    }
});

// Автоматическая отправка формы при изменении фильтров
document.querySelectorAll('#status, #reason').forEach(element => {
    element.addEventListener('change', function() {
        this.closest('form').submit();
    });
});


function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-orange-500 text-white px-4 py-2 rounded-md shadow-lg z-50';
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}
</script>
@endpush

@php
$reasonLabels = [
    'spam' => __('main.spam'),
    'inappropriate_content' => __('main.inappropriate_content'),
    'harassment' => __('main.harassment'),
    'fake_information' => __('main.fake_information'),
    'copyright' => __('main.copyright_violation'),
    'other' => __('main.other')
];
@endphp
@endsection