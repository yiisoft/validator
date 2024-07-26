<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\TestEnvironments\Php81\Rule;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithDifferentPropertyVisibility;
use Yiisoft\Validator\Tests\TestEnvironments\Support\Data\NestedClassAttribute;
use Yiisoft\Validator\Tests\TestEnvironments\Support\Data\NestedWithCallbackAttribute;
use Yiisoft\Validator\Validator;

final class NestedTest extends TestCase
{
    public function dataHandler(): array
    {
        return [
            'object' => [
                new class () {
                    #[Nested(['number' => new Number(max: 7)])]
                    private ObjectWithDifferentPropertyVisibility $object;

                    public function __construct()
                    {
                        $this->object = new ObjectWithDifferentPropertyVisibility();
                    }
                },
                [
                    'object.number' => ['Number must be no greater than 7.'],
                ],
            ],
            'object-private-only' => [
                new class () {
                    #[Nested(
                        ['age' => new Number(min: 100, skipOnEmpty: true), 'number' => new Number(max: 7)],
                        validatedObjectPropertyVisibility: ReflectionProperty::IS_PRIVATE,
                    )]
                    private ObjectWithDifferentPropertyVisibility $object;

                    public function __construct()
                    {
                        $this->object = new ObjectWithDifferentPropertyVisibility();
                    }
                },
                [
                    'object.number' => ['Number must be no greater than 7.'],
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataHandler
     */
    public function testHandler(
        object $data,
        array $expectedErrorMessagesIndexedByPath,
        ?bool $expectedIsValid = false
    ): void {
        $result = (new Validator())->validate($data);

        $this->assertSame($expectedIsValid, $result->isValid());
        if (!$expectedIsValid) {
            $this->assertSame($expectedErrorMessagesIndexedByPath, $result->getErrorMessagesIndexedByPath());
        }
    }

    public function testClassAttribute(): void
    {
        $result = (new Validator())->validate(new NestedClassAttribute());

        $this->assertSame(
            [
                'a' => ['A must be no less than 7.'],
                'b' => ['B must be no greater than 1.'],
            ],
            $result->getErrorMessagesIndexedByProperty(),
        );
    }

    public function testWithCallbackAttribute(): void
    {
        $result = (new Validator())->validate(new NestedWithCallbackAttribute());

        $this->assertSame(
            [
                'a' => ['Invalid A.'],
                'b' => ['Invalid B.'],
            ],
            $result->getErrorMessagesIndexedByProperty(),
        );
    }
}
