<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\NestedHandler;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipNone;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipOnEmpty;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipOnNull;
use Yiisoft\Validator\Tests\Stub\Rule;

final class NestedTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $rule = new Nested();

        $this->assertNull($rule->getRules());
        $this->assertFalse($rule->getRequirePropertyPath());
        $this->assertSame('Property path "{path}" is not found.', $rule->getNoPropertyPathMessage());
        $this->assertFalse($rule->getSkipOnEmpty());
        $this->assertInstanceOf(SkipNone::class, $rule->getSkipOnEmptyCallback());
        $this->assertFalse($rule->shouldSkipOnError());
        $this->assertNull($rule->getWhen());
    }

    public function testSkipOnEmptyInConstructor(): void
    {
        $rule = new Nested(skipOnEmpty: true);

        $this->assertTrue($rule->getSkipOnEmpty());
    }

    public function testSkipOnEmptyCallbackInConstructor(): void
    {
        $rule = new Nested(skipOnEmptyCallback: new SkipOnNull());

        $this->assertInstanceOf(SkipOnNull::class, $rule->getSkipOnEmptyCallback());
    }

    public function testSkipOnEmptySetter(): void
    {
        $rule = (new Nested())->skipOnEmpty(true);

        $this->assertTrue($rule->getSkipOnEmpty());
    }

    public function testSkipOnEmptyCallbackSetter(): void
    {
        $rule = (new Nested())->skipOnEmptyCallback(new SkipOnEmpty());

        $this->assertInstanceOf(SkipOnEmpty::class, $rule->getSkipOnEmptyCallback());
    }

    public function testGetName(): void
    {
        $rule = new Nested();

        $this->assertEquals('nested', $rule->getName());
    }

    public function testHandlerClassName(): void
    {
        $rule = new Nested();

        $this->assertSame(NestedHandler::class, $rule->getHandlerClassName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new Nested([new Number(integerPattern: '/1/', numberPattern: '/1/')]),
                [
                    'requirePropertyPath' => false,
                    'noPropertyPathMessage' => [
                        'message' => 'Property path "{path}" is not found.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'rules' => [
                        [
                            'number',
                            'asInteger' => false,
                            'min' => null,
                            'max' => null,
                            'notANumberMessage' => [
                                'message' => 'Value must be a number.',
                            ],
                            'tooSmallMessage' => [
                                'message' => 'Value must be no less than {min}.',
                                'parameters' => ['min' => null],
                            ],
                            'tooBigMessage' => [
                                'message' => 'Value must be no greater than {max}.',
                                'parameters' => ['max' => null],
                            ],
                            'skipOnEmpty' => false,
                            'skipOnError' => false,
                            'integerPattern' => '/1/',
                            'numberPattern' => '/1/',
                        ],
                    ],
                ],
            ],
            [
                new Nested(['user.age' => new Number(integerPattern: '/1/', numberPattern: '/1/')]),
                [
                    'requirePropertyPath' => false,
                    'noPropertyPathMessage' => [
                        'message' => 'Property path "{path}" is not found.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'rules' => [
                        'user.age' => [
                            'number',
                            'asInteger' => false,
                            'min' => null,
                            'max' => null,
                            'notANumberMessage' => [
                                'message' => 'Value must be a number.',
                            ],
                            'tooSmallMessage' => [
                                'message' => 'Value must be no less than {min}.',
                                'parameters' => ['min' => null],
                            ],
                            'tooBigMessage' => [
                                'message' => 'Value must be no greater than {max}.',
                                'parameters' => ['max' => null],
                            ],
                            'skipOnEmpty' => false,
                            'skipOnError' => false,
                            'integerPattern' => '/1/',
                            'numberPattern' => '/1/',
                        ],
                    ],
                ],
            ],
            [
                new Nested([
                    'author.name' => new Rule('author-name', ['key' => 'name']),
                    'author.age' => new Rule('author-age', ['key' => 'age']),
                ]),
                [
                    'requirePropertyPath' => false,
                    'noPropertyPathMessage' => [
                        'message' => 'Property path "{path}" is not found.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'rules' => [
                        'author.name' => ['author-name', 'key' => 'name'],
                        'author.age' => ['author-age', 'key' => 'age'],
                    ],
                ],
            ],
            [
                new Nested([
                    'author' => [
                        'name' => new Rule('author-name', ['key' => 'name']),
                        'age' => new Rule('author-age', ['key' => 'age']),
                    ],
                ]),
                [
                    'requirePropertyPath' => false,
                    'noPropertyPathMessage' => [
                        'message' => 'Property path "{path}" is not found.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'rules' => [
                        'author' => [
                            'name' => ['author-name', 'key' => 'name'],
                            'age' => ['author-age', 'key' => 'age'],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataOptions
     */
    public function testOptions(Nested $rule, array $expectedOptions): void
    {
        $options = $rule->getOptions();

        $this->assertEquals($expectedOptions, $options);
    }

    public function testValidationEmptyRules(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Nested([]);
    }

    public function testValidationRuleIsNotInstanceOfRule(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Nested(['path.to.value' => (new stdClass())]);
    }

    public function testWithNestedAndEachShortcutBare(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Bare shortcut is prohibited. Use "Each" rule instead.');
        new Nested(['*' => [new Number(min: -10, max: 10)]]);
    }
}
