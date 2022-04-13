<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Email;

use Yiisoft\Validator\ParametrizedRuleInterface;
use Yiisoft\Validator\Rule\Email\Email;
use Yiisoft\Validator\Tests\Rule\AbstractRuleTest;

/**
 * @group t2
 */
final class EmailTest extends AbstractRuleTest
{
    public function optionsDataProvider(): array
    {
        return [

            [
                new Email(),
                [
                    'allowName' => false,
                    'checkDNS' => false,
                    'enableIDN' => false,
                    'message' => [
                        'message' => 'This value is not a valid email address.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Email(allowName: true),
                [
                    'allowName' => true,
                    'checkDNS' => false,
                    'enableIDN' => false,
                    'message' => [
                        'message' => 'This value is not a valid email address.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Email(allowName: true, checkDNS: true),
                [
                    'allowName' => true,
                    'checkDNS' => true,
                    'enableIDN' => false,
                    'message' => [
                        'message' => 'This value is not a valid email address.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Email(allowName: true, enableIDN: true),
                [
                    'allowName' => true,
                    'checkDNS' => false,
                    'enableIDN' => true,
                    'message' => [
                        'message' => 'This value is not a valid email address.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    protected function getRule(): ParametrizedRuleInterface
    {
        return new Email();
    }
}
