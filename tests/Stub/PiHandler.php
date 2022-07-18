<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

final class PiHandler implements RuleHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        $result = new Result();

        if (!(\abs($value - M_PI) < PHP_FLOAT_EPSILON)) {
            $result->addError('Value must be Pi.', ['value' => $value]);
        }

        return $result;
    }
}
