<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $totalCategories = Category::count();
        $totalProducts = Product::count();
        $totalUsers = User::count();
        $totalOrders = Order::count();

        $recentUsers = User::latest()
            ->take(5)
            ->get();

        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view(
            'admin.dashboard.index',
            compact(
                'totalCategories',
                'totalProducts',
                'totalUsers',
                'totalOrders',
                'recentUsers',
                'recentOrders'
            )
        );
    }
}