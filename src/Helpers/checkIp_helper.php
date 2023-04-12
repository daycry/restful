<?php

declare(strict_types=1);

use Daycry\RestFul\Libraries\CheckIpInRange;

if (! function_exists('checkIp')) {
    /**
     * Provides a valid IP for actual request
     *
     * @param string $ip IP
     * @param array $ips Ips
     */
    function checkIp(string $ip, array $ips): bool
    {
        $return = false;

        foreach ($ips as $i) {
            if (strpos($i, '/') !== false || strpos($i, '-') !== false || strpos($i, '*') !== false) {
                $return = CheckIpInRange::ipv4_in_range($ip, $i);
            } elseif ($ip === trim($i)) {
                $return = true;
            }

            if ($return) {
                break;
            }
        }

        return $return;
    }
}
