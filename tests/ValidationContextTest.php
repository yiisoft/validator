<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yiisoft\Validator\PropertyTranslator\ArrayPropertyTranslator;
use Yiisoft\Validator\PropertyTranslator\NullPropertyTranslator;
use Yiisoft\Validator\DataSet\ArrayDataSet;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\Validator;

final class ValidationContextTest extends TestCase
{
    public function testGetDataSetWithoutDataSet(): void
    {
        $context = new ValidationContext();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Data set in validation context is not set.');
        $context->getDataSet();
    }

    public function testConstructor(): void
    {
        $context = new ValidationContext(['key' => 42]);

        $this->assertSame(42, $context->getParameter('key'));
    }

    public function testDataSet(): void
    {
        $dataSet = new ArrayDataSet();

        $context = new ValidationContext();
        $context->setDataSet($dataSet);

        $this->assertSame($dataSet, $context->getDataSet());
    }

    public function testSetParameter(): void
    {
        $context = new ValidationContext();
        $context->setParameter('key', 42);

        $this->assertSame(42, $context->getParameter('key'));
    }

    public function testGetParameter(): void
    {
        $context = new ValidationContext(['key' => 42]);

        $this->assertSame(42, $context->getParameter('key'));
        $this->assertNull($context->getParameter('non-exists'));
        $this->assertSame(7, $context->getParameter('non-exists', 7));
    }

    public function testValidateWithoutValidator(): void
    {
        $context = new ValidationContext();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Validator is not set in validation context.');
        $context->validate(42);
    }

    public function testValidateInCurrentScopeWithoutValidator(): void
    {
        $context = new ValidationContext();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Validator is not set in validation context.');
        $context->validateInCurrentScope(42, []);
    }

    public function testValidateInCurrentScopeWithCallable(): void
    {
        $data = ['a' => 7, 'b' => 8];
        $innerValue = null;
        $innerData = null;
        $innerProperty = null;

        $result = (new Validator())->validate($data, [
            'a' => new Callback(
                static function (mixed $value, Callback $rule, ValidationContext $context) use (
                    &$innerValue,
                    &$innerData,
                    &$innerProperty,
                ): Result {
                    return $context->validateInCurrentScope(
                        $value,
                        static function (mixed $value, Callback $rule, ValidationContext $context) use (
                            &$innerValue,
                            &$innerData,
                            &$innerProperty,
                        ): Result {
                            $innerValue = $value;
                            $innerData = $context->getDataSet()->getData();
                            $innerProperty = $context->getProperty();
                            return (new Result())->addError('Error.');
                        },
                    );
                },
            ),
        ]);

        $this->assertSame(7, $innerValue);
        $this->assertSame($data, $innerData);
        $this->assertSame('a', $innerProperty);
        $this->assertSame(['a' => ['Error.']], $result->getErrorMessagesIndexedByPath());
    }

    public function testValidateInCurrentScopeWithCurrentValue(): void
    {
        $data = new class {
            public int $a = 5;
            public int $b = 7;
        };

        $result = (new Validator())->validate($data, [
            new Callback(
                static fn(mixed $value, Callback $rule, ValidationContext $context): Result => $context
                    ->validateInCurrentScope($value, new Each([new Number(max: 6)])),
            ),
        ]);

        $this->assertSame(['b' => ['Value must be no greater than 6.']], $result->getErrorMessagesIndexedByPath());
    }

    public function testValidateInCurrentScopeWithAnotherValue(): void
    {
        $data = ['items' => [1, 20], 'c' => 100];

        $result = (new Validator())->validate($data, [
            new Callback(
                static fn(mixed $value, Callback $rule, ValidationContext $context): Result => $context
                    ->validateInCurrentScope($value['items'], new Each([new Number(max: 6)])),
            ),
        ]);

        $this->assertSame(['1' => ['Value must be no greater than 6.']], $result->getErrorMessagesIndexedByPath());
    }

