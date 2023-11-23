[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/donate?business=SYC5XDT23UZ5G&no_recurring=0&item_name=Thank+you%21&currency_code=EUR)

# Restful

Restful server for Codeigniter 4

[![Build Status](https://github.com/daycry/restful/actions/workflows/php.yml/badge.svg?branch=master)](https://github.com/daycry/restful/actions/workflows/php.yml)
[![Coverage Status](https://coveralls.io/repos/github/daycry/restful/badge.svg?branch=master)](https://coveralls.io/github/daycry/restful?branch=master)
[![Downloads](https://poser.pugx.org/daycry/restful/downloads)](https://packagist.org/packages/daycry/restful)
[![GitHub release (latest by date)](https://img.shields.io/github/v/release/daycry/restful)](https://packagist.org/packages/daycry/restful)
[![GitHub stars](https://img.shields.io/github/stars/daycry/restful)](https://packagist.org/packages/daycry/restful)
[![GitHub license](https://img.shields.io/github/license/daycry/restful)](https://github.com/daycry/restful/blob/master/LICENSE)


## Installation via composer

Use the package with composer install

	> composer require daycry/restful

## Configuration

Run command:

    > php spark restful:publish
    > php spark settings:publish
    > php spark cronjob:publish
    > php spark jwt:publish

This command will copy a config file to your app namespace.
Then you can adjust it to your needs. By default file will be present in `app/Config/`.

    > php spark migrate -all

This command create restful tables in your database.


## Usage

```php
<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Daycry\RestFul\RestFul;
use Daycry\RestFul\Traits\Authenticable;

class Center extends ResourceController
{
    use RestFul;
    use Authenticable;

    public function index()
    {
        return $this->respond( $this->content );
    }
}


If you need to validate the data, you can call `validation` method passing the string rules, array of data and Validation Config file if you need.
By default load `App\Config\Validation.php` rules.

For Example: `app/Config/Validation.php` or if rules are in custom namespace `app/Modules/Example/Config/Validation.php`

```php
	public $requiredLogin = [
		'username'		=> 'required',
		'password'		=> 'required'
	];
```

```php
<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Daycry\RestFul\RestFul;
use Daycry\RestFul\Traits\Authenticable;
use Daycry\RestFul\Traits\Validation;

class Center extends ResourceController
{
    use RestFul;
    use Authenticable;
    use Validation;

    public function index()
    {
        $this->validation( 'requiredLogin', $this->content );
        return $this->respond( $this->content );
    }
}
```

**$this->content** contains a body content in the request.
**$this->args** contains all params, get, post, headers,...

If you want you can use the object **$this->request** for get this params if you want, for example, `$this->request->getPost()`

```php
<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Daycry\RestFul\RestFul;
use Daycry\RestFul\Traits\Authenticable;
use Daycry\RestFul\Traits\Validation;

class Center extends ResourceController
{
    use RestFul;
    use Authenticable;
    use Validation;

    public function index()
    {
        $this->validation( 'requiredLogin', $this->content, config( Example\\Validation ), true, true );
        return $this->respond( $this->content );
    }
}
```
Validation function parameters

Fiels | Desctiption
-------- | -----------
rule | Name of rule
data | array of data to validate
namespace| Namespace that contains the rule, `default: null`
getShared | **true** or **false**
filter | If you want to limit the content of the body, exclusively to the parameters of the rule.


#Access Filter

You can restrict requests setting scopes by access filter
```php

<?php

namespace App\Config;

$routes->group('group', ['namespace' => 'App\Controllers'], static function ($routes) {
    $routes->post('search/(:segment)', 'Search::$1', [ 'filter' => 'access:example.read' ]);
    $routes->post('auth/(:segment)', 'Auth::$1', [ 'filter' => 'access:example.auth' ]);
});
```

## User Model Class

By default you can associate users with keys via the '\Daycry\RestFul\Models\UserModel' model, but you can customize extending this class.

Example:

```php
<?php
namespace App\Models;

use CodeIgniter\Validation\ValidationInterface;
use CodeIgniter\Database\ConnectionInterface;
use Daycry\RestFul\Models\UserModel as RestFulUserModel

class UserModel extends RestFulUserModel
{
    protected $allowedFields  = [
        'username',
        'scopes'
    ];

    public function __construct(?ConnectionInterface &$db = null, ?ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);
    }

}
```

If you customize the user class, you have to modify the default configuration

Example:

```php
<?php
    public string $userProvider = \Daycry\RestFul\Models\UserModel::class;
```

## Exceptions & block Invalid Attempts

If you want to use some custom exception to use it as a failed request attempt and allow the blocking of that IP, you have to create the static attribute **authorized**.

If **authorized** is **false** the system increases by 1 the failed attempts by that IP.
Example:

```php
<?php

    namespace App\Exceptions;

    use Daycry\RestFul\Exceptions\RuntimeException;

    class CustomException extends RuntimeException
    {
        protected $code = 401;

        public static $authorized = true;

        public static function forInvalidPassphrase()
        {
            self::$authorized = false;
            return new self(lang('Secret.invalidPassphrase'));
        }

        public static function forInvalidToken()
        {
            self::$authorized = false;
            return new self(lang('Secret.invalidToken'));
        }

        public static function forExpiredToken()
        {
            self::$authorized = false;
            return new self(lang('Secret.tokenExpired'));
        }

        public static function forTokenReaded()
        {
            self::$authorized = false;
            return new self(lang('Secret.readed'));
        }
    }
```

## OPTIONS

You can customize the requests independently using the `ws_endpoints` table.

Fiels | Value | Options | Desctiption
-------- | ------------- | ------- | -----------
`controller_id` | | | This field contains the identifier of the **ws_controllers** table, this table stores the controllers of the classes, for example `\App\Controllers\Auth`
`method`| `\App\Controllers\Auth::login` | | Use this field to configure the method of controller
`auth`| `token` | `false`,`basic`,`digest`,`bearer`, `session`, `token` | Use this field to configure the autentication method
`log`| `null` | `null`,`1`,`0` | Use this field if you want log the request
`limit`| `null` | `null`,`1`,`15` | Use this field if you want to set a request limit, this value must be an integer
`time`| `null` | `null`,`1800`,`3600` | This field is used to know how often the request limit is reset ( Time in seconds Example: 3600 -> In this case you can do {limit} request in 3600 seconds)
`scope`| `null` | `null`,`auth.login`,`auth.read` | Use this field to indicate the scope in the request, you can set this value in `access filter`

You can fill the **ws_controllers** automatically with a command.

```php
<?php

    php spark restful:discover
```

This command search class in the namespace o namespaces that you want.
You can set this namespaces in the **RestFul.php** config file.

```php
<?php

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
    */
    public array $namespaceScope = ['\App\Controllers', '\Api\Controllers'];
```

Or creating a cronjob tasks editing **CronJob.php** config file like this.

```php
<?php

    /*
    |--------------------------------------------------------------------------
    | Cronjobs
    |--------------------------------------------------------------------------
    |
    | Register any tasks within this method for the application.
    | Called by the TaskRunner.
    |
    | @param Scheduler $schedule
    */
    public function init(Scheduler $schedule)
    {
        $schedule->command('restful:discover')->named('discoverRestful')->daily();
        or
        $schedule->command('restful:discover')->named('discoverRestful')->daily('11:30 am');
    }
```

More information about cronjob: https://github.com/daycry/cronjob

## RESPONSE

The default response is in `json` but you can change to `xml` in the headers.

```
Accept: application/json
```
or
```
Accept: application/xml
```


## INPUT BODY

The body of petition is `json` by default, but you can change it.

```
Content-Type: application/json
```
or
```
Content-Type: application/xml
```


## API TOKEN

You can sent `api rest token` in headers, GET or POST variable like this.
```
X-API-KEY: TOKEN
```
```
http://example.com?X-API-KEY=key
```

## LANGUAGE

You can sent `language` like this.
```
Accept-Language: en
```

