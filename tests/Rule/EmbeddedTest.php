<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Closure;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Yiisoft\Validator\Rule\Embedded;
use Yiisoft\Validator\SimpleRuleHandlerContainer;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipNone;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipOnNull;
use Yiisoft\Validator\Tests\Stub\InheritAttributesObject\InheritAttributesObject;
use Yiisoft\Validator\Tests\Stub\ObjectWithDifferentPropertyVisibility;
use Yiisoft\Validator\Validator;

final class EmbeddedTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $rule = new Embedded();

        $this->assertSame(
            ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PUBLIC,
            $rule->getPropertyVisibility(),
        );
        $this->assertFalse($rule->getSkipOnEmpty());
        $this->assertInstanceOf(SkipNone::class, $rule->getSkipOnEmptyCallback());
        $this->assertFalse($rule->shouldSkipOnError());
        $this->assertNull($rule->getWhen());
    }

    public function testPropertyVisibilityInConstructor(): void
    {
        $rule = new Embedded(propertyVisibility: ReflectionProperty::IS_PRIVATE);

        $this->assertSame(ReflectionProperty::IS_PRIVATE, $rule->getPropertyVisibility());
    }

    public function testSkipOnEmptyInConstructor(): void
    {
        $rule = new Embedded(skipOnEmpty: true);

        $this->assertTrue($rule->getSkipOnEmpty());
    }

    public function testSkipOnEmptyCallbackInConstructor(): void
    {
        $rule = new Embedded(skipOnEmptyCallback: new SkipOnNull());

        $this->assertInstanceOf(SkipOnNull::class, $rule->getSkipOnEmptyCallback());
    }

    public function testSkipOnErrorInConstructor(): void
    {
        $rule = new Embedded(skipOnError: true);

        $this->assertTrue($rule->shouldSkipOnError());
    }

    public function testWhenInConstructor(): void
    {
        $rule = new Embedded(when: static fn (): bool => true);

        $this->assertInstanceOf(Closure::class, $rule->getWhen());
    }

    public function dataHandler(): array
    {
        return [
            'base' => [
                new class() {
                    #[Embedded]
                    private ObjectWithDifferentPropertyVisibility $object;

                    public function __construct()
                    {
                        $this->object = new ObjectWithDifferentPropertyVisibility();
                    }
                },
                [
                    'object.name' => ['Value cannot be blank.'],
                    'object.age' => ['Value must be no less than 21.'],
                ],
            ],
            'only-public' => [
                new class() {
                    #[Embedded(propertyVisibility: ReflectionProperty::IS_PUBLIC)]
                    private ObjectWithDifferentPropertyVisibility $object;

                    public function __construct()
                    {
                        $this->object = new ObjectWithDifferentPropertyVisibility();
                    }
                },
                [
                    'object.name' => ['Value cannot be blank.'],
                ],
            ],
            'only-protected' => [
                new class() {
                    #[Embedded(propertyVisibility: ReflectionProperty::IS_PROTECTED)]
                    private ObjectWithDifferentPropertyVisibility $object;

                    public function __construct()
                    {
                        $this->object = new ObjectWithDifferentPropertyVisibility();
                    }
                },
                [
                    'object.age' => ['Value must be no less than 21.'],
                ],
            ],
            'inherit-attributes' => [
                new class() {
                    #[Embedded]
                    private $object;

                    public function __construct()
                    {
                        $this->object = new InheritAttributesObject();
                    }
                },
                [
                    'object.age' => [
                        'Value must be no less than 21.',
                        'Value must be equal to "23".',
                    ],
                    'object.number' => ['Value must be equal to "99".'],
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
        $result = $this->createValidator()->validate($data);

        $this->assertSame($expectedIsValid, $result->isValid());
        if (!$expectedIsValid) {
            $this->assertSame($expectedErrorMessagesIndexedByPath, $result->getErrorMessagesIndexedByPath());
        }
    }

    private function createValidator(): Validator
    {
        return new Validator(new SimpleRuleHandlerContainer());
    }
}
