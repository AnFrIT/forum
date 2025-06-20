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
                <textarea name="moderator_notes" id="resolve_response" rows="3"
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
                <textarea name="moderator_notes" id="reject_response" rows="3" required
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