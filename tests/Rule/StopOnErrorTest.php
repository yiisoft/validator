<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\StopOnError;
use Yiisoft\Validator\SerializableRuleInterface;

final class StopOnErrorTest extends AbstractRuleTest
{
    public function testSkipOnEmptyInConstructor(): void
    {
        $rule = new StopOnError(skipOnEmpty: true);

        $this->assertTrue($rule->getSkipOnEmpty());
    }

    public function testSkipOnEmptySetter(): void
    {
        $rule = (new StopOnError())->skipOnEmpty(true);

        $this->assertTrue($rule->getSkipOnEmpty());
    }

    public function optionsDataProvider(): array
    {
        return [
            [
                new StopOnError(),
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
        return new StopOnError();
    }
}