    public function testValidateInCurrentScopeRestoresParameters(): void
    {
        $data = ['items' => [1, 20]];
        $parametersInside = null;
        $parametersBefore = null;
        $parametersAfter = null;

        (new Validator())->validate($data, [
            new Callback(
                static function (mixed $value, Callback $rule, ValidationContext $context) use (
                    &$parametersInside,
                    &$parametersBefore,
                    &$parametersAfter,
                ): Result {
                    $parametersBefore = [
                        $context->getParameter(ValidationContext::PARAMETER_PREVIOUS_RULES_ERRORED),
                        $context->getParameter(ValidationContext::PARAMETER_VALUE_AS_ARRAY),
                    ];
                    $context->validateInCurrentScope($value['items'], [
                        new Number(max: 6),
                        static function (mixed $value, Callback $rule, ValidationContext $context) use (
                            &$parametersInside,
                        ): Result {
                            $parametersInside = [
                                $context->getParameter(ValidationContext::PARAMETER_PREVIOUS_RULES_ERRORED),
                                $context->getParameter(ValidationContext::PARAMETER_VALUE_AS_ARRAY),
                            ];
                            return new Result();
                        },
                    ]);
                    $parametersAfter = [
                        $context->getParameter(ValidationContext::PARAMETER_PREVIOUS_RULES_ERRORED),
                        $context->getParameter(ValidationContext::PARAMETER_VALUE_AS_ARRAY),
                    ];
                    return new Result();
                },
            ),
        ]);

        $this->assertSame([null, $data], $parametersBefore);
        $this->assertSame([true, null], $parametersInside);
        $this->assertSame($parametersBefore, $parametersAfter);
    }

    public function testGetRawDataWithoutRawData(): void
    {
        $context = new ValidationContext();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Validator is not set in validation context.');
        $context->getRawData();
    }

    public function testSetContextDataOnce(): void
    {
        $validator = new Validator();
        $data1 = ['1'];
        $data2 = ['2'];
        $dataSet1 = new ArrayDataSet($data1);
        $dataSet2 = new ArrayDataSet($data2);
        $currentScopeValidator1 = static fn(): Result => (new Result())->addError('Error 1.');
        $currentScopeValidator2 = static fn(): Result => (new Result())->addError('Error 2.');

        $context = (new ValidationContext())
            ->setContextDataOnce($validator, new NullPropertyTranslator(), $data1, $dataSet1, $currentScopeValidator1)
            ->setContextDataOnce($validator, new NullPropertyTranslator(), $data2, $dataSet2, $currentScopeValidator2);

        $this->assertSame($data1, $context->getRawData());
        $this->assertSame($dataSet1, $context->getGlobalDataSet());
        $this->assertSame(['Error 1.'], $context->validateInCurrentScope(null, [])->getErrorMessages());
    }

    public static function dataTranslatedPropertyWithoutTranslator(): array
    {
        return [
            'null' => ['Value'],
            'string' => ['test'],
        ];
    }

    #[DataProvider('dataTranslatedPropertyWithoutTranslator')]
    public function testTranslatedPropertyWithoutTranslator(?string $property): void
    {
        $context = new ValidationContext();
        $context->setProperty($property);

        $this->assertSame($property, $context->getTranslatedProperty());
    }

    public function testSetPropertyTranslator(): void
    {
        $translator = new ArrayPropertyTranslator(['name' => 'Имя']);

        $context = (new ValidationContext())
            ->setPropertyTranslator($translator)
            ->setProperty('name');

        $this->assertSame('Имя', $context->getTranslatedProperty());
    }

    public function testSetPropertyLabel(): void
    {
        $context = (new ValidationContext())
            ->setProperty('name')
            ->setPropertyLabel('first Name');

        $this->assertSame('first Name', $context->getPropertyLabel());
        $this->assertSame('first Name', $context->getTranslatedProperty());
        $this->assertSame('First Name', $context->getCapitalizedTranslatedProperty());
    }

    public function testSetPropertyLabelWithTranslator(): void
    {
        $translator = new ArrayPropertyTranslator(['First Name' => 'Имя']);

        $context = (new ValidationContext())
            ->setPropertyTranslator($translator)
            ->setProperty('name')
            ->setPropertyLabel('First Name');

        $this->assertSame('Имя', $context->getTranslatedProperty());
    }
}
