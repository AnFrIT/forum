@extends('layouts.app')

@section('title', __('main.edit_category') . ': ' . $category->name)

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 fw-bold text-primary">{{ __('main.edit_category') }}: {{ $category->name }}</h1>
                <p class="text-muted mb-0">{{ __('main.edit_category_desc') }}</p>
            </div>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> {{ __('main.back') }}
            </a>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="row g-3">
                @csrf
                @method('PUT')
                
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="name" class="form-label fw-bold">{{ __('main.name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control form-control-lg @error('name') is-invalid @enderror" 
                               value="{{ old('name', $category->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="parent_id" class="form-label fw-bold">{{ __('main.parent_category') }}</label>
                        <select name="parent_id" id="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                            <option value="">-- {{ __('main.no_parent_category') }} --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="order" class="form-label fw-bold">{{ __('main.sort_order') }}</label>
                        <input type="number" name="order" id="order" class="form-control @error('order') is-invalid @enderror" 
                               value="{{ old('order', $category->order) }}" min="0" required>
                        @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">{{ __('main.sort_order_help') }}</small>
                    </div>
                    
                    <div class="form-group mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" 
                                   value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">
                                {{ __('main.active_category') }}
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="description" class="form-label fw-bold">{{ __('main.description') }}</label>
                        <textarea name="description" id="description" rows="5" 
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-primary">{{ __('main.category_information') }}</h5>
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span>{{ __('main.created') }}:</span>
                                <span class="fw-bold">{{ format_date($category->created_at) }}</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span>{{ __('main.topics_in_category') }}:</span>
                                <span class="badge bg-primary rounded-pill">{{ $category->topics_count }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-2">
                                <span>{{ __('main.posts') }}:</span>
                                <span class="badge bg-info rounded-pill">{{ $category->posts_count }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 mt-4">
                    <hr>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle me-1"></i> {{ __('main.save') }}
                        </button>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i> {{ __('main.cancel') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection