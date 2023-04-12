<?php

declare(strict_types=1);

namespace Daycry\RestFul\Libraries;

/**
 * Class Passwords
 *
 * Provides a central location to handle password
 * related tasks like hashing, verifying, validating, etc.
 */
class Passwords
{
    /**
     * Hash a password.
     *
     * @param string $password
     *
     * @return false|string|null
     */
    public function hash(string $password)
    {
        return password_hash($password, service('settings')->get('RestFul.hashAlgorithm'), $this->getHashOptions());
    }

    private function getHashOptions(): array
    {
        if (
            (defined('PASSWORD_ARGON2I') && service('settings')->get('RestFul.hashAlgorithm') === PASSWORD_ARGON2I)
            || (defined('PASSWORD_ARGON2ID') && service('settings')->get('RestFul.hashAlgorithm') === PASSWORD_ARGON2ID)
        ) {
            return [
                'memory_cost' => service('settings')->get('RestFul.hashMemoryCost'),
                'time_cost'   => service('settings')->get('RestFul.hashTimeCost'),
                'threads'     => service('settings')->get('RestFul.hashThreads'),
            ];
        }

        return [
            'cost' => service('settings')->get('RestFul.hashCost'),
        ];
    }

    /**
     * Verifies a password against a previously hashed password.
     *
     * @param string $password The password we're checking
     * @param string $hash     The previously hashed password
     */
    public function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Checks to see if a password should be rehashed.
     */
    public function needsRehash(string $hashedPassword): bool
    {
        return password_needs_rehash($hashedPassword, service('settings')->get('RestFul.hashAlgorithm'), $this->getHashOptions());
    }
}
