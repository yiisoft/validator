<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Compare;
use Yiisoft\Validator\Rule\CompareHandler;
use Yiisoft\Validator\Tests\Support\ValidatorFactory;
use Yiisoft\Validator\Tests\Support\RuleWithCustomHandler;
use Yiisoft\Validator\Validator;

final class CompareTest extends TestCase
{
    public function testDifferentRuleInHandler(): void
    {
        $rule = new RuleWithCustomHandler(CompareHandler::class);
        $validator = $this->createValidator();

        $this->expectExceptionMessageMatches(
            '/.*' . preg_quote(Compare::class) . '.*' . preg_quote(RuleWithCustomHandler::class) . '.*/'
        );
        $validator->validate([], [$rule]);
    }

    private function createValidator(): Validator
    {
        return ValidatorFactory::make();
    }
}
