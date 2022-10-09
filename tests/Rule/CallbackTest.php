<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\AtLeast;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\ValidationContext;

final class CallbackTest extends AbstractRuleTest
{
    public function testGetName(): void
    {
        $rule = new Callback(callback: fn() => new Result());
        $this->assertSame('callback', $rule->getName());
    }

    public function optionsDataProvider(): array
    {
        return [
            [
                new Callback(
                    static fn (mixed $value, object $rule, ValidationContext $context): Result => new Result()
                ),
                [
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Callback(
                    static fn (mixed $value, object $rule, ValidationContext $context): Result => new Result(),
                    skipOnEmpty: true
                ),
                [
                    'skipOnEmpty' => true,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    protected function getRule(): SerializableRuleInterface
    {
        return new AtLeast([]);
    }
}
