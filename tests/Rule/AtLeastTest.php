<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\AtLeast;

class AtLeastTest extends TestCase
{
    public function testAtLeastOne(): void
    {
        $model = new stdClass();
        $model->attr1 = 1;
        $model->attr2 = null;
        $model->attr3 = null;

        $rule = new AtLeast(['attr1', 'attr2']);
        $result = $rule->validate($model);

        $this->assertTrue($result->isValid());
    }

    public function testAtLeastOneWithOnlyAttributes(): void
    {
        $model = new stdClass();
        $model->attr1 = null;
        $model->attr2 = 1;
        $model->attr3 = null;

        $rule = new AtLeast(['attr2']);
        $result = $rule->validate($model);

        $this->assertTrue($result->isValid());
    }

    public function testAtLeastWithError(): void
    {
        $model = new stdClass();
        $model->attr1 = null;
        $model->attr2 = null;
        $model->attr3 = 1;

        $rule = new AtLeast(['attr1', 'attr2']);
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

        $rule = new AtLeast(['attr1', 'attr2'], min: 2);
        $result = $rule->validate($model);

        $this->assertFalse($result->isValid());
        $this->assertEquals(
            ['The model is not valid. Must have at least "2" filled attributes.'],
            $result->getErrorMessages()
        );
    }

    public function testGetName(): void
    {
        $rule = new AtLeast(['attr1', 'attr2']);
        $this->assertEquals('atLeast', $rule->getName());
    }

    public function getOptionsProvider(): array
    {
        return [
            [
                new AtLeast(['attr1', 'attr2']),
                [
                    'attributes' => ['attr1', 'attr2'],
                    'min' => 1,
                    'message' => 'The model is not valid. Must have at least "1" filled attributes.',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new AtLeast(['attr1', 'attr2'], min: 2),
                [
                    'attributes' => ['attr1', 'attr2'],
                    'min' => 2,
                    'message' => 'The model is not valid. Must have at least "2" filled attributes.',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider getOptionsProvider
     */
    public function testGetOptions(Rule $rule, array $expectedOptions): void
    {
        $this->assertEquals($expectedOptions, $rule->getOptions());
    }
}
