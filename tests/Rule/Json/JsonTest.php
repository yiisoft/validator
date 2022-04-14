<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Json;

use Yiisoft\Validator\Rule\Json\Json;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Tests\Rule\AbstractRuleTest;

/**
 * @group t2
 */
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

    protected function getRule(): RuleInterface
    {
        return new Json();
    }
}
