<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\ErrorMessage;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidatorFactory;

class ValidatorFactoryTest extends TranslatorMock
{
    public function testCreate()
    {
        $validation = new ValidatorFactory();

        $attribute = 'test';
        $errorMessage = 'error message';

        $validator = $validation->create(
            [
                $attribute => [
                    static function () use ($errorMessage) {
                        $result = new Result();
                        $result->addError(new ErrorMessage($errorMessage));
                        return $result;
                    },
                ],
            ]
        );

        $result = $validator->validate($this->createDataSet([$attribute => '']));

        $this->assertEquals(new ErrorMessage($errorMessage), $result->getResult($attribute)->getErrors()[0]);
    }

    public function testCreateWithTranslator()
    {
        $translatableMessage = 'test message';
        $validation = new ValidatorFactory($this->createTranslatorMock(['error' => $translatableMessage]));

        $attribute = 'test';
        $validator = $validation->create(
            [
                $attribute => [
                    static function () {
                        $result = new Result();
                        $result->addError(new ErrorMessage('error'));
                        return $result;
                    },
                ],
            ]
        );

        $result = $validator->validate($this->createDataSet([$attribute => '']));

        $this->assertEquals(new ErrorMessage('error'), $result->getResult($attribute)->getErrors()[0]);
    }

    public function testCreateWithInvalidRule()
    {
        $validation = new ValidatorFactory();

        $this->expectException(\InvalidArgumentException::class);

        $attribute = 'test';
        $validation->create(
            [
                $attribute => [
                    'invalid rule',
                ],
            ]
        );
    }

    private function createDataSet(array $attributes): DataSetInterface
    {
        return new class($attributes) implements DataSetInterface {
            private array $attributes;

            public function __construct(array $attributes)
            {
                $this->attributes = $attributes;
            }

            public function getAttributeValue(string $attribute)
            {
                return $this->hasAttribute($attribute) ? $this->attributes[$attribute] : null;
            }

            public function hasAttribute(string $attribute): bool
            {
                return array_key_exists($attribute, $this->attributes);
            }
        };
    }
}
