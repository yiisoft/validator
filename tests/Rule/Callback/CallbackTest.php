<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Callback;

use Yiisoft\Validator\ParametrizedRuleInterface;
use Yiisoft\Validator\Rule\AtLeast\AtLeast;
use Yiisoft\Validator\Rule\Callback\Callback;
use Yiisoft\Validator\Tests\Rule\AbstractRuleTest;

/**
 * @group t
 */
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
