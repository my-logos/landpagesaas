<?php

namespace App\Http\Controllers\Admin;

use App\Models\Page;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;

class PagesController extends BaseAdminController
{
    public function index(Request $request)
    {
        $query = $this->buildPagesQuery($request);
        $pages = $query->orderBy('created_at', 'desc')->paginate(20);
        $filterData = $this->getFilterData();

        return view('admin.pages.index', $this->getViewData(array_merge(
            compact('pages'),
            $filterData
        )));
    }

    /**
     * Build pages query with filters
     */
    protected function buildPagesQuery(Request $request)
    {
        $query = Page::with(['user', 'product']);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        return $query;
    }

    /**
     * Get filter data (users and products)
     */
    protected function getFilterData(): array
    {
        return [
            'users' => User::where('role', '!=', 'admin')
                ->orWhereNull('role')
                ->orderBy('name')
                ->get(),
            'products' => Product::with('user')
                ->orderBy('name')
                ->get(),
        ];
    }

    public function show(Page $page)
    {
        $page->load(['user', 'product']);
        $pageUrl = $this->getPageUrl($page);

        return view('admin.pages.show', $this->getViewData(compact('page', 'pageUrl')));
    }

    /**
     * Get page URL
     */
    protected function getPageUrl(Page $page): string
    {
        return url('/page/' . $page->id);
    }
}
