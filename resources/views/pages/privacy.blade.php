@extends('layouts.app')

@section('title', __('pages.privacy_title') . ' - ' . config('app.name'))

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ __('pages.privacy_title') }}</h1>
        <p class="text-sm text-gray-500 mb-6">{{ __('pages.privacy_updated') }}</p>
        
        <div class="prose max-w-none mb-8">
            <p class="text-lg mb-6">{{ __('pages.privacy_welcome') }}</p>
            
            <!-- Section 1 -->
            <h2 class="text-xl font-semibold text-gray-800 mt-6">{{ __('pages.privacy_section1_title') }}</h2>
            <p>{{ __('pages.privacy_section1_content') }}</p>
            <ul class="list-disc pl-6 mt-2 space-y-2">
                <li>{{ __('pages.privacy_collect1') }}</li>
                <li>{{ __('pages.privacy_collect2') }}</li>
                <li>{{ __('pages.privacy_collect3') }}</li>
                <li>{{ __('pages.privacy_collect4') }}</li>
            </ul>
            
            <!-- Section 2 -->
            <h2 class="text-xl font-semibold text-gray-800 mt-6">{{ __('pages.privacy_section2_title') }}</h2>
            <p>{{ __('pages.privacy_section2_content') }}</p>
            <ul class="list-disc pl-6 mt-2 space-y-2">
                <li>{{ __('pages.privacy_use1') }}</li>
                <li>{{ __('pages.privacy_use2') }}</li>
                <li>{{ __('pages.privacy_use3') }}</li>
                <li>{{ __('pages.privacy_use4') }}</li>
                <li>{{ __('pages.privacy_use5') }}</li>
            </ul>
            
            <!-- Section 3 -->
            <h2 class="text-xl font-semibold text-gray-800 mt-6">{{ __('pages.privacy_section3_title') }}</h2>
            <p>{{ __('pages.privacy_section3_content') }}</p>
            
            <!-- Section 4 -->
            <h2 class="text-xl font-semibold text-gray-800 mt-6">{{ __('pages.privacy_section4_title') }}</h2>
            <p>{{ __('pages.privacy_section4_content') }}</p>
            
            <!-- Section 5 -->
            <h2 class="text-xl font-semibold text-gray-800 mt-6">{{ __('pages.privacy_section5_title') }}</h2>
            <p>{{ __('pages.privacy_section5_content') }}</p>
            
            <!-- Section 6 -->
            <h2 class="text-xl font-semibold text-gray-800 mt-6">{{ __('pages.privacy_section6_title') }}</h2>
            <p>{{ __('pages.privacy_section6_content') }}</p>
            
            <!-- Section 7 -->
            <h2 class="text-xl font-semibold text-gray-800 mt-6">{{ __('pages.privacy_section7_title') }}</h2>
            <p>{{ __('pages.privacy_section7_content') }}</p>
            <ul class="list-disc pl-6 mt-2 space-y-2">
                <li>{{ __('pages.privacy_right1') }}</li>
                <li>{{ __('pages.privacy_right2') }}</li>
                <li>{{ __('pages.privacy_right3') }}</li>
                <li>{{ __('pages.privacy_right4') }}</li>
                <li>{{ __('pages.privacy_right5') }}</li>
            </ul>
            
            <!-- Section 8 -->
            <h2 class="text-xl font-semibold text-gray-800 mt-6">{{ __('pages.privacy_section8_title') }}</h2>
            <p>{{ __('pages.privacy_section8_content') }}</p>
        </div>
        
        <div class="border-t border-gray-200 pt-6 mt-6">
            <p class="italic text-gray-600">{{ __('pages.privacy_footer') }}</p>
        </div>
        
        <div class="mt-8 text-center">
            <a href="{{ route('home') }}" class="inline-block text-blue-600 hover:text-blue-800 hover:underline">
                ← {{ __('auth.back_to_home') }}
            </a>
        </div>
    </div>
</div>
@endsection 