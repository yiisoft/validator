<?php

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\Url;
use Yiisoft\Validator\Rule\Uuid;
use Yiisoft\Validator\Rule\UuidHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

class UuidTest extends RuleTestCase {
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void {
        $rule = new Uuid();
        $this->assertSame(Uuid::class, $rule->getName());
    }

    /**
     * @return string[]
     */
    protected function getDifferentRuleInHandlerItems(): array {
        return [Uuid::class, UuidHandler::class];
    }

    /**
     * @return array[]
     */
    public function dataValidationPassed(): array {
        return [
            ['0193ba64-5ef5-7ba3-90c1-2915349a60d3', [new Uuid()]],
            ['89ffcfc5-d452-4eb9-8949-d35fb577f09d', [new Uuid()]],

            ['289cef4c-b873-11ef-9cd2-0242ac120002', [new Uuid()]],
        ];
    }

    public function dataValidationFailed(): array {
        $errors = ['' => ['The value of value does not conform to the UUID format.']];

        return [
            ['not uuid value', [new Uuid()], $errors],
            ['ea20aba6-1fb2-45de-8582-6ed15f94501', [new Uuid()], $errors],
        ];
    }

    public function dataOptions(): array {
        return [
            [new Uuid(), []],
        ];
    }

    public function testSkipOnError(): void {
        $this->testSkipOnErrorInternal(new Url(), new Url(skipOnError: true));
    }

    /**
     * @return void
     */
    public function testWhen(): void {
        $when = static fn(mixed $value): bool => $value !== null;
        $this->testWhenInternal(new Uuid(), new Uuid(when: $when));
    }
}
