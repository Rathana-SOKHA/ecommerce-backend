@extends('admin.layouts.app')

@section('content')

<div class="admin-card-header">
    <h1 class="admin-page-title" style="margin-bottom: 0;">Edit Category</h1>
</div>

<div class="admin-card">

    <form action="{{ route('categories.update',$category) }}" method="POST">

        @csrf
        @method('PUT')

        <div class="admin-form-group">
            <label class="admin-form-label" for="name">Category Name</label>
            <input id="name" type="text" name="name" class="admin-form-input" value="{{ old('name',$category->name) }}" placeholder="Enter category name">
            @error('name')
                <p class="admin-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="admin-form-group">
            <label class="admin-form-label" for="description">Description</label>
            <textarea id="description" name="description" class="admin-form-input" rows="4" placeholder="Enter category description">{{ old('description',$category->description) }}</textarea>
            @error('description')
                <p class="admin-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="admin-form-group">
            <label class="admin-toggle-label">
                <input type="checkbox" name="status" {{ $category->status ? 'checked' : '' }}>
                <span>Active</span>
                <span class="admin-toggle-hint">— visible in storefront</span>
            </label>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 8px;">
            <button type="submit" class="admin-btn admin-btn-primary">
                Update Category
            </button>
            <a href="{{ route('categories.index') }}" class="admin-btn admin-btn-secondary">
                Cancel
            </a>
        </div>

    </form>

</div>

@endsection
