<?php

namespace Typiqally\Enum;

class EnumConstant
{
    public string $key;

    /** @var mixed */
    public $value;

    private string $methodName;

    /**
     * @param string $methodName
     * @param string $key
     * @param mixed $value
     */
    public function __construct(string $methodName, string $key, $value)
    {
        $this->methodName = strtolower($methodName);
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @param string $input
     *
     * @return bool
     */
    public function equals(string $input): bool
    {
        if ($this->value === $input) {
            return true;
        }

        if (is_string($input) && $this->methodName === strtolower($input)) {
            return true;
        }

        return false;
    }
}
