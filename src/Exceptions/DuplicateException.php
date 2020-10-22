<?php

namespace Typiqally\Enum\Exceptions;

use InvalidArgumentException;

class DuplicateException extends InvalidArgumentException
{
    public function __construct(string $class)
    {
        parent::__construct("There are duplicates in the array of $class");
    }
}
