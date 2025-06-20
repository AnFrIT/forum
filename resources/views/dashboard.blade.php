@extends('layouts.app')

@section('title', __('main.dashboard') . ' - ' . config('app.name'))

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                {{ __('main.logged_in_success') }}
            </div>
        </div>
    </div>
</div>
@endsection