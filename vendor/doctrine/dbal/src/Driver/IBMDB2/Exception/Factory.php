<?php

declare (strict_types=1);
namespace WappoVendor\Doctrine\DBAL\Driver\IBMDB2\Exception;

use WappoVendor\Doctrine\DBAL\Driver\AbstractException;
use function preg_match;
/**
 * @internal
 *
 * @psalm-immutable
 */
final class Factory
{
    /**
     * @param callable(int): T $constructor
     *
     * @return T
     *
     * @template T of AbstractException
     */
    public static function create(string $message, callable $constructor) : AbstractException
    {
        $code = 0;
        if (preg_match('/ SQL(\\d+)N /', $message, $matches) === 1) {
            $code = -(int) $matches[1];
        }
        return $constructor($code);
    }
}
