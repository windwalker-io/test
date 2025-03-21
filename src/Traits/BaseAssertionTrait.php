<?php

declare(strict_types=1);

namespace Windwalker\Test\Traits;

use Throwable;
use Windwalker\Data\Format\PhpFormat;
use Windwalker\Utilities\Str;
use Windwalker\Utilities\TypeCast;

/**
 * StringTestTrait
 *
 * @since  {DEPLOY_VERSION}
 */
trait BaseAssertionTrait
{
    /**
     * assertStringDataEquals
     *
     * @param  string  $expected
     * @param  string  $actual
     * @param  string  $message
     *
     * @return  void
     */
    public static function assertStringDataEquals(
        string $expected,
        string $actual,
        string $message = ''
    ): void {
        static::assertEquals(
            Str::collapseWhitespaces($expected),
            Str::collapseWhitespaces($actual),
            $message
        );
    }

    /**
     * assertStringDataEquals
     *
     * @param  string  $expected
     * @param  string  $actual
     * @param  string  $message
     *
     * @return  void
     */
    public static function assertStringSafeEquals(
        string $expected,
        string $actual,
        string $message = ''
    ): void {
        static::assertEquals(
            trim(Str::replaceCRLF($expected)),
            trim(Str::replaceCRLF($actual)),
            $message
        );
    }

    /**
     * assertExpectedException
     *
     * @param  callable          $closure
     * @param  string|Throwable  $class
     * @param  string|null       $msg
     * @param  int|null          $code
     * @param  string            $message
     *
     * @return  void
     */
    public static function assertExpectedException(
        callable $closure,
        string|Throwable $class = Throwable::class,
        ?string $msg = null,
        ?int $code = null,
        string $message = ''
    ): void {
        if (is_object($class)) {
            $class = get_class($class);
        }

        try {
            $closure();
        } catch (Throwable $t) {
            static::assertInstanceOf($class, $t, $message);

            if ($msg !== null) {
                static::assertStringStartsWith($msg, $t->getMessage(), $message);
            }

            if ($code !== null) {
                static::assertEquals($code, $t->getCode(), $message);
            }

            return;
        }

        static::fail('No exception or throwable caught. expected: ' . $class);
    }

    /**
     * Asserts that two associative arrays are similar.
     *
     * Both arrays must have the same indexes with identical values
     * without respect to key ordering
     *
     * @param  array  $expected
     * @param  array  $array
     */
    public static function assertArraySimilar(array $expected, array $array, bool $useSort = false): void
    {
        if (array_is_list($expected)) {
            static::recursiveSort($expected);
            static::recursiveSort($array);
        } else {
            static::recursiveSortKeys($expected);
            static::recursiveSortKeys($array);
        }

        self::assertEquals($expected, $array);
    }

    public static function assertArraySortedSimilar(array $expected, array $array): void
    {
        self::assertArraySimilar($expected, $array, true);
    }

    public static function recursiveSortKeys(array &$array): void
    {
        ksort($array);

        foreach ($array as &$value) {
            if (is_array($value)) {
                static::recursiveSortKeys($value);
            }
        }
    }

    public static function recursiveSort(array &$array): void
    {
        sort($array);

        foreach ($array as &$value) {
            if (is_array($value)) {
                static::recursiveSort($value);
            }
        }
    }

    public static function dumpArray(mixed $array, array $options = [], bool $asString = false): ?string
    {
        $options['return'] = false;

        $export = (new PhpFormat())->dump(TypeCast::toArray($array, true), $options);

        if ($asString) {
            return $export;
        }

        echo $export;

        return null;
    }
}
