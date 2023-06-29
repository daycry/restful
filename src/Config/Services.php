<?php

declare(strict_types=1);

namespace Daycry\RestFul\Config;

use Config\Services as BaseService;
use CodeIgniter\HTTP\UserAgent;
use Config\App;
use Daycry\RestFul\Auth;
use Daycry\RestFul\Libraries\IncomingRequest;
use CodeIgniter\HTTP\CLIRequest;
use Daycry\RestFul\Libraries\Logger;
use Daycry\RestFul\Libraries\Authentication;
use Daycry\RestFul\Libraries\Passwords;
use Daycry\RestFul\Libraries\Cors;

class Services extends BaseService
{
    /**
     * Returns the current Request object.
     *
     * createRequest() injects IncomingRequest or CLIRequest.
     *
     * @return CLIRequest|IncomingRequest
     *
     * @deprecated The parameter $config and $getShared are deprecated.
     */
    public static function request(?App $config = null, bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('request', $config);
        }

        // @TODO remove the following code for backward compatibility
        return static::incomingrequest($config, $getShared);
    }

    /**
     * The IncomingRequest class models an HTTP request.
     *
     * @return IncomingRequest
     *
     * @internal
     */
    public static function incomingrequest(?App $config = null, bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('request', $config);
        }

        $config ??= config('App');

        return new IncomingRequest(
            $config,
            self::uri(),
            'php://input',
            new UserAgent()
        );
    }

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
     * The restful log class
     */
    public static function log(bool $getShared = true): Logger
    {
        if ($getShared) {
            return self::getSharedInstance('log');
        }

        helper('checkEndpoint');

        return new Logger(checkEndpoint());
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

    public static function cors(?RestFul $config = null, bool $getShared = true)
    {
        $config ??= config('RestFul');

        if ($getShared) {
            return static::getSharedInstance('cors', $config);
        }

        return new Cors($config);
    }
}
