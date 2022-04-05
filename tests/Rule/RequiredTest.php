<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\Required;

/**
 * @group validators
 */
class RequiredTest extends TestCase
{
    public function testValidateWithDefaults()
    {
        $rule = new Required();

        $this->assertFalse($rule->validate(null)->isValid());
        $this->assertFalse($rule->validate([])->isValid());
        $this->assertTrue($rule->validate('not empty')->isValid());
        $this->assertTrue($rule->validate(['with', 'elements'])->isValid());
    }

    public function testName(): void
    {
        $this->assertEquals('required', (new Required())->getName());
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
