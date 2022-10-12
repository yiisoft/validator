<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Tests\Support\ValidatorFactory;

final class RegexTest extends TestCase
{
    public function testGetName(): void
    {
        $rule = new Regex('//');
        $this->assertSame('regex', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new Regex('//'),
                [
                    'pattern' => '//',
                    'not' => false,
                    'incorrectInputMessage' => [
                        'message' => 'Value should be string.',
                    ],
                    'message' => [
                        'message' => 'Value is invalid.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Regex('//', not: true),
                [
                    'pattern' => '//',
                    'not' => true,
                    'incorrectInputMessage' => [
                        'message' => 'Value should be string.',
                    ],
                    'message' => [
                        'message' => 'Value is invalid.',
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
    public function testOptions(Regex $rule, array $expectedOptions): void
    {
        $options = $rule->getOptions();
        $this->assertSame($expectedOptions, $options);
    }

    public function dataValidationPassed(): array
    {
        return [
            ['a', [new Regex('/a/')]],
            ['ab', [new Regex('/a/')]],
            ['b', [new Regex('/a/', not: true)]],
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
        $incorrectInputMessage = 'Value should be string.';
        $message = 'Value is invalid.';

        return [
            [['a', 'b'], [new Regex('/a/')], ['' => [$incorrectInputMessage]]],
            [['a', 'b'], [new Regex('/a/', not: true)], ['' => [$incorrectInputMessage]]],
            [null, [new Regex('/a/')], ['' => [$incorrectInputMessage]]],
            [null, [new Regex('/a/', not: true)], ['' => [$incorrectInputMessage]]],
            [new stdClass(), [new Regex('/a/')], ['' => [$incorrectInputMessage]]],
            [new stdClass(), [new Regex('/a/', not: true)], ['' => [$incorrectInputMessage]]],
            ['b', [new Regex('/a/')], ['' => [$message]]],
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

    public function dataCustomErrorMessage(): array
    {
        return [
            ['b', [new Regex('/a/', message: 'Custom message.')], ['' => ['Custom message.']]],
            [null, [new Regex('/a/', incorrectInputMessage: 'Custom message.')], ['' => ['Custom message.']]],
        ];
    }

    /**
     * @dataProvider dataCustomErrorMessage
     */
    public function testCustomErrorMessage(mixed $data, array $rules, array $errorMessagesIndexedByPath): void
    {
        $result = ValidatorFactory::make()->validate($data, $rules);

        $this->assertFalse($result->isValid());
        $this->assertSame($errorMessagesIndexedByPath, $result->getErrorMessagesIndexedByPath());
    }
}
