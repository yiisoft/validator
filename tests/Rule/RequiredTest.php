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

    public function optionsProvider(): array
    {
        return [
            [
                new Required(),
                [
                    'message' => 'Value cannot be blank.',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider optionsProvider
     *
     * @param Rule $rule
     * @param array $expected
     */
    public function testOptions(Rule $rule, array $expected): void
    {
        $this->assertEquals($expected, $rule->getOptions());
    }

//    public function testValidateAttribute()
//    {
//        // empty req-value
//        $val = new Required();
//        $m = FakedValidationModel::createWithAttributes(['attr_val' => null]);
//        $val->validateAttribute($m, 'attr_val');
//        $this->assertTrue($m->hasErrors('attr_val'));
//        $this->assertNotFalse(stripos(current($m->getErrors('attr_val')), 'blank'));
//        $val = new Required(['requiredValue' => 55]);
//        $m = FakedValidationModel::createWithAttributes(['attr_val' => 56]);
//        $val->validateAttribute($m, 'attr_val');
//        $this->assertTrue($m->hasErrors('attr_val'));
//        $this->assertNotFalse(stripos(current($m->getErrors('attr_val')), 'must be'));
//        $val = new Required(['requiredValue' => 55]);
//        $m = FakedValidationModel::createWithAttributes(['attr_val' => 55]);
//        $val->validateAttribute($m, 'attr_val');
//        $this->assertFalse($m->hasErrors('attr_val'));
//    }
}
