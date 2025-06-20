{{-- resources/views/partials/popular-categories.blade.php --}}

<div class="bg-white rounded-lg shadow-lg p-6">
    <h3 class="text-lg font-semibold text-gray-700 mb-4">
        <span class="mr-2">🔥</span>{{ __('main.popular_categories') }}
    </h3>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @if(isset($popularCategories) && $popularCategories->count() > 0)
            @foreach($popularCategories as $category)
                <a href="{{ route('categories.show', $category) }}" 
                   class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-left group">
                    <div class="font-medium text-gray-800 group-hover:text-blue-600">{{ $category->name }}</div>
                    <div class="flex justify-between items-center mt-2">
                        <div class="text-sm text-gray-600">{{ $category->topics_count ?? 0 }} {{ __('main.topics_count') }}</div>
                        <div class="text-xs text-gray-500">{{ $category->posts_count ?? 0 }} {{ __('main.posts_count') }}</div>
                    </div>
                </a>
            @endforeach
        @else
            <div class="col-span-full text-center text-gray-500 py-8">
                <p class="mb-2">{{ __('main.popular_categories') }} {{ __('main.not_defined_yet') }}</p>
                <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    {{ __('main.visit_home_page') }}
                </a>
            </div>
        @endif
    </div>
</div> 