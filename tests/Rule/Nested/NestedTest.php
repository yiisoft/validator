<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Nested;

use InvalidArgumentException;
use stdClass;
use Yiisoft\Validator\Rule\Nested\Nested;
use Yiisoft\Validator\Rule\Number\Number;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Tests\Rule\AbstractRuleTest;
use Yiisoft\Validator\Tests\Stub\Rule;

/**
 * @group t
 */
final class NestedTest extends AbstractRuleTest
{
    public function optionsDataProvider(): array
    {
        return [
            [
                new Nested([new Number()]),
                [
                    [
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
                    ],
                ],
            ],
            [
                new Nested(['user.age' => new Number()]),
                [
                    'user.age' => [
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
                    ],
                ],
            ],
            [
                new Nested([
                    'author.name' => new Rule('author-name', ['key' => 'name']),
                    'author.age' => new Rule('author-age', ['key' => 'age']),
                ]),
                [
                    'author.name' => ['key' => 'name'],
                    'author.age' => ['key' => 'age'],
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
                        'name' => ['key' => 'name'],
                        'age' => ['key' => 'age'],
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


    protected function getRule(): RuleInterface
    {
        return new Nested([]);
    }
}
