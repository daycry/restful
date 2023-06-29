<?php

declare(strict_types=1);

namespace Daycry\RestFul\Config;

use CodeIgniter\Config\BaseConfig;
use Daycry\RestFul\Models\UserModel;
use Daycry\RestFul\Authenticators\Basic;
use Daycry\RestFul\Authenticators\Digest;
use Daycry\RestFul\Authenticators\Bearer;
use Daycry\RestFul\Authenticators\Session;
use Daycry\RestFul\Authenticators\WhiteList;
use Daycry\RestFul\Authenticators\AccessToken;
use Daycry\RestFul\Interfaces\AuthenticatorInterface;
use Daycry\RestFul\Interfaces\LibraryAuthenticatorInterface;

class RestFul extends BaseConfig
{
    /**
    * --------------------------------------------------------------------------
    * REST Realm
    * --------------------------------------------------------------------------
    *
    * Name of the password protected REST API displayed on login dialogs
    *
    * e.g: My Secret REST API
    *
    */
    public string $restRealm = 'WEB SERVICE';

    /**
     * --------------------------------------------------------------------
     * Customize Name of Tables
     * --------------------------------------------------------------------
     * Only change if you want to rename the default RestFull table names
     *
     * @var array<string, string>
     */
    public array $tables = [
        'users'                 => 'ws_users',
        'identities'            => 'ws_users_identities',
        'groups'                => 'ws_groups',
        'users_groups'          => 'ws_users_groups',
        'logs'                  => 'ws_logs',
        'apis'                  => 'ws_apis',
        'controllers'           => 'ws_controllers',
        'endpoints'             => 'ws_endpoints',
        'attemps'               => 'ws_attemps',
        'limits'                => 'ws_limits'
    ];

    public ?string $databaseGroup = null;

    /**
     * --------------------------------------------------------------------
     * Default Authenticator
     * --------------------------------------------------------------------
     * Set to specify the REST API requires to be logged in
     * you can do a mixed Authentication setting accessTokenEnabled 'true'
     *
     *  NULL     No login required
     * 'basic'   Unsecure login
     * 'digest'  More secure login
     * 'bearer'     Bearer Token
     * 'session' Check for a PHP session variable. See 'authSource' to set the authorization key
     * 'whitelist' Check and IP Address for validate
     * 'token' Api Token
     *
     * @var string
     */
    public ?string $defaultAuth = null;

    /**
     * --------------------------------------------------------------------
     * Authenticators
     * --------------------------------------------------------------------
     * The available authentication systems, listed
     * with alias and class name. These can be referenced
     * by alias in the auth helper:
     *      auth('tokens')->autenticate();
     *
     * @var array<string, class-string<AuthenticatorInterface>>
     */
    public array $authenticators = [
        'basic'     => Basic::class,
        'digest'    => Digest::class,
        'bearer'    => Bearer::class,
        'session'   => Session::class,
        'whitelist' => WhiteList::class,
        'token'     => AccessToken::class
    ];

    /**
     * --------------------------------------------------------------------
     * Auth Source
     * --------------------------------------------------------------------
     * Set to specify the REST API requires to be logged in
     *
     *  NULL     Use config based users or wildcard testing
     * 'ldap'    Use LDAP authentication
     * 'library' Use a authentication library
     *
     * If library authentication is used define the class
     *
     * For digest authentication the library function should return already a stored
     * md5(username:restrealm:password) for that username
     *
     * e.g: md5('admin:REST API:1234') = '1e957ebc35631ab22d5bd6526bd14ea2'
     *
     * @var array
     */
    public array $authSource = [
        'basic'     => null,
        'digest'    => null,
        'bearer'    => null,
        'session'   => null,
        'whitelist' => null,
        'token'     => null
    ];

    /**
     * --------------------------------------------------------------------
     * Custom Authenticators
     * --------------------------------------------------------------------
     * Set to specify library for authenticator mode
     *
     * @var array<string, class-string<LibraryAuthenticatorInterface>|null>>
     */

    public array $libraryCustomAuthenticators =
    [
        'basic'     => null,
        'digest'    => null,
        'bearer'    => null,
        'session'   => null,
        'whitelist' => null,
        'token'     => null
    ];

