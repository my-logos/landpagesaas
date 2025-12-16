<?php

namespace App\Services;

use App\Models\SubscriptionPackage;
use App\Models\Page;
use App\Models\User;
use App\Models\Order;

class LandingPageService
{
    /**
     * Get all packages ordered by price
     */
    public function getPackages()
    {
        return SubscriptionPackage::orderBy('price_cents', 'asc')->get();
    }

    /**
     * Get landing page statistics
     */
    public function getStatistics(): array
    {
        $totalPages = Page::count();
        $totalUsers = $this->getTotalUsers();
        $totalSalesCents = $this->getTotalSales();

        return [
            'total_pages' => $this->formatNumber($totalPages),
            'total_visitors' => $this->formatNumber($totalUsers * 1000), // Approximation
            'total_sales' => $this->formatNumber($totalSalesCents / 100), // Convert cents to currency
            'total_clients' => $this->formatNumber($totalUsers),
        ];
    }

    /**
     * Get total users (excluding admins)
     */
    protected function getTotalUsers(): int
    {
        return User::where(function ($query) {
            $query->where('role', '!=', 'admin')->orWhereNull('role');
        })->count();
    }

    /**
     * Get total sales from delivered orders
     */
    protected function getTotalSales(): int
    {
        return Order::where('status', 'delivered')->sum('total_cents') ?? 0;
    }

    /**
     * Format number for display (e.g., 6800 -> "6.8K")
     */
    protected function formatNumber($number): string
    {
        if ($number >= 1000000) {
            return number_format($number / 1000000, 1) . 'M';
        } elseif ($number >= 1000) {
            return number_format($number / 1000, 1) . 'K';
        }
        return number_format($number);
    }
}
