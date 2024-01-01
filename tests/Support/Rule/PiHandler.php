<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Rule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

use function abs;

final class PiHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        $result = new Result();

        if (!(abs($value - M_PI) < PHP_FLOAT_EPSILON)) {
            $result->addError('The value must be Pi.');
        }

        return $result;
    }
}
