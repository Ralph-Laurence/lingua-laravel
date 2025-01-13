<?php

namespace App\Http\Utils;

use App\Models\FieldNames\UserFields;
use App\Models\User;
use Hashids\Hashids;

//#! This is a custom class and did NOT came from chatify installation
class ChatifyUtils
{
    private static $hashid = null;

    /**
     * This function changes Chatify-specific user attributes
     * into its SignLingua user attributes equivalent
     */
    public static function wrapUserProps($user)
    {
        // if (property_exists($user, 'name'))
        // {

        // }

        $user->name = implode(' ', [$user->{UserFields::Firstname}, $user->{UserFields::Lastname}]);
        $user->avatar = User::getPhotoUrl($user->{UserFields::Photo});

        return $user;
    }

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

    public static function getHashidInstance() {

        if (self::$hashid == null)
            self::$hashid = new Hashids(HashSalts::Chat, 10);

        return self::$hashid;
    }

    public static function toHashedChatId($rawId)
    {
        $hashid = self::getHashidInstance();
        return $hashid->encode($rawId);
    }

    public static function toRawChatId($hashedId)
    {
        $hashid = self::getHashidInstance();
        return $hashid->decode($hashedId)[0];
    }
}
