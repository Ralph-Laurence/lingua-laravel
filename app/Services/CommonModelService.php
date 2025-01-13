<?php

namespace App\Services;

use Illuminate\Http\Request;

class CommonModelService
{
    public function createRequestFilterRules(Request $request, array $availableFilters)
    {
        $options = [];
        foreach ($availableFilters as $filter => $default)
        {
            $options[$filter] = $request->input($filter, $default);
        }
        return $options;
    }

    /**
     * This function checks if filters have been applied to a request
     */
    public function areFiltersApplied(array $options, array $availableFilters)
    {
        foreach ($availableFilters as $filter => $default)
        {
            if ($options[$filter] !== $default) {
                return true;
            }
        }
        return false;
    }
}
