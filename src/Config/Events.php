<?php

namespace Daycry\RestFul\Config;

use CodeIgniter\Events\Events;
use CodeIgniter\Config\Services;
use Daycry\RestFul\Models\LogModel;
use Daycry\RestFul\Exceptions\BaseException;
use Daycry\Encryption\Encryption;

Events::on('pre_system', static function () {
    $benchmark = Services::timer();
    $benchmark->start('restful');
});

Events::on('post_system', static function () {

    helper(['auth']);

    if(service('settings')->get('RestFul.enableLogs')) {
        $request = Services::request();
        $benchmark = Services::timer();
        $response = Services::response();

        try {
            $userId = auth()->id();
        } catch(BaseException $ex) {
            $userId = null;
        }

        $benchmark->stop('restful');

        //get header vars
        $headers = array_map(
            function ($header) {
                return $header->getValueLine();
            },
            $request->headers()
        );

        if($request->getJson()) {
            $body = array_merge($request->getJson(), $request->getRawInput());
        } else {
            $body = $request->getRawInput();
        }

        $params = array_merge($headers, $request->getGetPost(), $body);
        $params = ($params) ? $params : null;

        if($params) {
            $params = (service('settings')->get('RestFul.logParamsJson')) ? \json_encode($params) : \serialize($params);
            if(service('settings')->get('RestFul.logParamsEncrypt')) {
                $params = (new Encryption())->encrypt($params);
            }
        }

        //array_merge($headers,$request->getGetPost(), $body)
        $data = [
            'user_id' => $userId,
            'uri'       => (string) $request->getUri(),
            'method'    => $request->getMethod(),
            'params'    => $params,
            'ip_address' => $request->getIPAddress(),
            'duration'   => $benchmark->getElapsedTime('restful'),
            'response_code' => $response->getStatusCode()
        ];

        $logModel = new LogModel();
        $logModel->insert($data);
    }
});
