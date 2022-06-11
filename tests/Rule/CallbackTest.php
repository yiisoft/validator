<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\AtLeast;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\ParametrizedRuleInterface;

final class CallbackTest extends AbstractRuleTest
{
    public function optionsDataProvider(): array
    {
        return [
            [
                new Callback(static fn ($value) => $value),
                [
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Callback(static fn ($value) => $value, skipOnEmpty: true),
                [
                    'skipOnEmpty' => true,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    protected function getRule(): ParametrizedRuleInterface
    {
        return new AtLeast([]);
    }
}
