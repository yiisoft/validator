<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Base;

use InvalidArgumentException;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\RuleWithOptionsInterface;

trait RuleWithProvidedRulesTrait
{
    abstract public function testGetOptionsWithNotRule(): void;

    private function testGetOptionsWithNotRuleInternal($ruleClassName): void
    {
        $rule = new $ruleClassName([
            new Required(),
            new class () {
            },
            new Number(min: 1),
        ]);
        $this->assertInstanceOf(RuleWithOptionsInterface::class, $rule);
        $this->assertInstanceOf(RulesProviderInterface::class, $rule);

        $this->expectException(InvalidArgumentException::class);

        $ruleInterfaceName = RuleInterface::class;
        $message = "Rule must be either an instance of $ruleInterfaceName or a callable, class@anonymous given.";
        $this->expectExceptionMessage($message);

        $rule->getOptions();
    }
}
