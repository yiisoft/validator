<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use Yiisoft\Validator\Rules;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

class RulesTest extends TestCase
{
    public function testMethodSyntax(): void
    {
        $rules = new Rules();
        $rules->add(new Required());
        $rules->add((new Number())->max(10));

        $result = $rules->validate(42);
        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrors());
    }

    public function testArraySyntax(): void
    {
        $rules = new Rules(
            [
                new Required(),
                (new Number())->max(10)
            ]
        );

        $result = $rules->validate(42);
        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrors());
    }

    public function testCallback(): void
    {
        $rules = new Rules(
            [
                static function ($value): Result {
                    $result = new Result();
                    if ($value !== 42) {
                        $result->addError('Value should be 42!');
                    }
                    return $result;
                }
            ]
        );

        $result = $rules->validate(41);
        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrors());
    }

    public function testWhenValidate()
    {
        $rules = new Rules(
            [
                (new Number())->min(10),
                (new Number())->min(10)->when(fn () => false)->skipOnError(false),
                (new Number())->min(10)->skipOnError(false)
            ]
        );

        $result = $rules->validate(1);

        $this->assertFalse($result->isValid());
        $this->assertCount(2, $result->getErrors());
    }

    public function testSkipOnError()
    {
        $rules = new Rules(
            [
                (new Number())->min(10),
                (new Number())->min(10)->skipOnError(false),
                (new Number())->min(10)
            ]
        );

        $result = $rules->validate(1);

        $this->assertFalse($result->isValid());
        $this->assertCount(2, $result->getErrors());
    }
}
