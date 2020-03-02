<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use Yiisoft\Validator\Rule;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\MissingAttributeException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Boolean;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Validator;

class ValidatorTest extends TestCase
{
    public function getDataObject(array $attributes): DataSetInterface
    {
        return new class($attributes) implements DataSetInterface {
            private array $data;

            public function __construct(array $data)
            {
                $this->data = $data;
            }

            public function getAttributeValue(string $attribute)
            {
                if (!$this->hasAttribute($attribute)) {
                    throw new MissingAttributeException("There is no \"$attribute\" attribute in the class.");
                }

                return $this->data[$attribute];
            }

            public function hasAttribute(string $attribute): bool
            {
                return isset($this->data[$attribute]);
            }
        };
    }

    public function testAddingRulesViaConstructor(): void
    {
        $dataObject = $this->getDataObject(
            [
                'bool' => true,
                'int' => 41,
            ]
        );

        $validator = new Validator(
            [
                'bool' => [new Boolean()],
                'int' => [
                    (new Number())->integer(),
                    (new Number())->integer()->min(44),
                    static function ($value): Result {
                        $result = new Result();
                        if ($value !== 42) {
                            $result->addError('Value should be 42!');
                        }
                        return $result;
                    }
                ],
            ]
        );

        $results = $validator->validate($dataObject);

        $this->assertTrue($results->getResult('bool')->isValid());

        $intResult = $results->getResult('int');
        $this->assertFalse($intResult->isValid());
        $this->assertCount(1, $intResult->getErrors());
    }

    public function testAddingRulesOneByOne(): void
    {
        $dataObject = $this->getDataObject(
            [
                'bool' => true,
                'int' => 42,
            ]
        );

        $validator = new Validator();
        $validator->addRule('bool', new Boolean());
        $validator->addRule('int', (new Number())->integer());
        $validator->addRule('int', (new Number())->integer()->min(44));

        $results = $validator->validate($dataObject);

        $this->assertTrue($results->getResult('bool')->isValid());

        $intResult = $results->getResult('int');
        $this->assertFalse($intResult->isValid());
        $this->assertCount(1, $intResult->getErrors());
    }
}
