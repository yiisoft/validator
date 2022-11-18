<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Exception;

use Throwable;
use Yiisoft\FriendlyException\FriendlyExceptionInterface;
use Yiisoft\Validator\Result;

final class InvalidCallbackReturnTypeException extends \Exception implements FriendlyExceptionInterface
{
    public function __construct(mixed $result, int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf(
            'Return value of callback must be an instance of %s, %s returned.',
            Result::class,
            get_debug_type($result)
        );

        parent::__construct($message, $code, $previous);
    }

    /**
     * @codeCoverageIgnore Called in "error-handler" package.
     */
    public function getName(): string
    {
        return 'Invalid callable return value';
    }

    /**
     * @codeCoverageIgnore Called in "error-handler" package.
     */
    public function getSolution(): ?string
    {
        return <<<SOLUTION
            The callback must return an instance of \\Yiisoft\\Validator\\Result. An example of a valid callback:
                static function (): \\Yiisoft\\Validator\\Result
                {
                    \$result = new \\Yiisoft\\Validator\\Result();

                    ...

                    return \$result;
                }
        SOLUTION;
    }
}
