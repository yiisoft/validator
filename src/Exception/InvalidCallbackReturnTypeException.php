<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Exception;

use Exception;
use Throwable;
use Yiisoft\FriendlyException\FriendlyExceptionInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\CallbackHandler;

/**
 * An exception used by the handler ({@see CallbackHandler}) of {@see Callback} rule for the cases when returned value
 * is not a {@see Result} instance.
 */
final class InvalidCallbackReturnTypeException extends Exception implements FriendlyExceptionInterface
{
    public function __construct(
        /**
         * @var mixed The actual wrong value returned from a callback.
         */
        mixed $returnValue,
        /**
         * @var Throwable|null The previous throwable used for the exception chaining.
         */
        ?Throwable $previous = null,
    ) {
        $message = sprintf(
            'Return value of callback must be an instance of "%s", "%s" returned.',
            Result::class,
            get_debug_type($returnValue),
        );

        parent::__construct($message, 0, $previous);
    }

    /**
     * @return string Human understandable exception name.
     */
    public function getName(): string
    {
        return 'Invalid callable return value';
    }

    /**
     * @return string|null Suggestion on how to fix the exception.
     */
    public function getSolution(): ?string
    {
        return <<<SOLUTION
The callback must return an instance of `\Yiisoft\Validator\Result`. An example of a valid callback:

```php
use Yiisoft\Validator\Result;

static function (mixed \$value): Result
{
    \$result = new Result();

    if (!in_array(\$value, [7, 42], true)) {
        \$result->addError('Value must be 7 or 42.');
    }

    return \$result;
}
```
SOLUTION;
    }
}
