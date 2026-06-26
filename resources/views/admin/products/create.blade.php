@extends('admin.layouts.app')

@section('content')

<div class="admin-card-header">
    <h1 class="admin-page-title" style="margin-bottom: 0;">Create Product</h1>
</div>

<div class="admin-card">

    <form
        action="{{ route('products.store') }}"
        method="POST"
        enctype="multipart/form-data"
    >

        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">

            <div class="admin-form-group">
                <label class="admin-form-label" for="category_id">Category</label>
                <select id="category_id" name="category_id" class="admin-form-input">
                    <option value="">Select category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label" for="name">Product Name</label>
                <input id="name" type="text" name="name" class="admin-form-input" placeholder="Enter product name">
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label" for="price">Price ($)</label>
                <input id="price" type="number" step="0.01" name="price" class="admin-form-input" placeholder="0.00">
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label" for="stock">Stock</label>
                <input id="stock" type="number" name="stock" class="admin-form-input" placeholder="0">
            </div>

        </div>

        <div class="admin-form-group">
            <label class="admin-form-label" for="description">Description</label>
            <textarea id="description" name="description" class="admin-form-input" rows="4" placeholder="Enter product description"></textarea>
        </div>

        <div class="admin-form-group">
            <label class="admin-form-label" for="image">Product Image</label>
            <input id="image" type="file" name="image" class="admin-form-input" accept="image/*" onchange="previewImage(this, 'image-preview-create')">
            <p class="admin-form-hint" id="image-name-create" style="display: none; margin-top: 6px; font-weight: 500; color: #0d9488;"></p>
        </div>

        <div class="admin-form-group" id="image-preview-create-wrapper" style="display: none;">
            <label style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Image Preview</label>
            <img id="image-preview-create" class="admin-image-preview" src="#" alt="Preview">
        </div>

        <div class="admin-form-group">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 12px 16px; background: #f0fdfa; border-radius: 10px; border: 1px solid #ccfbf1;">
                <input type="checkbox" name="status" checked style="width: 18px; height: 18px; accent-color: #0d9488;">
                <span style="font-weight: 600; color: #0f766e;">Active</span>
                <span style="font-size: 13px; color: #64748b; margin-left: 4px;">— visible to customers</span>
            </label>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 8px;">
            <button type="submit" class="admin-btn admin-btn-primary">
                Create Product
            </button>
            <a href="{{ route('products.index') }}" class="admin-btn admin-btn-secondary">
                Cancel
            </a>
        </div>

    </form>

</div>

<script>
    function previewImage(input, previewId) {
        var preview = document.getElementById(previewId);
        var previewWrapper = document.getElementById(previewId + '-wrapper');
        var nameLabel = document.getElementById('image-name-create');

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                previewWrapper.style.display = 'block';
                nameLabel.textContent = 'Selected: ' + input.files[0].name;
                nameLabel.style.display = 'block';
            };

            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '#';
            previewWrapper.style.display = 'none';
            nameLabel.style.display = 'none';
        }
    }
</script>

@endsection
