<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Lazy;
use Yiisoft\Validator\Rule\LazyHandler;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\Tests\Stub\FakeValidatorFactory;
use Yiisoft\Validator\Validator;

final class LazyTest extends AbstractRuleTest
{
    public function testSkipOnEmptyInConstructor(): void
    {
        $rule = new Lazy(skipOnEmpty: true);

        $this->assertTrue($rule->getSkipOnEmpty());
    }

    public function testSkipOnEmptySetter(): void
    {
        $rule = (new Lazy())->skipOnEmpty(true);

        $this->assertTrue($rule->getSkipOnEmpty());
    }

    public function optionsDataProvider(): array
    {
        return [
            [
                new Lazy(),
                [
                    'rules' => null,
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    protected function getRule(): SerializableRuleInterface
    {
        return new Lazy();
    }
}
