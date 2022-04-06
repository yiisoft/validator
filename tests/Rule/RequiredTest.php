<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Required;

class RequiredTest extends TestCase
{
    public function validateWithDefaultsProvider(): array
    {
        return [
            [null, false],
            [[], false],
            ['not empty', true],
            [['with', 'elements'], true],
        ];
    }

    /**
     * @dataProvider validateWithDefaultsProvider
     */
    public function testValidateWithDefaults(mixed $value, bool $expectedIsValid): void
    {
        $rule = new Required();
        $result = $rule->validate($value);

        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function testName(): void
    {
        $rule = new Required();
        $this->assertEquals('required', $rule->getName());
    }

    public function testOptions(): void
    {
        $rule = new Required();
        $expectedOptions = [
            'message' => 'Value cannot be blank.',
            'skipOnEmpty' => false,
            'skipOnError' => false,
        ];

        $this->assertEquals($expectedOptions, $rule->getOptions());
    }
}
