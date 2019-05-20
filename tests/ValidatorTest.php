<?php

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\DataSet;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Boolean;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Validator;

class ValidatorTest extends TestCase
{
    public function getDataObject(array $attributes): DataSet
    {
        return new class ($attributes) implements DataSet
        {
            private $data;

            public function __construct(array $data)
            {
                $this->data = $data;
            }

            public function getValue(string $key)
            {
                if (isset($this->data[$key])) {
                    return $this->data[$key];
                }

                throw new \RuntimeException("There is no $key in the class.");
            }
        };
    }

    public function testAddingRulesViaConstructor()
    {
        $dataObject = $this->getDataObject([
            'bool' => true,
            'int' => 41,
        ]);

        $validator = new Validator([
            'bool' => [new Boolean()],
            'int' => [
                (new Number())->integer(),
                (new Number())->integer()->min(44),
                function ($value): Result {
                    $result = new Result();
                    if ($value !== 42) {
                        $result->addError('Value should be 42!');
                    }
                    return $result;
                }
            ],
        ]);

        $results = $validator->validate($dataObject);

        $this->assertTrue($results->getResult('bool')->isValid());

        $intResult = $results->getResult('int');
        $this->assertFalse($intResult->isValid());
        $this->assertCount(2, $intResult->getErrors());
    }

    public function testAddingRulesOneByOne()
    {
        $dataObject = $this->getDataObject([
            'bool' => true,
            'int' => 42,
        ]);

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
