<?php

declare(strict_types=1);

namespace Daycry\RestFul\Authenticators;

use Daycry\RestFul\Interfaces\AuthenticatorInterface;
use Daycry\RestFul\Models\UserModel;
use Daycry\RestFul\Entities\User;
use Daycry\RestFul\Exceptions\AuthenticationException;

class Digest extends Base implements AuthenticatorInterface
{
    public function __construct(UserModel $provider)
    {
        $this->method = 'digest';
        $this->provider = $provider;
        parent::__construct();
    }

    public function check(): ?User
    {
        $digest_string = $this->request->getServer('PHP_AUTH_DIGEST');
        if ($digest_string === null) {
            $digest_string = $this->request->getHeaderLine('authorization');
        }

        $unique_id = uniqid();

        $digest_string = $digest_string . '';
        if (empty($digest_string)) {
            $this->forceLogin($unique_id);
        }

        $matches = [];
        preg_match_all('@(username|nonce|uri|nc|cnonce|qop|response)=[\'"]?([^\'",]+)@', $digest_string, $matches);
        $digest = (empty($matches[1]) || empty($matches[2])) ? [] : array_combine($matches[1], $matches[2]);

        $digest['username'] = isset($digest['username']) ? $digest['username'] : null;

        $username = $this->checkLogin($digest['username']);

        if (isset($digest['username']) == false || $username == false) {
            $this->forceLogin($unique_id);
        }

        $md5 = md5(strtoupper($this->request->getMethod()) . ':' . $digest['uri']);

        $valid_response = md5($username.':'.$digest['nonce'].':'.$digest['nc'].':'.$digest['cnonce'].':'.$digest['qop'].':'.$md5);

        if (strcasecmp($digest['response'], $valid_response) !== 0) {
            throw AuthenticationException::forInvalidCredentials();
        }

        $this->user = $this->findUser($digest['username']);

        return $this->user;
    }

    protected function attempt(string $authMethod, string $username, ?string $password = null)
    {
        $user = $this->findUser($username);
        $identity = $user->getIdentity($authMethod);
        return $identity->secret;
    }
}
