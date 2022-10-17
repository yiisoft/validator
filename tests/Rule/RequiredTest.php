<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\RequiredHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;

final class RequiredTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use SerializableRuleTestTrait;

    public function testGetName(): void
    {
        $rule = new Required();
        $this->assertSame('required', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new Required(),
                [
                    'message' => 'Value cannot be blank.',
                    'notPassedMessage' => 'Value not passed.',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    public function dataValidationPassed(): array
    {
        return [
            ['not empty', [new Required()]],
            [['with', 'elements'], [new Required()]],
        ];
    }

    public function dataValidationFailed(): array
    {
        $message = 'Value cannot be blank.';

        return [
            [null, [new Required()], ['' => [$message]]],
            [[], [new Required()], ['' => [$message]]],
            'custom error' => [null, [new Required(message: 'Custom error')], ['' => ['Custom error']]],
        ];
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Required::class, RequiredHandler::class];
    }
}
