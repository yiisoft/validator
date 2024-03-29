<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Base;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\RuleHandlerResolver\SimpleRuleHandlerContainer;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Validator;

abstract class RuleTestCase extends TestCase
{
    abstract public function dataValidationPassed(): array;

    /**
     * @dataProvider dataValidationPassed
     */
    public function testValidationPassed(mixed $data, array|RuleInterface|null $rules = null): void
    {
        $result = (new Validator())->validate($data, $rules);

        $this->assertSame([], $result->getErrorMessagesIndexedByPath());
    }

    abstract public function dataValidationFailed(): array;

    /**
     * @dataProvider dataValidationFailed
     */
    public function testValidationFailed(
        mixed $data,
        array|RuleInterface|null $rules,
        array $errorMessagesIndexedByPath,
        ?array $ruleHandlers = null
    ): void {
        $validator = new Validator(
            ruleHandlerResolver: $ruleHandlers === null ? null : new SimpleRuleHandlerContainer($ruleHandlers)
        );
        $result = $validator->validate($data, $rules);

        $this->assertFalse($result->isValid());
        $this->assertSame($errorMessagesIndexedByPath, $result->getErrorMessagesIndexedByPath());
    }
}
