<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\Json;
use Yiisoft\Validator\SerializableRuleInterface;

final class JsonTest extends AbstractRuleTest
{
    public function optionsDataProvider(): array
    {
        return [
            [
                new Json(),
                [
                    'message' => [
                        'message' => 'The value is not JSON.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    protected function getRule(): SerializableRuleInterface
    {
        return new Json();
    }
}
