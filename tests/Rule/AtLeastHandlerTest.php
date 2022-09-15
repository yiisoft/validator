<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use stdClass;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\AtLeast;
use Yiisoft\Validator\Rule\AtLeastHandler;
use Yiisoft\Validator\RuleHandlerInterface;

final class AtLeastHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        return [
            [
                new AtLeast(['attr2']),
                ...$this->createValueAndErrorsPair(
                $this->createObject(1, null),
                [new Error('The model is not valid. Must have at least "{min}" filled attributes.', parameters: ['min' => 1])]
                ),
            ],
            [
                new AtLeast(['attr1', 'attr2'], min: 2),
                ...$this->createValueAndErrorsPair(
                    $this->createObject(1, null),
                [new Error('The model is not valid. Must have at least "{min}" filled attributes.', parameters: ['min' => 2])]
                ),
            ],
        ];
    }

    public function passedValidationProvider(): array
    {
        return [
            [
                new AtLeast(['attr1', 'attr2']),
                $this->createObject(1, null),
            ],
            [
                new AtLeast(['attr2']),
                $this->createObject(null, 1),
            ],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [
                new AtLeast(['attr1', 'attr2'], min: 2, message: 'Custom error'),
                ...$this->createValueAndErrorsPair(
                    $this->createObject(1, null),
                [new Error('Custom error', parameters: ['min' => 2])]
                ),
            ],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new AtLeastHandler();
    }

    private function createObject(mixed $attr1, mixed $attr2): stdClass
    {
        $object = new stdClass();
        $object->attr1 = $attr1;
        $object->attr2 = $attr2;
        return $object;
    }
}
