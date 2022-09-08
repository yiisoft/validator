<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Php81\Rule;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Tests\Stub\FakeValidatorFactory;
use Yiisoft\Validator\Tests\Stub\ObjectWithDifferentPropertyVisibility;
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
                    'object.number' => ['Value must be no greater than 7.'],
                ],
            ],
            'object-private-only' => [
                new class () {
                    #[Nested(
                        ['age' => new Number(min: 100, skipOnEmpty: true), 'number' => new Number(max: 7)],
                        propertyVisibility: ReflectionProperty::IS_PRIVATE
                    )]
                    private ObjectWithDifferentPropertyVisibility $object;

                    public function __construct()
                    {
                        $this->object = new ObjectWithDifferentPropertyVisibility();
                    }
                },
                [
                    'object.number' => ['Value must be no greater than 7.'],
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
        return FakeValidatorFactory::make();
    }
}
