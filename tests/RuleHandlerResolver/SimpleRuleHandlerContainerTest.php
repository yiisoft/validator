<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\RuleHandlerResolver;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Exception\RuleHandlerInterfaceNotImplementedException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleHandlerResolver\SimpleRuleHandlerContainer;
use Yiisoft\Validator\ValidationContext;

final class SimpleRuleHandlerContainerTest extends TestCase
{
    public function testInvalidInstance(): void
    {
        $this->expectException(RuleHandlerInterfaceNotImplementedException::class);
        $this->expectExceptionMessage('Expected instance of "Yiisoft\Validator\RuleHandlerInterface". Got "int".');
        new SimpleRuleHandlerContainer(['my-rule' => 72]);
    }

    public function testPredefinedHandler(): void
    {
        $handler = new class() implements RuleHandlerInterface {
            public function validate(mixed $value, object $rule, ValidationContext $context): Result
            {
                return new Result();
            }
        };

        $container = new SimpleRuleHandlerContainer(['test-handler' => $handler]);

        $this->assertSame($handler, $container->resolve('test-handler'));
    }
}
