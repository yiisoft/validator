<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\AtLeast;

/**
 * @group validators
 */
class AtLeastTest extends TestCase
{
    public function testAtLeastOne(): void
    {
        $model = new stdClass();
        $model->attr1 = 1;
        $model->attr2 = null;
        $model->attr3 = null;

        $rule = AtLeast::rule(['attr1', 'attr2']);

        $this->assertTrue($rule->validate($model)->isValid());
        $this->assertEquals([], $rule->validate($model)->getErrors());
    }

    public function testAtLeastOneWithOnlyAttributes(): void
    {
        $model = new stdClass();
        $model->attr1 = null;
        $model->attr2 = 1;
        $model->attr3 = null;

        $rule = AtLeast::rule(['attr2']);

        $this->assertTrue($rule->validate($model)->isValid());
        $this->assertEquals([], $rule->validate($model)->getErrors());
    }

    public function testAtLeastWithError(): void
    {
        $model = new stdClass();
        $model->attr1 = null;
        $model->attr2 = null;
        $model->attr3 = 1;

        $rule = AtLeast::rule(['attr1', 'attr2']);
        $result = $rule->validate($model);

        $this->assertFalse($result->isValid());
        $this->assertEquals(
            ['The model is not valid. Must have at least "1" filled attributes.'],
            $result->getErrorMessages()
        );
    }

    public function testAtLeastMinAttribute(): void
    {
        $model = new stdClass();
        $model->attr1 = 1;
        $model->attr2 = null;

        $rule = AtLeast::rule(['attr1', 'attr2'])->min(2);
        $result = $rule->validate($model);

        $this->assertFalse($result->isValid());
        $this->assertEquals(
            ['The model is not valid. Must have at least "2" filled attributes.'],
            $result->getErrorMessages()
        );
    }

    public function testName(): void
    {
        $this->assertEquals('atLeast', AtLeast::rule(['attr1', 'attr2'])->getName());
    }

    public function optionsProvider(): array
    {
        return [
            [
                AtLeast::rule(['attr1', 'attr2']),
                [
                    'min' => 1,
                    'message' => 'The model is not valid. Must have at least "1" filled attributes.',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                AtLeast::rule(['attr1', 'attr2'])->min(2),
                [
                    'min' => 2,
                    'message' => 'The model is not valid. Must have at least "2" filled attributes.',
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
}
