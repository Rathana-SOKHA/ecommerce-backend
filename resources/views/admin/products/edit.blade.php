@extends('admin.layouts.app')

@section('content')

<div class="admin-card-header">
    <h1 class="admin-page-title" style="margin-bottom: 0;">Edit Product</h1>
</div>

<div class="admin-card">

    <form
        action="{{ route('products.update',$product) }}"
        method="POST"
        enctype="multipart/form-data"
    >

        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">

            <div class="admin-form-group">
                <label class="admin-form-label" for="category_id">Category</label>
                <select id="category_id" name="category_id" class="admin-form-input">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label" for="name">Product Name</label>
                <input id="name" type="text" name="name" class="admin-form-input" value="{{ $product->name }}" placeholder="Enter product name">
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label" for="price">Price ($)</label>
                <input id="price" type="number" step="0.01" name="price" class="admin-form-input" value="{{ $product->price }}" placeholder="0.00">
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label" for="stock">Stock</label>
                <input id="stock" type="number" name="stock" class="admin-form-input" value="{{ $product->stock }}" placeholder="0">
            </div>

        </div>

        <div class="admin-form-group">
            <label class="admin-form-label" for="description">Description</label>
            <textarea id="description" name="description" class="admin-form-input" rows="4" placeholder="Enter product description">{{ $product->description }}</textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">

            <div class="admin-form-group">
                <label class="admin-form-label">Current Image</label>
                @if($product->image)
                    <img src="{{ asset('storage/'.$product->image) }}" style="width: 100%; max-width: 180px; border-radius: 10px; border: 2px solid #e2e8f0; display: block;">
                @else
                    <div style="width: 100%; max-width: 180px; height: 120px; background: #f1f5f9; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 13px; border: 2px dashed #e2e8f0;">
                        No image
                    </div>
                @endif
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label" for="new_image">Replace Image</label>
                <input id="new_image" type="file" name="image" class="admin-form-input" accept="image/*" onchange="previewImage(this, 'image-preview-edit')">
                <p class="admin-form-hint" id="image-name-edit" style="display: none; margin-top: 6px; font-weight: 500; color: #0d9488;"></p>
            </div>

            <div class="admin-form-group" id="image-preview-edit-wrapper" style="display: none;">
                <label style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">New Image Preview</label>
                <img id="image-preview-edit" class="admin-image-preview" src="#" alt="Preview">
            </div>

        </div>

        <div class="admin-form-group">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 12px 16px; background: #f0fdfa; border-radius: 10px; border: 1px solid #ccfbf1;">
                <input type="checkbox" name="status" {{ $product->status ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: #0d9488;">
                <span style="font-weight: 600; color: #0f766e;">Active</span>
                <span style="font-size: 13px; color: #64748b; margin-left: 4px;">— visible to customers</span>
            </label>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 8px;">
            <button type="submit" class="admin-btn admin-btn-primary">
                Update Product
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
        var nameLabel = document.getElementById('image-name-edit');

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
