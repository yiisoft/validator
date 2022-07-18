<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use stdClass;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\Tests\Stub\Rule;

final class NestedTest extends AbstractRuleTest
{
    public function optionsDataProvider(): array
    {
        return [
            [
                new Nested([new Number(integerPattern: '/1/', numberPattern: '/1/')]),
                [
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
            [
                new Nested(['user.age' => new Number(integerPattern: '/1/', numberPattern: '/1/')]),
                [
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
            [
                new Nested([
                    'author.name' => new Rule('author-name', ['key' => 'name']),
                    'author.age' => new Rule('author-age', ['key' => 'age']),
                ]),
                [
                    'author.name' => ['author-name', 'key' => 'name'],
                    'author.age' => ['author-age', 'key' => 'age'],
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
                    'author' => [
                        'name' => ['author-name', 'key' => 'name'],
                        'age' => ['author-age', 'key' => 'age'],
                    ],
                ],
            ],
        ];
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

    protected function getRule(): SerializableRuleInterface
    {
        return new Nested([new Rule('rule-name', ['key' => 'value'])]);
    }
}
