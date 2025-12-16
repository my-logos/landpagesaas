<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

/**
 * Trait for managing user resources (Products, Pages, Orders)
 * Provides common index and create methods with locale handling
 */
trait ManagesUserResources
{
    /**
     * Get locale data from the trait
     */
    protected function getResourceLocaleData()
    {
        return $this->getLocaleData();
    }

    /**
     * Get user's resources with pagination or full list
     */
    protected function getUserResources(Request $request, string $modelClass, bool $paginate = false)
    {
        $query = $modelClass::where('user_id', $request->user()->id);

        return $paginate ? $query->paginate(15) : $query->get();
    }

    /**
     * Count user's resources
     */
    protected function countUserResources(Request $request, string $modelClass): int
    {
        return $modelClass::where('user_id', $request->user()->id)->count();
    }

    /**
     * Prepare common view data for index pages
     */
    protected function prepareIndexViewData(Request $request, array $resources, array $additionalData = []): array
    {
        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getResourceLocaleData();

        $resourceKey = count($resources) > 0 ? array_key_first(get_object_vars($resources[0])) : 'items';
        $viewData = array_merge(
            compact('resources', 'locale', 'dir', 't'),
            $additionalData
        );

        return $viewData;
    }
}
