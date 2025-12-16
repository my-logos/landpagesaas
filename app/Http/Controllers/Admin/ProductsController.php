<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class ProductsController extends BaseAdminController
{
    public function index(Request $request)
    {
        $query = $this->buildProductsQuery($request);
        $products = $query->withCount('orders')->orderBy('created_at', 'desc')->paginate(20);
        $users = $this->getUsersForFilter();

        return view('admin.products.index', $this->getViewData(compact('products', 'users')));
    }

    /**
     * Build products query with filters
     */
    protected function buildProductsQuery(Request $request)
    {
        $query = Product::with(['user', 'orders']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        return $query;
    }

    /**
     * Get users for filter dropdown
     */
    protected function getUsersForFilter()
    {
        return User::where('role', '!=', 'admin')
            ->orWhereNull('role')
            ->orderBy('name')
            ->get();
    }

    public function show(Product $product)
    {
        $product->load(['user', 'orders']);
        $productStats = $this->getProductStats($product);

        return view('admin.products.show', $this->getViewData(array_merge(
            compact('product'),
            $productStats
        )));
    }

    /**
     * Get product statistics
     */
    protected function getProductStats(Product $product): array
    {
        return [
            'ordersCount' => $product->orders()->count(),
            'totalRevenue' => $product->orders()->where('status', 'delivered')->sum('total_cents') ?? 0,
        ];
    }
}
