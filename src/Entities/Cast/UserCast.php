<?php

declare(strict_types=1);

namespace Daycry\RestFul\Entities\Cast;

use CodeIgniter\Entity\Cast\BaseCast;
use Daycry\RestFul\Entities\User;
use Daycry\RestFul\Exceptions\CastException;

/**
 * User Cast
 */
final class UserCast extends BaseCast
{
    /**
     * @param string $value
     */
    public static function get($value, array $params = []): array
    {
        $userProvider = model(service('settings')->get('RestFul.userProvider'));
        return $userProvider->where('id', $value)->first();
    }

    /**
     * @param bool|int|string $value
     */
    public static function set($value, array $params = []): string
    {
        if($value instanceof User) {
            throw CastException::forInvalidObject();
        }

        return $value->id;
    }
}
