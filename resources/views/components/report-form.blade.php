<!-- Форма для отправки жалобы -->
<div id="report-form" class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: false }" x-show="open" style="display: none;">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="open = false" x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
        
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full z-10 p-6" 
             x-show="open" 
             @click.away="open = false"
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 transform scale-95" 
             x-transition:enter-end="opacity-100 transform scale-100" 
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="opacity-100 transform scale-100" 
             x-transition:leave-end="opacity-0 transform scale-95">
            
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-800">{{ __('main.report_content') }}</h3>
                <button type="button" @click="open = false" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form method="POST" action="{{ route('reports.store') }}">
                @csrf
                
                <input type="hidden" name="reportable_type" id="reportable_type">
                <input type="hidden" name="reportable_id" id="reportable_id">
                
                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">{{ __('main.reason') }}</label>
                    <select name="reason" id="reason" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">{{ __('main.select_reason') }}...</option>
                        <option value="spam">{{ __('main.spam_advertising') }}</option>
                        <option value="offensive">{{ __('main.offensive_content') }}</option>
                        <option value="offtopic">{{ __('main.offtopic') }}</option>
                        <option value="rules">{{ __('main.rules_violation') }}</option>
                        <option value="other">{{ __('main.other') }}</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">{{ __('main.describe_issue') }}</label>
                    <textarea name="description" id="description" rows="4" required
                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                              placeholder="{{ __('main.describe_violation_detail') }}"></textarea>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" @click="open = false"
                            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                        {{ __('main.cancel') }}
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-white bg-red-600 rounded-md hover:bg-red-700 transition-colors">
                        {{ __('main.send_report') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Скрипт для открытия формы -->
<script>
    function openReportForm(type, id) {
        const form = document.getElementById('report-form');
        document.getElementById('reportable_type').value = type;
        document.getElementById('reportable_id').value = id;
        
        // Используем Alpine.js для управления модальным окном
        if (window.Alpine) {
            const modal = Alpine.$data(form);
            if (modal) {
                modal.open = true;
            } else {
                console.error('Alpine data not found for report form');
            }
        } else {
            console.error('Alpine.js not initialized');
            // Fallback для случаев, когда Alpine.js не загружен
            form.style.display = 'block';
        }
    }
</script> 