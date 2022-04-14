<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\AtLeast;

class AtLeastTest extends TestCase
{
    public function testAttributes(): void
    {
        $rule1 = new AtLeast(['attr1']);
        $this->assertEquals(['attr1'], $rule1->getOptions()['attributes']);

        $rule2 = $rule1->attributes(['attr1', 'attr2']);
        $this->assertEquals(['attr1', 'attr2'], $rule2->getOptions()['attributes']);

        $this->assertNotSame($rule1, $rule2);
    }

    public function testMin(): void
    {
        $rule1 = new AtLeast(['attr1'], min: 2);
        $this->assertSame(2, $rule1->getOptions()['min']);

        $rule2 = $rule1->min(3);
        $this->assertSame(3, $rule2->getOptions()['min']);

        $this->assertNotSame($rule1, $rule2);
    }

    public function testMessage(): void
    {
        $rule1 = new AtLeast(['attr1'], message: 'Message 1.');
        $this->assertSame('Message 1.', $rule1->getOptions()['message']);

        $rule2 = $rule1->message('Message 2.');
        $this->assertSame('Message 2.', $rule2->getOptions()['message']);

        $this->assertNotSame($rule1, $rule2);
    }

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
