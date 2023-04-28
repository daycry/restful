<?php

declare(strict_types=1);

namespace Daycry\RestFul\Libraries;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Daycry\Encryption\Encryption;
use Daycry\RestFul\Entities\Endpoint;
use CodeIgniter\Debug\Timer;
use Daycry\RestFul\Models\LogModel;
use Daycry\RestFul\Interfaces\BaseException;

class Logger
{
    protected LogModel $logModel;

    protected RequestInterface $request;

    protected ResponseInterface $response;

    protected bool $logAuthorized = false;

    protected bool $authorized = true;

    protected Timer $benchmark;

    protected int $responseCode = 0;

    protected int $insertId = 0;

    public function __construct(?Endpoint $endpoint)
    {
        $this->benchmark = Services::timer();
        $this->benchmark->start('restful');

        $this->logModel = new LogModel();
        $this->request = Services::request();
        $this->response = Services::response();

        if (
            (is_null($endpoint) && service('settings')->get('RestFul.enableLogs') == true) ||
            (service('settings')->get('RestFul.enableLogs') == true && (!is_null($endpoint) && is_null($endpoint->log))) ||
            (!is_null($endpoint) && $endpoint->log)
        ) {
            $this->logAuthorized = true;
        }
    }

    public function setLogAuthorized(bool $logAuthorized): self
    {
        $this->logAuthorized = $logAuthorized;

        return $this;
    }

    public function setAuthorized(bool $authorized): self
    {
        $this->authorized = $authorized;

        return $this;
    }

    public function setResponseCode(int $responseCode): self
    {
        $this->responseCode = $responseCode;

        return$this;
    }

    public function save(): int
    {
        if($this->logAuthorized) {
            $params = $this->request->getAllParams();

            $params = $params ? (service('settings')->get('RestFul.logParamsJson') == true ? \json_encode($params) : \serialize($params)) : null;
            $params = ($params != null && service('settings')->get('RestFul.logParamsEncrypt') == true) ? (new Encryption())->encrypt($params) : $params;

            $this->response = Services::response();

            $this->benchmark->stop('restful');

            //If authenticator not exists
            try {
                $userId = auth()->id();
            } catch(BaseException $ex) {
                $userId = null;
            }

            $data = [
                'user_id'   => $userId,
                'uri'        => $this->request->uri,
                'method'     => $this->request->getMethod(),
                'params'     => $params,
                'ip_address' => $this->request->getIPAddress(),
                'duration'   => $this->benchmark->getElapsedTime('restful'),
                'response_code' => $this->responseCode,
                'authorized' => $this->authorized,
            ];

            $this->logModel->save($data);
            $this->insertId = $this->logModel->getInsertID();
        }

        return $this->insertId;
    }
}
