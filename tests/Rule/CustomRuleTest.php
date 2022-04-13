<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

class CustomRuleTest extends TestCase
{
    public function testUsingCommonArgumentsViaComposition()
    {
        $rule = new class () implements RuleInterface {
            private Number $baseRule;

            public function __construct()
            {
                $this->baseRule = new Number(min: -10, max: 10);
            }

            public function validate(mixed $value, ?ValidationContext $context = null): Result
            {
                return $this->baseRule->validate($value, $context);
            }
        };
        $result = $rule->validate(20);
        $expectedErrors = [new Error('Value must be no greater than 10.')];

        $this->assertEquals($expectedErrors, $result->getErrors());
    }
}
