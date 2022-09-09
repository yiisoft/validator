<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Countable;
use stdClass;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\CountHandler;
use Yiisoft\Validator\RuleHandlerInterface;

final class CountHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $rule = new Count(min: 3);

        $lessThanMinmessage = 'This value must contain at least 3 items.';
        $greaterThanMaxMessage = 'This value must contain at most 3 items.';

        return [
            [$rule, 1, [new Error('This value must be an array or implement \Countable interface.')]],
            [$rule, [1], [new Error($lessThanMinmessage)]],
            [$rule, [], [new Error($lessThanMinmessage)]],
            [$rule, [0, 0], [new Error($lessThanMinmessage)]],
            [$rule, [1.1], [new Error($lessThanMinmessage)]],
            [$rule, [''], [new Error($lessThanMinmessage)]],
            [$rule, ['some string'], [new Error($lessThanMinmessage)]],
            [$rule, [new stdClass()], [new Error($lessThanMinmessage)]],
            // https://www.php.net/manual/ru/class.countable.php
            [
                $rule,
                [
                    new class () {
                        protected int $myCount = 3;

                        public function count(): int
                        {
                            return $this->myCount;
                        }
                    },
                ],
                [new Error($lessThanMinmessage)],
            ],
            [new Count(max: 3), [0, 0, 0, 0], [new Error($greaterThanMaxMessage)]],
        ];
    }

    public function passedValidationProvider(): array
    {
        return [
            [new Count(min: 3), [0, 0, 0]],
            [new Count(min: 3), [0, 0, 0, 0]],
            [new Count(exactly: 3), [0, 0, 0]],
            [new Count(max: 3), []],
            [new Count(max: 3), [0, 0]],
            [new Count(max: 3), [0, 0, 0]],
            [
                new Count(min: 3),
                new class () implements Countable {
                    protected int $myCount = 3;

                    public function count(): int
                    {
                        return $this->myCount;
                    }
                },
            ],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [new Count(max: 3, greaterThanMaxMessage: 'Custom message.'), [0, 0, 0, 0], [new Error('Custom message.')]],
            [new Count(exactly: 3, notExactlyMessage: 'Custom message.'), [0, 0, 0, 0], [new Error('Custom message.')]],
            [new Count(min: 3, lessThanMinMessage: 'Custom message.'), [0, 0], [new Error('Custom message.')]],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new CountHandler($this->getTranslator());
    }
}
