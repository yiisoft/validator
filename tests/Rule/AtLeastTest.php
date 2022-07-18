<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\AtLeast;
use Yiisoft\Validator\SerializableRuleInterface;

final class AtLeastTest extends AbstractRuleTest
{
    public function optionsDataProvider(): array
    {
        return [
            [
                new AtLeast(['attr1', 'attr2']),
                [
                    'attributes' => [
                        'attr1', 'attr2',
                    ],
                    'min' => 1,
                    'message' => [
                        'message' => 'The model is not valid. Must have at least "{min}" filled attributes.',
                        'parameters' => ['min' => 1],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new AtLeast(['attr1', 'attr2'], min: 2),
                [
                    'attributes' => [
                        'attr1', 'attr2',
                    ],
                    'min' => 2,
                    'message' => [
                        'message' => 'The model is not valid. Must have at least "{min}" filled attributes.',
                        'parameters' => ['min' => 2],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    protected function getRule(): SerializableRuleInterface
    {
        return new AtLeast([]);
    }
}
