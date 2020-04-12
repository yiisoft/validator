<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Json;

/**
 * @group validators
 */
class JsonTest extends TestCase
{
    public function testInvalidJsonValidate(): void
    {
        $val = new Json();
        $this->assertFalse($val->validate('{"name": "tester"')->isValid());
        $this->assertFalse($val->validate('{"name": tester}')->isValid());
    }

    public function testInvalidTypeValidate(): void
    {
        $val = new Json();
        $this->assertFalse($val->validate(['json'])->isValid());
        $this->assertFalse($val->validate(10)->isValid());
        $this->assertFalse($val->validate(null)->isValid());
    }

    public function testValidValueValidate(): void
    {
        $this->assertTrue((new Json())->validate('{"name": "tester"}')->isValid());
    }

    public function testValidationMessage(): void
    {
        $this->assertEquals(
            [
                'The value is not json'
            ],
            (new Json())->validate('')->getErrors()
        );
    }

    public function testCustomValidationMessage(): void
    {
        $this->assertEquals(
            [
                'bad json'
            ],
            (new Json())->message('bad json')->validate('')->getErrors()
        );
    }
}
