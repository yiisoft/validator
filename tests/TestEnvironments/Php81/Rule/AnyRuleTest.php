<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\TestEnvironments\Php81\Rule;

use Yiisoft\Validator\Rule\AnyRule;
use Yiisoft\Validator\Rule\Type\FloatType;
use Yiisoft\Validator\Rule\Type\IntegerType;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;

final class AnyRuleTest extends RuleTestCase
{
    public function dataValidationPassed(): array
    {
        return [
            'using as attribute' => [
                new class () {
                    #[AnyRule([new IntegerType(), new FloatType()])]
                    private int|float $sum = 1.5;
                },
                null,
            ],
        ];
    }

    public function dataValidationFailed(): array
    {
        $message = 'At least one of the inner rules must pass the validation.';

        return [
            'using as attribute' => [
                new class () {
                    #[AnyRule([new IntegerType(), new FloatType()])]
                    private string $sum = '1.5';
                },
                null,
                ['sum' => [$message]],
            ],
        ];
    }
}
