<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use ArrayObject;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\DataSet\MixedDataSet;
use Yiisoft\Validator\Rule\Subset;
use Yiisoft\Validator\Rule\SubsetHandler;
use Yiisoft\Validator\Tests\Support\ValidatorFactory;
use Yiisoft\Validator\Tests\Support\Rule\RuleWithCustomHandler;
use Yiisoft\Validator\Validator;

final class SubsetTest extends TestCase
{
    public function testGetName(): void
    {
        $rule = new Subset([]);
        $this->assertSame('subset', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new Subset([]),
                [
                    'values' => [],
                    'strict' => false,
                    'iterableMessage' => [
                        'message' => 'Value must be iterable.',
                    ],
                    'subsetMessage' => [
                        'message' => 'Values must be ones of {values}.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataOptions
     */
    public function testOptions(Subset $rule, array $expectedOptions): void
    {
        $options = $rule->getOptions();
        $this->assertSame($expectedOptions, $options);
    }

    public function dataValidationPassed(): array
    {
        return [
            [[], [new Subset(range(1, 10))]],
            [[1, 2, 3, 4, 5], [new Subset(range(1, 10))]],
            [[6, 7, 8, 9, 10], [new Subset(range(1, 10))]],
            [['1', '2', '3', 4, 5, 6], [new Subset(range(1, 10))]],

            [['a', 'b'], [new Subset(['a', 'b', 'c'])]],
            [new MixedDataSet(new ArrayObject(['a', 'b'])), [new Subset(['a', 'b', 'c'])]],
        ];
    }

    /**
     * @dataProvider dataValidationPassed
     */
    public function testValidationPassed(mixed $data, array $rules): void
    {
        $result = ValidatorFactory::make()->validate($data, $rules);

        $this->assertTrue($result->isValid());
    }

    public function dataValidationFailed(): array
    {
        return [
            [
                [0, 1, 2],
                [new Subset(range(1, 10))],
                ['' => ['Values must be ones of "1", "2", "3", "4", "5", "6", "7", "8", "9", "10".']],
            ],
            [
                [10, 11, 12],
                [new Subset(range(1, 10))],
                ['' => ['Values must be ones of "1", "2", "3", "4", "5", "6", "7", "8", "9", "10".']],
            ],
        ];
    }

    /**
     * @dataProvider dataValidationFailed
     */
    public function testValidationFailed(mixed $data, array $rules, array $errorMessagesIndexedByPath): void
    {
        $result = ValidatorFactory::make()->validate($data, $rules);

        $this->assertFalse($result->isValid());
        $this->assertSame($errorMessagesIndexedByPath, $result->getErrorMessagesIndexedByPath());
    }

    public function testCustomErrorMessage(): void
    {
        $data = ['data' => ['2']];
        $rules = ['data' => new Subset(['a'], subsetMessage: 'Custom error')];

        $result = ValidatorFactory::make()->validate($data, $rules);

        $this->assertFalse($result->isValid());
        $this->assertSame(
            ['data' => ['Custom error']],
            $result->getErrorMessagesIndexedByPath()
        );
    }

    public function testDifferentRuleInHandler(): void
    {
        $rule = new RuleWithCustomHandler(SubsetHandler::class);
        $validator = ValidatorFactory::make();

        $this->expectExceptionMessageMatches(
            '/.*' . preg_quote(Subset::class) . '.*' . preg_quote(RuleWithCustomHandler::class) . '.*/'
        );
        $validator->validate([], [$rule]);
    }
}
