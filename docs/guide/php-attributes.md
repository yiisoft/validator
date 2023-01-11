#### Using attributes

##### Basic usage

Common flow is the same as you would use usual classes:

1. Declare property.
2. Add rules to it.

```php
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;

final class Chart
{
    #[Each([
        new Nested(Point::class),
    ])]
    private array $points;
}

final class Point
{
    #[Nested(Coordinates::class)]
    private $coordinates;
    #[Count(exactly: 3)]
    #[Each([
        new Number(min: 0, max: 255),
    ])]
    private array $rgb;
}

final class Coordinates
{
    #[Number(min: -10, max: 10)]
    private int $x;
    #[Number(min: -10, max: 10)]
    private int $y;
}
```

Here are some technical details:

- In case of a flat array `Point::$rgb`, a property type `array` needs to be declared.

Pass object directly to `validate()` method:

```php
use Yiisoft\Validator\ValidatorInterface;

// Usually obtained from container
$validator = $container->get(ValidatorInterface::class);

$coordinates = new Coordinates();
$errors = $validator->validate($coordinates)->getErrorMessagesIndexedByPath();
```

##### Traits

Traits are supported too:

```php
use Yiisoft\Validator\Rule\HasLength;

trait TitleTrait
{
    #[HasLength(max: 255)]
    private string $title;
}

final class Post
{
    use TitleTrait;
}
```

##### Callbacks

`Callback::$callback` property is not supported, also you can't use `callable` type with attributes. However,
`Callback::$method` can be set instead:

```php
<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\ValidationContext;

final class Author
{
    #[Callback(method: 'validateName')]
    private string $name;

    public static function validateName(mixed $value, object $rule, ValidationContext $context): Result
    {
        $result = new Result();
        if ($value !== 'foo') {
            $result->addError('Value must be "foo"!');
        }

        return $result;
    }
}
```

Note that the method must exist and have public and static modifiers.

##### Limitations

###### Nested attributes

PHP 8.0 supports attributes, but nested declaration is allowed only in PHP 8.1 and above.

So attributes such as `Each`, `Nested` and `Composite` are not allowed in PHP 8.0.

The following example is not allowed in PHP 8.0:

```php
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Number;

final class Color
{
    #[Each([
        new Number(min: 0, max: 255),
    ])]
    private array $values;
}
```

But you can do this by creating a new composite rule from it.

```php
namespace App\Validator\Rule;

use Attribute;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Composite;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class RgbRule extends Composite
{
    public function getRules(): array
    {
        return [
            new Each([
                new Number(min: 0, max: 255),
            ]),
        ];
    }
}
```

And use it after as attribute.

```php
use App\Validator\Rule\RgbRule;

final class Color
{
    #[RgbRule]
    private array $values;
}

```

###### Function / method calls

You can't use a function / method call result with attributes. This problem can be overcome either with custom rule or
`Callback::$method` property. An example of custom rule:

```php
use Attribute;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\RuleInterface
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\ValidationContext;

final class CustomFormatter implements FormatterInterface
{
    public function format(string $message, array $parameters = []): string
    {
        // More complex logic
        // ...
        return $message;
    }
}

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class ValidateXRule implements RuleInterface
{
    public function __construct(
        private $value,
    ) {
    }
    
    public function getValue()
    {
        return $this->value;
    }
}

final class Coordinates
{
    #[Number(min: -10, max: 10)]
    #[ValidateXRule()]
    private int $x;
    #[Number(min: -10, max: 10)]
    private int $y;
}
```

###### Passing instances

If you have PHP >= 8.1, you can utilize passing instances in attributes' scope. Otherwise, again fallback to custom
rules approach described above.

```php
use Yiisoft\Validator\Rule\HasLength;

final class Post
{
    #[HasLength(max: 255, formatter: new CustomFormatter())]
    private string $title;
}
```
