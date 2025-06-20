@extends('layouts.app')

@section('title', __('main.report_details') . ' - ' . config('app.name'))

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Заголовок -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-700">
                    ⚠️ {{ __('main.report_details') }} #{{ $report->id }}
                </h1>
                <p class="text-gray-600 mt-2">
                    {{ __('main.submitted') }}: {{ $report->created_at->format('d.m.Y H:i') }}
                </p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.reports.index') }}" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    ← {{ __('main.back_to_reports') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Статус жалобы -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">📊 {{ __('main.status') }}</h2>
        
        <div class="flex items-center gap-4">
            <span class="px-3 py-1 rounded-full text-sm font-medium
                {{ $report->status === 'pending' ? 'bg-orange-100 text-orange-800' : '' }}
                {{ $report->status === 'resolved' ? 'bg-green-100 text-green-800' : '' }}
                {{ $report->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                @if($report->status === 'pending')
                    ⏳ {{ __('main.pending') }}
                @elseif($report->status === 'resolved')
                    ✅ {{ __('main.resolved') }}
                @else
                    ❌ {{ __('main.rejected') }}
                @endif
            </span>
            
            <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm rounded-full">
                {{ __('main.reason') }}: {{ $report->reason }}
            </span>
            
            @if($report->priority === 'high')
                <span class="px-3 py-1 bg-red-100 text-red-800 text-sm rounded-full">
                    🔥 {{ __('main.high_priority') }}
                </span>
            @endif
        </div>
    </div>

    <!-- Информация о жалобе -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Автор жалобы -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">👤 {{ __('main.report_author') }}</h2>
            
            <div class="flex items-center">
                @if($report->reporter->avatar)
                    <img src="{{ $report->reporter->avatar_url }}" 
                         alt="{{ $report->reporter->name }}" 
                         class="w-12 h-12 rounded-full object-cover mr-4">
                @else
                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white text-lg font-bold mr-4">
                        {{ strtoupper(substr($report->reporter->name, 0, 1)) }}
                    </div>
                @endif
                
                <div>
                    <a href="{{ route('profile.show', $report->reporter) }}" 
                       class="font-medium text-blue-600 hover:text-blue-800 block">
                        {{ $report->reporter->name }}
                    </a>
                    <span class="text-sm text-gray-500">
                        {{ __('main.reports_count') }}: {{ $report->reporter->reports()->count() }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Объект жалобы -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">🎯 {{ __('main.report_object') }}</h2>
            
            @if($report->reportable)
                @if($report->reportable instanceof \App\Models\Post)
                    <div class="border border-gray-200 rounded p-4">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium">{{ __('main.post_from') }} {{ $report->reportable->user->name }}</span>
                            <a href="{{ route('topics.show', [$report->reportable->topic, 'post' => $report->reportable->id]) }}#post-{{ $report->reportable->id }}" 
                               class="text-blue-600 hover:text-blue-800 text-sm">
                                {{ __('main.go_to') }} →
                            </a>
                        </div>
                        <div class="text-sm text-gray-600">
                            {{ __('main.in_topic') }}: <span class="font-medium">{{ $report->reportable->topic->title }}</span>
                        </div>
                        <div class="text-sm text-gray-700 mt-3 bg-gray-50 p-3 rounded">
                            {{ Str::limit(strip_tags($report->reportable->content), 200) }}
                        </div>
                    </div>
                @elseif($report->reportable instanceof \App\Models\Topic)
                    <div class="border border-gray-200 rounded p-4">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium">{{ __('main.topic_from') }} {{ $report->reportable->user->name }}</span>
                            <a href="{{ route('topics.show', $report->reportable) }}" 
                               class="text-blue-600 hover:text-blue-800 text-sm">
                                {{ __('main.go_to') }} →
                            </a>
                        </div>
                        <div class="text-sm font-medium text-gray-700">{{ $report->reportable->title }}</div>
                    </div>
                @endif
            @else
                <div class="text-gray-500 text-center py-4">
                    {{ __('main.content_not_available') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Описание жалобы -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">📝 {{ __('main.description') }}</h2>
        
        <div class="bg-gray-50 p-4 rounded text-gray-700">
            {{ $report->description }}
        </div>
    </div>

    @if($report->status !== 'pending')
        <!-- Решение модератора -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">
                👨‍⚖️ {{ __('main.moderator_decision') }}
            </h2>
            
            <div class="flex items-center mb-4">
                @if($report->moderator->avatar)
                    <img src="{{ $report->moderator->avatar_url }}" 
                         alt="{{ $report->moderator->name }}" 
                         class="w-8 h-8 rounded-full object-cover mr-3">
                @else
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-bold mr-3">
                        {{ strtoupper(substr($report->moderator->name, 0, 1)) }}
                    </div>
                @endif
                
                <div>
                    <span class="font-medium">{{ $report->moderator->name }}</span>
                    <div class="text-xs text-gray-500">
                        {{ $report->resolved_at->format('d.m.Y H:i') }}
                    </div>
                </div>
            </div>
            
            @if($report->moderator_notes)
                <div class="bg-blue-50 border border-blue-100 rounded p-4 text-blue-700">
                    {{ $report->moderator_notes }}
                </div>
            @endif
        </div>
    @endif

    @if($report->status === 'pending')
        <!-- Действия -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">
                🛠️ {{ __('main.actions') }}
            </h2>
            
            <div class="flex gap-3">
                <button onclick="resolveReport({{ $report->id }})" 
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors">
                    ✅ {{ __('main.resolve') }}
                </button>
                
                <button onclick="rejectReport({{ $report->id }})" 
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors">
                    ❌ {{ __('main.reject') }}
                </button>
            </div>
        </div>
    @endif
</div>

<!-- Модальные окна -->
@include('admin.reports._modals')

@push('scripts')
<script>
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

// Обработка форм решения и отклонения жалобы
document.addEventListener('DOMContentLoaded', function() {
    const resolveForm = document.getElementById('resolve-form');
    const rejectForm = document.getElementById('reject-form');
    
    if (resolveForm) {
        resolveForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(resolveForm);
            const url = resolveForm.action;
            
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Произошла ошибка при обработке жалобы');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Произошла ошибка при обработке жалобы');
            });
        });
    }
    
    if (rejectForm) {
        rejectForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(rejectForm);
            const url = rejectForm.action;
            
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Произошла ошибка при обработке жалобы');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Произошла ошибка при обработке жалобы');
            });
        });
    }
});
</script>
@endpush
@endsection 