    /**
     * --------------------------------------------------------------------
     * User Provider
     * --------------------------------------------------------------------
     * The name of the class that handles user persistence.
     * By default, this is the included UserModel, which
     * works with any of the database engines supported by CodeIgniter.
     * You can change it as long as they adhere to the
     * Daycry\RestFul\Models\UserModel.
     *
     * @var class-string<UserModel>
     */
    public string $userProvider = UserModel::class;

    /**
     * --------------------------------------------------------------------
     * REST AJAX Only
     * --------------------------------------------------------------------
     * Set to TRUE to allow AJAX requests only. Set to FALSE to accept HTTP requests
     *
     * Hint: This is good for production environments
     */
    public bool $ajaxOnly = false;

    /**
    * --------------------------------------------------------------------------
    * CORS Allowable Methods
    * --------------------------------------------------------------------------
    *
    * If using CORS checks, you can set the methods you want to be allowed
    */
    public bool $checkCors = false;

    /**
     * --------------------------------------------------------------------------
     * Allowed HTTP headers
     * --------------------------------------------------------------------------
     *
     * Indicates which HTTP headers are allowed.
     *
     * @var array
     */
    public $allowedHeaders = ['*'];

    /**
     * --------------------------------------------------------------------------
     * Allowed HTTP methods
     * --------------------------------------------------------------------------
     *
     * Indicates which HTTP methods are allowed.
     *
     * @var array
     */
    public $allowedMethods = ['*'];

    /**
     * --------------------------------------------------------------------------
     * Allowed request origins
     * --------------------------------------------------------------------------
     *
     * Indicates which origins are allowed to perform requests.
     * Patterns also accepted, for example *.foo.com
     *
     * @var array
     */
    public $allowedOrigins = ['*'];

    /**
     * --------------------------------------------------------------------------
     * Allowed origins patterns
     * --------------------------------------------------------------------------
     *
     * Patterns that can be used with `preg_match` to match the origin.
     *
     * @var array
     */
    public $allowedOriginsPatterns = [];

    /**
     * --------------------------------------------------------------------------
     * Exposed headers
     * --------------------------------------------------------------------------
     *
     * Headers that are allowed to be exposed to the web server.
     *
     * @var array
     */
    public $exposedHeaders = [];

    /**
     * --------------------------------------------------------------------------
     * Max age
     * --------------------------------------------------------------------------
     *
     * Indicates how long the results of a preflight request can be cached.
     *
     * @var int
     */
    public $maxAge = 0;

    /**
     * --------------------------------------------------------------------------
     * Whether or not the response can be exposed when credentials are present
     * --------------------------------------------------------------------------
     *
     * Indicates whether or not the response to the request can be exposed when the
     * credentials flag is true. When used as part of a response to a preflight
     * request, this indicates whether or not the actual request can be made
     * using credentials.  Note that simple GET requests are not preflighted,
     * and so if a request is made for a resource with credentials, if
     * this header is not returned with the resource, the response
     * is ignored by the browser and not returned to web content.
     *
     * @var boolean
     */
    public $supportsCredentials = false;
    
    /**
    * --------------------------------------------------------------------------
    * Enable block Invalid Attempts
    * --------------------------------------------------------------------------
    *
    * IP blocking on consecutive failed attempts
    */
    public bool $enableInvalidAttempts = false;
    public int $maxAttempts = 10;
    public int $timeBlocked = 3600;

    /**
    * --------------------------------------------------------------------------
    * Enable BlackList
    * --------------------------------------------------------------------------
    *
    * IP blocking on consecutive failed attempts
    *
    * You can block a range of ip
    * 1. Wildcard format:     1.2.3.*
    * 2. CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
    * 3. Start-End IP format: 1.2.3.0-1.2.3.255
    *
    * e.g: ['123.456.789.0', '987.654.32.1', '1.2.3/24']
    */
    public bool $ipBlacklistEnabled = false;
    public array $ipBlacklist = [];

    /**
    * --------------------------------------------------------------------------
    * Enable WhiteList
    * --------------------------------------------------------------------------
    *
    * If you enabled this option only whitelist ip can access
    *
    * You can enable a range of ip
    * 1. Wildcard format:     1.2.3.*
    * 2. CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
    * 3. Start-End IP format: 1.2.3.0-1.2.3.255
    *
    * e.g: ['123.456.789.0', '987.654.32.1', '1.2.3/24']
    */
    public bool $ipWhitelistEnabled = false;
    public array $ipWhitelist = [];

