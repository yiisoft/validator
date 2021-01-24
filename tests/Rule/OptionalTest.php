<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Optional;
use Yiisoft\Validator\ValidationContext;

final class OptionalTest extends TestCase
{
    public function testBase(): void
    {
        $rule = new Optional(
            new Number()
        );

        $context = new ValidationContext(
            $this->createDataSet(['code' => '42']),
            'code',
        );

        $this->assertTrue($rule->validate('42', $context)->isValid());
    }

    public function testWithoutContext(): void
    {
        $rule = new Optional(
            new Number()
        );

        $this->assertTrue($rule->validate('42')->isValid());
        $this->assertFalse($rule->validate('hello')->isValid());
    }

    public function testEmpty(): void
    {
        $rule = new Optional(
            new Number()
        );

        $context = new ValidationContext(
            $this->createDataSet(['code' => '']),
            'code',
        );

        $this->assertTrue($rule->validate('', $context)->isValid());
    }

    public function testEmptyCallback(): void
    {
        $rule = (new Optional(new Number()))
            ->emptyCallback(fn ($value) => $value == '0');

        $context = new ValidationContext(
            $this->createDataSet(['code' => '']),
            'code',
        );
        $this->assertFalse($rule->validate('', $context)->isValid());
    }

    public function testCheckEmpty(): void
    {
        $rule = (new Optional(new Number()))
            ->checkEmpty(false);

        $context = new ValidationContext(
            $this->createDataSet(['code' => '']),
            'code',
        );
        $this->assertFalse($rule->validate('', $context)->isValid());

        $context = new ValidationContext(
            $this->createDataSet(['name' => 'mike']),
            'code',
        );
        $this->assertTrue($rule->validate(null, $context)->isValid());
    }

    public function testImmutability(): void
    {
        $rule = new Optional();
        $this->assertNotSame($rule, $rule->rules());
        $this->assertNotSame($rule, $rule->checkEmpty(false));
        $this->assertNotSame($rule, $rule->emptyCallback(null));
    }

    private function createDataSet(array $attributes): DataSetInterface
    {
        return new class($attributes) implements DataSetInterface {
            private array $attributes;

            public function __construct(array $attributes)
            {
                $this->attributes = $attributes;
            }

            public function getAttributeValue(string $attribute)
            {
                return $this->hasAttribute($attribute) ? $this->attributes[$attribute] : null;
            }

            public function hasAttribute(string $attribute): bool
            {
                return array_key_exists($attribute, $this->attributes);
            }
        };
    }
}
