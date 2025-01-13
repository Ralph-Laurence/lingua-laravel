<?php

namespace App\Http\Utils;

use App\Models\FieldNames\UserFields;
use App\Models\User;
use Hashids\Hashids;

//#! This is a custom class and did NOT came from chatify installation
class Helper
{
    /**
     * Add a prefix to each item in the haystack
     */
    public static function prependFields($prefix, $haystack, $implode = false, $implodeChar = ',')
    {
        $data = array_map(function($needle) use($prefix)
        {
            return $prefix.$needle;
        },
        $haystack);

        if ($implode)
            return implode($implodeChar, $data);

        return $data;
    }
}