    /**
    *--------------------------------------------------------------------------
    * Access Token
    *--------------------------------------------------------------------------
    */
    public bool $accessTokenEnabled = false;
    public string $accessTokenName = 'X-API-KEY';
    public bool $strictApiAndAuth = false; // force the use of both api and auth before a valid api request is made

    /**
     *--------------------------------------------------------------------------
     * REST Method Access Control
     * --------------------------------------------------------------------------
     * When set to TRUE, the REST API will check the access table to see if
     * the User can access that controller.
     */
    public bool $enableCheckAccess = false;

    /**
     *--------------------------------------------------------------------------
     * REST Logs
     * --------------------------------------------------------------------------
     * When set to TRUE, the REST API will check the access table to see if
     * the User can access that controller.
     */
    public bool $enableLogs = true;
    public bool $logParamsJson = true;
    public bool $logParamsEncrypt = false;

    /**
    *--------------------------------------------------------------------------
    * Cronjob
    *--------------------------------------------------------------------------
    *
    * Set to TRUE to enable Cronjob for fill the table petitions with your API classes
    * $restNamespaceScope \Namespace\Class or \Namespace\Folder\Class or \Namespace example: \App\Controllers
    *
    * This feature use Daycry\CronJob vendor
    * for more information: https://github.com/daycry/cronjob
    *
    * Ex: $namespaceScope = ['\Api\Controllers\Class', '\App\Controllers\Class'];
    */
    public array $namespaceScope = [];

    /**
     * Exclude methods in discovering
     *
     * This is useful when you use traits or the class extends the initController method
     *
     * Ex: doLogin is a Authenticable trait method and initController is a method of ResourceController class
     */
    public array $excludeMethods = ['initController', '_remap', 'doLogin'];

    /**
     *--------------------------------------------------------------------------
     * REST Method Limit Control
     * --------------------------------------------------------------------------
     * When set to TRUE, the REST API will count the number of uses of each method
     * by an API key each hour. This is a general rule that can be overridden in the
     * $this->method array in each controller
     *
     * Available methods are :
     * public string $restLimitsMethod = 'IP_ADDRESS'; // Put a limit per ip address
     * public string $restLimitsMethod = 'USER'; // Put a limit per user
     * public string $restLimitsMethod = 'METHOD_NAME'; // Put a limit on method calls
     * public string $restLimitsMethod = 'ROUTED_URL';  // Put a limit on the routed URL
     */
    public bool $enableLimit = false;
    public string $limitMethod = 'METHOD_NAME';
    public int $requestLimit = 10;
    public int $timeLimit = HOUR;

    /**
     * --------------------------------------------------------------------
     * Hashing Algorithm to use
     * --------------------------------------------------------------------
     * Valid values are
     * - PASSWORD_DEFAULT (default)
     * - PASSWORD_BCRYPT
     * - PASSWORD_ARGON2I  - As of PHP 7.2 only if compiled with support for it
     * - PASSWORD_ARGON2ID - As of PHP 7.3 only if compiled with support for it
     */
    public string $hashAlgorithm = PASSWORD_DEFAULT;

    /**
     * --------------------------------------------------------------------
     * ARGON2I/ARGON2ID Algorithm options
     * --------------------------------------------------------------------
     * The ARGON2I method of hashing allows you to define the "memory_cost",
     * the "time_cost" and the number of "threads", whenever a password hash is
     * created.
     */
    public int $hashMemoryCost = 65536; // PASSWORD_ARGON2_DEFAULT_MEMORY_COST;

    public int $hashTimeCost = 4;   // PASSWORD_ARGON2_DEFAULT_TIME_COST;
    public int $hashThreads  = 1;   // PASSWORD_ARGON2_DEFAULT_THREADS;

    /**
     * --------------------------------------------------------------------
     * BCRYPT Algorithm options
     * --------------------------------------------------------------------
     * The BCRYPT method of hashing allows you to define the "cost"
     * or number of iterations made, whenever a password hash is created.
     * This defaults to a value of 10 which is an acceptable number.
     * However, depending on the security needs of your application
     * and the power of your hardware, you might want to increase the
     * cost. This makes the hashing process takes longer.
     *
     * Valid range is between 4 - 31.
     */
    public int $hashCost = 10;
}
