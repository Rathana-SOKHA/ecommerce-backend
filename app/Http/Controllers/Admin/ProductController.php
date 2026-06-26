<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->latest()
            ->paginate(10);

        return view(
            'admin.products.index',
            compact('products')
        );
    }

    public function create()
    {
        $categories = Category::all();

        return view(
            'admin.products.create',
            compact('categories')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required'],
            'description' => ['nullable'],
            'price' => ['required', 'numeric'],
            'stock' => ['required', 'integer'],
            'image' => ['nullable', 'image'],
            'status' => ['nullable']
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {

            $imagePath = $request
                ->file('image')
                ->store('products', 'public');
        }

        Product::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'image' => $imagePath,
            'status' => $request->boolean('status')
        ]);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();

        return view(
            'admin.products.edit',
            compact(
                'product',
                'categories'
            )
        );
    }

    public function update(
        Request $request,
        Product $product
    ) {

        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required'],
            'description' => ['nullable'],
            'price' => ['required', 'numeric'],
            'stock' => ['required', 'integer'],
            'image' => ['nullable', 'image'],
            'status' => ['nullable']
        ]);

        $imagePath = $product->image;

        if ($request->hasFile('image')) {

            if ($product->image) {

                Storage::disk('public')
                    ->delete($product->image);
            }

            $imagePath = $request
                ->file('image')
                ->store('products', 'public');
        }

        $product->update([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'image' => $imagePath,
            'status' => $request->boolean('status')
        ]);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {

            Storage::disk('public')
                ->delete($product->image);
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}