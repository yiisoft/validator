<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Regex;

use Yiisoft\Validator\Rule\Regex\Regex;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Tests\Rule\AbstractRuleTest;

final class RegexTest extends AbstractRuleTest
{
    public function optionsDataProvider(): array
    {
        return [
            [
                new Regex('//'),
                [
                    'pattern' => '//',
                    'not' => false,
                    'incorrectInputMessage' => [
                        'message' => 'Value should be string.',
                    ],
                    'message' => [
                        'message' => 'Value is invalid.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Regex('//', not: true),
                [
                    'pattern' => '//',
                    'not' => true,
                    'incorrectInputMessage' => [
                        'message' => 'Value should be string.',
                    ],
                    'message' => [
                        'message' => 'Value is invalid.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    protected function getRule(): RuleInterface
    {
        return new Regex('//');
    }
}
