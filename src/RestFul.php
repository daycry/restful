<?php

namespace Daycry\RestFul;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Config\Services;
use CodeIgniter\Router\Router;
use Daycry\Encryption\Encryption;
use Daycry\RestFul\Entities\Endpoint;
use Daycry\RestFul\Validators\Cors;
use Daycry\RestFul\Validators\Attempt;
use Daycry\RestFul\Validators\Blacklist;
use Daycry\RestFul\Validators\Whitelist;
use Daycry\RestFul\Validators\Limit;
use Daycry\RestFul\Libraries\Logger;
use Daycry\RestFul\Interfaces\BaseException;
use Daycry\RestFul\Exceptions\ForbiddenException;
use Daycry\RestFul\Models\AttemptModel;
use Config\Mimes;
use ReflectionProperty;
use stdClass;

trait RestFul
{
    /**
     * @var Router $router
     */
    protected Router $router;

    /**
     * @var Encryption $encryption
     */
    protected Encryption $encryption;

    /**
     * @var Logger $_logger
     */
    private Logger $_logger;

    /**
     * @var Endpoint|null $override
     */
    protected ?Endpoint $override;

    /**
     * The authorization Request
     *
     * @var bool
     */
    private bool $_isRequestAuthorized = true;

    /**
     * The arguments from GET, POST, PUT, DELETE, PATCH, HEAD and OPTIONS request methods combined.
     *
     * @var array
     */
    protected array $args = [];

    /**
     * The body of request.
     *
     * @var mixed
     */
    protected mixed $content = [];

    /**
    * Extend this function to apply additional checking early on in the process.
    *
    * @return void
    */
    protected function earlyChecks(): void
    {
    }

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        helper(['security', 'checkEndpoint', 'auth', 'restFulLog']);

        $this->_logger = restFulLog();

        parent::initController($request, $response, $logger);

        $this->router = Services::router();
        $this->encryption = new Encryption();

        $this->override = checkEndpoint();

        if(method_exists($this, 'setFormat')) {
            $output = $this->request->negotiate('media', config('Format')->supportedResponseFormats);
            $output = Mimes::guessExtensionFromType($output);
            $this->setFormat($output);
        }

        $this->args = $this->request->getAllParams();
        $this->content = (!empty($this->args['body'])) ? $this->args['body'] : new stdClass();

        // Extend this function to apply additional checking early on in the process
        $this->earlyChecks();
    }

    /**
     * De-constructor.
     *
     * @return void
     */
    public function __destruct()
    {
        if ($this->request) {
            $this->_logRequest();

            if (service('settings')->get('RestFul.enableInvalidAttempts') == true) {
                $attemptModel = new AttemptModel();
                $attempt = $attemptModel->where('ip_address', $this->request->getIPAddress())->first();

                if ($this->_isRequestAuthorized === false) {
                    if ($attempt === null) {
                        $attempt = [
                                'ip_address' => $this->request->getIPAddress(),
                                'attempts'      => 1,
                                'hour_started' => time(),
                            ];

                        $attemptModel->save($attempt);
                    } else {
                        if ($attempt->attempts < service('settings')->get('RestFul.maxAttempts')) {
                            $attempt->attempts = $attempt->attempts + 1;
                            $attempt->hour_started = time();
                            $attemptModel->save($attempt);
                        }
                    }
                } else {
                    if ($attempt) {
                        $attemptModel->delete($attempt->id, true);
                    }
                }
            }
        }

        //reset previous validation at end
        if ($this->validator) {
            $this->validator->reset();
        }
    }

    /**
     * Requests are not made to methods directly, the request will be for
     * an "object". This simply maps the object and method to the correct
     * Controller method.
     *
     * @param string $method
     * @param array  $params     The params passed to the controller method
     *
     * @throws BaseException
     */
    public function _remap($method, ...$params)
    {
        try {
            if (config('App')->forceGlobalSecureRequests && $this->request->isSecure() === false) {
                // @codeCoverageIgnoreStart
                throw ForbiddenException::forUnsupportedProtocol();
                // @codeCoverageIgnoreEnd
            }

            if ($this->request->isAJAX() === false && service('settings')->get('RestFul.ajaxOnly')) {
                throw ForbiddenException::forOnlyAjax();
            }

            if (service('settings')->get('RestFul.checkCors') == true) {
                Cors::check($this->response);
            }

            if (service('settings')->get('RestFul.enableInvalidAttempts') == true) {
                Attempt::check($this->response);
            }

            if (service('settings')->get('RestFul.ipBlacklistEnabled') == true) {
                Blacklist::check($this->response);
            }

            if (service('settings')->get('RestFul.ipWhitelistEnabled') == true) {
                Whitelist::check($this->response);
            }

            $alias = (isset($this->override->auth) && $this->override->auth) ? $this->override->auth : service('settings')->get('RestFul.defaultAuth');

            if(method_exists($this, 'doLogin')) {
                $this->doLogin($this->override);
            }

            if(service('settings')->get('RestFul.enableLimit')) {
                Limit::check($this->override);
            }

            if (!method_exists($this, $method)) {
                throw ForbiddenException::forInvalidMethod($this->router->methodName());
            }

            return call_user_func_array([ $this, $method ], $params);

        } catch (BaseException $ex) {

            if(property_exists($ex, 'authorized')) {
                $this->_isRequestAuthorized = (new ReflectionProperty($ex, 'authorized'))->getValue();
            }

            $message = ($this->validator && $this->validator->getErrors()) ? $this->validator->getErrors() : $ex->getMessage();

            if ($ex->getCode()) {
                return $this->fail($message, $ex->getCode());
            } else {
                return $this->fail($message);
            }
        }
    }

    /**
     * Add the request to the log table.
     */
    protected function _logRequest()
    {
        $this->_logger
            ->setAuthorized($this->_isRequestAuthorized)
            ->setResponseCode($this->response->getStatusCode())
            ->save();
    }
}
