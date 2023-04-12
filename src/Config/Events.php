<?php

namespace Daycry\RestFul\Config;

use CodeIgniter\Events\Events;
use CodeIgniter\Config\Services;
use Daycry\RestFul\Models\UserModel;

/*Events::on('pre_system', static function () {
    $benchmark = Services::timer();
    $benchmark->start('restful');
});

Events::on('post_system', static function () {
    helper(['auth']);

    $session = Services::session();
    $benchmark = Services::timer();

    $createUser = service('settings')->get('RestFul.createUser');
    $user = \unserialize($session->get('restFulUser'));

    if ($user && !$user->id && $createUser) {
        $user = (new UserModel())->save($user);
    }

    $benchmark->stop('restful');

    $elapsedTime =  $benchmark->getElapsedTime('restful');
    $response = Services::response();
    $session->destroy();
});*/
