<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\AtLeast;
use Yiisoft\Validator\Tests\FormatterMock;

/**
 * @group validators
 */
class AtLeastTest extends FormatterMock
{
    public function testAtLeastOne(): void
    {
        $model = new \stdClass();
        $model->attr1 = 1;
        $model->attr2 = null;
        $model->attr3 = null;

        $rule = new AtLeast(['attr1', 'attr2']);

        $this->assertTrue($rule->validate($model)->isValid());
        $this->assertEquals([], $rule->validate($model)->getErrors());
    }

    public function testAtLeastOneWithOnlyAttributes(): void
    {
        $model = new \stdClass();
        $model->attr1 = null;
        $model->attr2 = 1;
        $model->attr3 = null;

        $rule = new AtLeast(['attr2']);

        $this->assertTrue($rule->validate($model)->isValid());
        $this->assertEquals([], $rule->validate($model)->getErrors());
    }

    public function testAtLeastWithError(): void
    {
        $model = new \stdClass();
        $model->attr1 = null;
        $model->attr2 = null;
        $model->attr3 = 1;

        $rule = new AtLeast(['attr1', 'attr2']);

        $this->assertFalse($rule->validate($model)->isValid());
        $this->assertCount(1, $rule->validate($model)->getErrors());
        $this->assertEquals(
            ['The model is not valid. Must have at least "1" filled attributes.'],
            $rule->validate($model)->getErrors()
        );
    }

    public function testAtLeastMinAttribute(): void
    {
        $model = new \stdClass();
        $model->attr1 = 1;
        $model->attr2 = null;

        $rule = (new AtLeast(['attr1', 'attr2']))->min(2);

        $this->assertFalse($rule->validate($model)->isValid());
        $this->assertCount(1, $rule->validate($model)->getErrors());
        $this->assertEquals(
            ['The model is not valid. Must have at least "2" filled attributes.'],
            $rule->validate($model)->getErrors()
        );
    }

    public function testName(): void
    {
        $this->assertEquals('atLeast', (new AtLeast(['attr1', 'attr2']))->getName());
    }

    public function optionsProvider(): array
    {
        return [
            [
                (new AtLeast(['attr1', 'attr2'])),
                [
                    'min' => 1,
                    'message' => 'The model is not valid. Must have at least "1" filled attributes.',
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ],
            ],
            [
                (new AtLeast(['attr1', 'attr2']))->min(2),
                [
                    'min' => 2,
                    'message' => 'The model is not valid. Must have at least "2" filled attributes.',
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
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
        $this->assertEquals(
            array_merge(
                $expected,
                ['message' => 'Translate: ' . $expected['message']]
            ),
            $rule->getOptions($this->createFormatterMock())
        );

        $this->assertEquals($expected, $rule->getOptions());
    }
}
