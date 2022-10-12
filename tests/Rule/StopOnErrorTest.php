<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\StopOnError;
use Yiisoft\Validator\Rule\StopOnErrorHandler;
use Yiisoft\Validator\Tests\Support\ValidatorFactory;
use Yiisoft\Validator\Tests\Support\RuleWithCustomHandler;
use Yiisoft\Validator\Validator;

final class StopOnErrorTest extends TestCase
{
    public function testGetName(): void
    {
        $rule = new StopOnError();
        $this->assertSame('stopOnError', $rule->getName());
    }

    public function testSkipOnEmptyInConstructor(): void
    {
        $rule = new StopOnError(skipOnEmpty: true);
        $this->assertTrue($rule->getSkipOnEmpty());
    }

    public function testSkipOnEmptySetter(): void
    {
        $rule = (new StopOnError())->skipOnEmpty(true);
        $this->assertTrue($rule->getSkipOnEmpty());
    }

    public function dataOptions(): array
    {
        return [
            [
                new StopOnError(),
                [
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'rules' => null,
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataOptions
     */
    public function testOptions(StopOnError $rule, array $expectedOptions): void
    {
        $options = $rule->getOptions();
        $this->assertSame($expectedOptions, $options);
    }

    public function dataValidationPassed(): array
    {
        return [
            'at least one succeed property' => [
                'hello',
                [
                    new StopOnError([
                        new HasLength(min: 1),
                        new HasLength(max: 10),
                    ])
                ],
            ],
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
            'case1' => [
                'hello',
                [
                    new StopOnError([
                        new HasLength(min: 10),
                        new HasLength(max: 1),
                    ])
                ],
                ['' => ['This value must contain at least 10 characters.']],
            ],
            'case2' => [
                'hello',
                [
                    new StopOnError([
                        new HasLength(max: 1),
                        new HasLength(min: 10),
                    ])
                ],
                ['' => ['This value must contain at most 1 character.']],
            ],
            'nested rules instead of plain structure' => [
                'hello',
                [
                    new StopOnError([
                        [
                            new HasLength(max: 1),
                            new HasLength(min: 10),
                        ],
                    ])
                ],
                ['' => ['This value must contain at most 1 character.']],
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

    public function testDifferentRuleInHandler(): void
    {
        $rule = new RuleWithCustomHandler(StopOnErrorHandler::class);
        $validator = ValidatorFactory::make();

        $this->expectExceptionMessageMatches(
            '/.*' . preg_quote(StopOnError::class) . '.*' . preg_quote(RuleWithCustomHandler::class) . '.*/'
        );
        $validator->validate([], [$rule]);
    }
}
