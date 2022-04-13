<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\AtLeast;

use Yiisoft\Validator\ParametrizedRuleInterface;
use Yiisoft\Validator\Rule\AtLeast\AtLeast;
use Yiisoft\Validator\Tests\Rule\AbstractRuleTest;

/**
 * @group t
 */
final class AtLeastTest extends AbstractRuleTest
{
    public function optionsDataProvider(): array
    {
        return [
            [
                new AtLeast(['attr1', 'attr2']),
                [
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

    protected function getRule(): ParametrizedRuleInterface
    {
        return new AtLeast([]);
    }
}
