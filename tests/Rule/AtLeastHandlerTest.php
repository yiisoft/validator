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
                $this->createObject(1, null),
                [new Error('The model is not valid. Must have at least "1" filled attributes.')],
            ],
            [
                new AtLeast(['attr1', 'attr2'], min: 2),
                $this->createObject(1, null),
                [new Error('The model is not valid. Must have at least "2" filled attributes.')],
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
                $this->createObject(1, null),
                [new Error('Custom error')],
            ],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new AtLeastHandler($this->getTranslator());
    }

    private function createObject(mixed $attr1, mixed $attr2): stdClass
    {
        $object = new stdClass();
        $object->attr1 = $attr1;
        $object->attr2 = $attr2;
        return $object;
    }
}
