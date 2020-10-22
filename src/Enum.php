<?php

namespace Typiqally\Enum;

use BadMethodCallException;
use JsonSerializable;
use ReflectionClass;
use ReflectionException;
use Typiqally\Enum\Exceptions\DuplicateException;
use Typiqally\Enum\Exceptions\UnexpectedTypeException;
use Typiqally\Enum\Exceptions\EnumException;

abstract class Enum implements JsonSerializable
{
    private static array $cache = [];

    /** @var string|int */
    protected $key;

    /** @var mixed */
    protected $value;

    /**
     * @param string|int $key
     *
     * @throws ReflectionException
     * @throws UnexpectedTypeException
     * @internal
     */
    public function __construct(string $key)
    {
        if (!is_string($key) && !is_int($key)) {
            throw new UnexpectedTypeException("Only string and integer keys allowed.");
        }

        $constant = $this->findConstant($key);

        if ($constant === null) {
            throw new BadMethodCallException("Constant $key is not defined, consider adding it in the documentation definition.");
        }

        $this->key = $constant->key;
        $this->value = $constant->value;
    }

    /**
     * @return array
     *
     * @throws ReflectionException
     * @throws UnexpectedTypeException
     */
    public static function toArray(): array
    {
        $result = [];
        foreach (static::resolveConstant() as $constant) {
            $result[$constant->key] = self::declare($constant->key);
        }

        return $result;
    }

    /**
     * @param string|int $key
     *
     * @return static
     *
     * @throws ReflectionException
     * @throws UnexpectedTypeException
     */
    public static function declare($key): Enum
    {
        return new static($key);
    }

    /**
     * @param string $name
     * @param array $arguments
     *
     * @return static
     *
     * @throws ReflectionException
     * @throws UnexpectedTypeException
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return static::declare($name);
    }

    /**
     * @return string[]|int[]
     */
    protected static function keys(): array
    {
        return [];
    }

    /**
     * @return object[]
     */
    protected static function values(): array
    {
        return [];
    }

    /**
     * @return EnumConstant[]
     *
     * @throws ReflectionException
     */
    private static function resolveConstant(): array
    {
        $className = static::class;

        if (static::$cache[$className] ?? null) {
            return static::$cache[$className];
        }

        $class = new ReflectionClass($className);
        $comment = $class->getDocComment();

        preg_match_all('/@method static self ([\w_]+)\(\)/', $comment, $matches);

        $constant = [];

        $keys = static::keys();
        $values = static::values();

        foreach ($matches[1] as $methodName) {
            $key = $keys[$methodName] = $keys[$methodName] ?? $methodName;
            $value = $values[$methodName] = $values[$methodName] ?? $methodName;

            $constant[$methodName] = new EnumConstant($methodName, $key, $value);
        }

        if (self::arrayHasDuplicates($keys) || self::arrayHasDuplicates($values)) {
            throw new DuplicateException(static::class);
        }

        return static::$cache[$className] ??= $constant;
    }

    private static function arrayHasDuplicates(array $array): bool
    {
        return count($array) > count(array_unique($array, SORT_REGULAR));
    }

    /**
     * @param string $name
     *
     * @return mixed
     *
     * @throws EnumException
     */
    public function __get(string $name)
    {
        if ($name === 'key') {
            return $this->key;
        }

        if ($name === 'value') {
            return $this->value;
        }

        throw new EnumException("Property $name is not supported");
    }

    /**
     * @param string $name
     * @param array $arguments
     *
     * @return bool
     * @throws ReflectionException
     * @throws UnexpectedTypeException
     * @throws EnumException
     */
    public function __call(string $name, array $arguments)
    {
        if (strpos($name, 'is') === 0) {
            $other = static::declare(substr($name, 2));

            return $this->equals($other);
        }

        throw new EnumException("Method $name not found");
    }

    public function equals(Enum ...$enums): bool
    {
        foreach ($enums as $enum) {
            if (get_class($this) === get_class($enum) && $this->key === $enum->key) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->value;
    }

    /**
     * @param string|int $key
     *
     * @return EnumConstant|null
     *
     * @throws ReflectionException
     */
    private function findConstant($key): ?EnumConstant
    {
        foreach (static::resolveConstant() as $constant) {
            if ($constant->equals($key)) {
                return $constant;
            }
        }

        return null;
    }
}
