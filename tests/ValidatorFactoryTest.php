<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\I18n\TranslatorInterface;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rules;
use Yiisoft\Validator\ValidatorFactory;

class ValidatorFactoryTest extends TestCase
{
    public function testCreate()
    {
        $validation = new ValidatorFactory();

        $attribute = 'test';
        $errorMessage = 'error message';

        $validator = $validation->create(
            [
                $attribute => new Rules([
                    static function () use ($errorMessage) {
                        $result = new Error();
                        $result->addError($errorMessage);
                        return $result;
                    }
                ])
            ]
        );

        $result = $validator->validate($this->createDataSet([$attribute => '']));

        $this->assertSame($errorMessage, $result->getResult($attribute)->getErrors()[0]);
    }

    public function testCreateWithTranslator()
    {
        $translatableMessage = 'test message';
        $validation = new ValidatorFactory();

        $attribute = 'test';
        $validator = $validation->create(
            [
                $attribute => new Rules([
                    static function () {
                        $result = new Error();
                        $result->addError('error');
                        return $result;
                    }
                ])
            ]
        );

        $result = $validator->validate($this->createDataSet([$attribute => '']))->translator($this->createTranslatorMock($translatableMessage));
        self::assertEquals([$translatableMessage], $result->getResult($attribute)->getErrors());
    }


    public function testCreateWithInvalidRule()
    {
        $validation = new ValidatorFactory();

        $this->expectException(\InvalidArgumentException::class);

        $attribute = 'test';
        $validation->create(
            new Rules([
                $attribute => [
                    'invalid rule'
                ]
            ])
        );
    }

    private function createTranslatorMock(string $returnMessage = null): TranslatorInterface
    {
        $translator = $this->createMock(TranslatorInterface::class);

        if ($returnMessage) {
            $translator
                ->method('translate')
                ->willReturn($returnMessage);
        }

        return $translator;
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
