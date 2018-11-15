<?php

namespace Sleekcube\AdminGenerator\Helpers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use GuzzleHttp;
use Laravel\Passport\Client;

class TokenManager
{
    const PERSONAL = 1;

    public static function fetchOrUpdateToken (User $user, $type)
    {
        if (
            $user->token_expiration === null ||
            Carbon::createFromFormat('Y-m-d H:i:s', $user->token_expiration)->lessThan(Carbon::now())
        ) {
            switch ($type) {
                case self::PERSONAL:
                    $token = $user->createToken('admin_token')->accessToken;
                    self::savePersonalAccessToken($token, $user);
                    break;

            }

            return true;
        }

        return false;
    }

    public static function savePersonalAccessToken ($token, User $user)
    {
        $user->api_token = $token;
        $user->save();
    }

    public static function saveAccessToken ($token, User $user)
    {
        $user->api_token = $token['access_token'];
        $user->refresh_token = $token['refresh_token'];
        $user->token_expiration = Carbon::now()->addSeconds($token['expires_in']);
        $user->save();
    }
}
