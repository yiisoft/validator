<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\TestEnvironments\Php81\Rule;

use Yiisoft\Validator\Rule\Any;
use Yiisoft\Validator\Rule\AnyHandler;
use Yiisoft\Validator\Rule\Type\FloatType;
use Yiisoft\Validator\Rule\Type\IntegerType;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class AnyTest extends RuleTestCase
{
    public function dataValidationPassed(): array
    {
        return [
            'using as attribute' => [
                new class () {
                    #[Any([new IntegerType(), new FloatType()])]
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
                    #[Any([new IntegerType(), new FloatType()])]
                    private string $sum = '1.5';
                },
                null,
                ['sum' => [$message]],
            ],
        ];
    }
}
