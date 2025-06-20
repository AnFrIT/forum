@extends('layouts.app')

@section('title', __('pages.terms_title') . ' - ' . config('app.name'))

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ __('pages.terms_title') }}</h1>
        <p class="text-sm text-gray-500 mb-6">{{ __('pages.terms_updated') }}</p>
        
        <div class="prose max-w-none mb-8">
            <p class="text-lg mb-6">{{ __('pages.terms_welcome') }}</p>
            
            <!-- Section 1 -->
            <h2 class="text-xl font-semibold text-gray-800 mt-6">{{ __('pages.terms_section1_title') }}</h2>
            <p>{{ __('pages.terms_section1_content') }}</p>
            
            <!-- Section 2 -->
            <h2 class="text-xl font-semibold text-gray-800 mt-6">{{ __('pages.terms_section2_title') }}</h2>
            <p>{{ __('pages.terms_section2_content') }}</p>
            <ul class="list-disc pl-6 mt-2 space-y-2">
                <li>{{ __('pages.terms_rule1') }}</li>
                <li>{{ __('pages.terms_rule2') }}</li>
                <li>{{ __('pages.terms_rule3') }}</li>
                <li>{{ __('pages.terms_rule4') }}</li>
                <li>{{ __('pages.terms_rule5') }}</li>
                <li>{{ __('pages.terms_rule6') }}</li>
            </ul>
            
            <!-- Section 3 -->
            <h2 class="text-xl font-semibold text-gray-800 mt-6">{{ __('pages.terms_section3_title') }}</h2>
            <p>{{ __('pages.terms_section3_content') }}</p>
            
            <!-- Section 4 -->
            <h2 class="text-xl font-semibold text-gray-800 mt-6">{{ __('pages.terms_section4_title') }}</h2>
            <p>{{ __('pages.terms_section4_content') }}</p>
            
            <!-- Section 5 -->
            <h2 class="text-xl font-semibold text-gray-800 mt-6">{{ __('pages.terms_section5_title') }}</h2>
            <p>{{ __('pages.terms_section5_content') }}</p>
            
            <!-- Section 6 -->
            <h2 class="text-xl font-semibold text-gray-800 mt-6">{{ __('pages.terms_section6_title') }}</h2>
            <p>{{ __('pages.terms_section6_content') }}</p>
        </div>
        
        <div class="border-t border-gray-200 pt-6 mt-6">
            <p class="italic text-gray-600">{{ __('pages.terms_footer') }}</p>
        </div>
        
        <div class="mt-8 text-center">
            <a href="{{ route('home') }}" class="inline-block text-blue-600 hover:text-blue-800 hover:underline">
                ← {{ __('auth.back_to_home') }}
            </a>
        </div>
    </div>
</div>
@endsection 