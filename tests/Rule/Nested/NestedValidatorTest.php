<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Nested;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\HasLength\HasLength;
use Yiisoft\Validator\Rule\InRange\InRange;
use Yiisoft\Validator\Rule\Nested\Nested;
use Yiisoft\Validator\Rule\Nested\NestedValidator;
use Yiisoft\Validator\Rule\Number\Number;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\Tests\Rule\AbstractRuleValidatorTest;

/**
 * @group t4
 */
final class NestedValidatorTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $value = [
            'author' => [
                'name' => 'Dmitry',
                'age' => 18,
            ],
        ];

        return [
            'error' => [
                new Nested(['author.age' => [new Number(min: 20)]]),
                $value,
                [new Error('Value must be no less than {min}.', ['min' => 20])],
            ],
            'key not exists' => [
                new Nested(['author.sex' => [new InRange(['male', 'female'])]]),
                $value,
                [new Error('This value is invalid.', [])],
            ],
        ];
    }

    public function passedValidationProvider(): array
    {
        $value = [
            'author' => [
                'name' => 'Dmitry',
                'age' => 18,
            ],
        ];

        return [
            'success' => [
                new Nested(['author.name' => [new HasLength(min: 3)]]),
                $value,
            ],
            'key not exists, skip empty' => [
                new Nested(['author.sex' => [new InRange(['male', 'female'], skipOnEmpty: true)]]),
                $value,
            ],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
        ];
    }

    protected function getValidator(): RuleValidatorInterface
    {
        return new NestedValidator();
    }

    protected function getConfigClassName(): string
    {
        return Nested::class;
    }
}
