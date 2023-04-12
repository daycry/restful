<?php

declare(strict_types=1);

namespace Daycry\RestFul\Config;

use Config\Services as BaseService;
use Daycry\RestFul\Auth;
use Daycry\RestFul\Libraries\Authentication;
use Daycry\RestFul\Libraries\Passwords;

class Services extends BaseService
{
    /**
     * The base auth class
     */
    public static function auth(bool $getShared = true): Auth
    {
        if ($getShared) {
            return self::getSharedInstance('auth');
        }

        return new Auth(new Authentication());
    }

    /**
     * Password utilities.
     */
    public static function passwords(bool $getShared = true): Passwords
    {
        if ($getShared) {
            return self::getSharedInstance('passwords');
        }

        return new Passwords();
    }
}
