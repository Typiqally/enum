# PHP Enum Types

In the current version of PHP, `enum` types are not supported. This package will introduce the `enum` type into PHP. The use-case is built around Java Enum Types. An enum type is a special data type that enables for a variable to be a set of predefined constants. The variable must be equal to one of the values that have been predefined for it.

## Note
This project is currently under development and still in an alpha stage.

## Installation
The package can be installed by executing the following command:

```
composer require typiqally/enum
```

## Basic
Basic enums can be created like this:

```php
use Typiqally\Enum\Enum;

/**
 * @method static self NORTH()
 * @method static self EAST()
 * @method static self SOUTH()
 * @method static self WEST()
 */
class Direction extends Enum
{
}
```

### Usage
And this is how they are used:

```php
public function walk(Direction $direction): void {
    //Walk logic
}

$this->walk(Direction::NORTH());
```

## Values
Enum constants can have a `mixed` value, this means it can have dynamic values like Java Enum Types. Values are assigned like this:

```php
use Typiqally\Enum\Enum;

/**
 * @method static self NORTH()
 * @method static self EAST()
 * @method static self SOUTH()
 * @method static self WEST()
 */
class Direction extends Enum
{
    protected static function values(): array
    {
        return [
            'NORTH' => [
                'degrees' => 0 | 360
            ],
            'EAST' => [
                'degrees' => 90
            ],
            'SOUTH' => [
                'degrees' => 180
            ],
            'WEST' => [
                'degrees' => 270
            ]
        ];
    }

    public function getDegrees(): int
    {
        return $this->value['degrees'];
    }
}
```

### Usage
And this is how they are used:

```php
public function walk(Direction $direction): void {
    $degrees = $direction->getDegrees();
}

$this->walk(Direction::NORTH());
```

## License
This project is licensed under the GPL-3.0 license. See [LICENSE.md](https://github.com/Typiqally/enum/blob/master/LICENSE.md) for the full license text.
