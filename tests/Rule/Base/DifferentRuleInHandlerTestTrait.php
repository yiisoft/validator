<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Base;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Tests\Support\Rule\RuleWithCustomHandler;
use Yiisoft\Validator\Validator;

trait DifferentRuleInHandlerTestTrait
{
    public function testDifferentRuleInHandler(): void
    {
        [$ruleClassName, $ruleHandlerClassName] = $this->getDifferentRuleInHandlerItems();

        $rule = new RuleWithCustomHandler($ruleHandlerClassName);
        $validator = new Validator();

        $this->expectException(UnexpectedRuleException::class);
        $this->expectExceptionMessage(
            'Expected "' . $ruleClassName . '", but "' . RuleWithCustomHandler::class . '" given.'
        );
        $validator->validate([], [$rule]);
    }

    /**
     * @return array{0:string, 1:string}
     */
    abstract protected function getDifferentRuleInHandlerItems(): array;
}
