<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\SerializableRuleInterface;

final class RequiredTest extends AbstractRuleTest
{
    public function optionsDataProvider(): array
    {
        return [
            [
                new Required(),
                [
                    'message' => [
                        'message' => 'Value cannot be blank.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    protected function getRule(): SerializableRuleInterface
    {
        return new Required();
    }
}
