<?php

declare(strict_types=1);

namespace Daycry\RestFul\Entities\Cast;

use CodeIgniter\Entity\Cast\BaseCast;

/**
 * Serialize Cast
 */
final class SerializeCast extends BaseCast
{
    /**
     * @param string $value
     */
    public static function get($value, array $params = []): ?array
    {
        if($value) {
            return unserialize($value);
        }
        return null;
    }

    /**
     * @param bool|int|string $value
     */
    public static function set($value, array $params = []): ?string
    {
        if($value) {
            return serialize($value);
        }
        return null;
    }
}
