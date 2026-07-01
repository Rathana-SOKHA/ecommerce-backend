@extends('admin.layouts.app')

@section('content')

<div class="admin-card-header" style="justify-content: space-between; margin-bottom: 20px;">
    <h1 class="admin-page-title" style="margin-bottom: 0;">Products</h1>
    <a href="{{ route('products.create') }}"
       class="admin-btn admin-btn-primary">
        Add Product
    </a>
</div>

@if(session('success'))
    <div class="admin-alert admin-alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="admin-card" style="padding: 0; overflow: hidden;">

    <table class="admin-table">

        <thead>
            <tr>
                <th>#</th>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>

        @foreach($products as $product)

            <tr>

                <td style="font-weight: 600; color: var(--color-gray-900);">{{ $loop->iteration }}</td>

                <td>

                    @if($product->image)

                        <img
                            src="{{ asset('storage/'.$product->image) }}"
                            width="70"
                            height="70"
                            style="border-radius: 8px; object-fit: cover; object-position: center;">

                    @endif

                </td>

                <td>{{ $product->name }}</td>

                <td>{{ $product->category->name }}</td>

                <td>${{ number_format($product->price,2) }}</td>

                <td>{{ $product->stock }}</td>

                <td>

                    <a
                        href="{{ route('products.edit',$product) }}"
                        class="admin-btn admin-btn-primary admin-btn-sm"
                        style="display: inline-flex;">
                        Edit
                    </a>

                    <form
                        id="delete-form-{{ $product->id }}"
                        action="{{ route('products.destroy',$product) }}"
                        method="POST"
                        style="display: inline;">

                        @csrf
                        @method('DELETE')

                        <button
                            type="button"
                            onclick="openDeleteModal('delete-form-{{ $product->id }}')"
                            class="admin-btn admin-btn-danger admin-btn-sm">
                            Delete
                        </button>

                    </form>

                </td>

            </tr>

        @endforeach

        </tbody>

    </table>

</div>

{{ $products->links('vendor.pagination.admin') }}

<!-- Delete Confirm Modal -->
<div id="delete-modal" class="admin-modal-overlay" style="display: none;">

    <div class="admin-modal-box">

        <div class="admin-modal-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                <line x1="10" y1="11" x2="10" y2="17"></line>
                <line x1="14" y1="11" x2="14" y2="17"></line>
            </svg>
        </div>

        <h3 class="admin-modal-title">Delete Product</h3>
        <p class="admin-modal-text">Are you sure you want to delete this product? This action cannot be undone.</p>

        <div class="admin-modal-actions">
            <button class="admin-btn admin-btn-secondary" onclick="closeDeleteModal()">
                Cancel
            </button>
            <button type="button"
                    class="admin-btn admin-btn-danger"
                    onclick="submitDeleteForm()">
                Yes, Delete
            </button>
        </div>

    </div>

</div>

<script>
    var deleteTargetForm = null;

    function openDeleteModal(formId) {
        deleteTargetForm = document.getElementById(formId);
        document.getElementById('delete-modal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('delete-modal').style.display = 'none';
        deleteTargetForm = null;
    }

    function submitDeleteForm() {
        if (deleteTargetForm) {
            deleteTargetForm.submit();
        }
    }

    document.getElementById('delete-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
</script>

@endsection
