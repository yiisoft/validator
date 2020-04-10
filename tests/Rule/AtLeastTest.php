<?php

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\MissingAttributeException;
use Yiisoft\Validator\Rule\AtLeast;

/**
 * @group validators
 */
class AtLeastTest extends TestCase
{
    public function testAtLeastOne(): void
    {
        $model = new \stdClass();
        $model->attr1 = 1;
        $model->attr2 = null;
        $model->attr3 = null;

        $rule = new AtLeast(['attributes' => ['attr1'], 'alternativeAttributes' => ['attr2']]);

        $this->assertTrue($rule->validate($model)->isValid());
        $this->assertEquals([], $rule->validate($model)->getErrors());
    }

    public function testAtLeastOneWithOnlyAttributes(): void
    {
        $model = new \stdClass();
        $model->attr1 = null;
        $model->attr2 = 1;
        $model->attr3 = null;

        $rule = new AtLeast(['attributes' => ['attr2']]);

        $this->assertTrue($rule->validate($model)->isValid());
        $this->assertEquals([], $rule->validate($model)->getErrors());
    }

    public function testAtLeastOneWithOnlyAlternativeAttributes(): void
    {
        $model = new \stdClass();
        $model->attr1 = null;
        $model->attr2 = 1;
        $model->attr3 = null;

        $rule = new AtLeast(['alternativeAttributes' => ['attr2']]);

        $this->assertTrue($rule->validate($model)->isValid());
        $this->assertEquals([], $rule->validate($model)->getErrors());
    }

    public function testAtLeastWithError(): void
    {
        $model = new \stdClass();
        $model->attr1 = null;
        $model->attr2 = null;
        $model->attr3 = 1;

        $rule = new AtLeast(['attributes' => ['attr1'], 'alternativeAttributes' => ['attr2']]);

        $this->assertFalse($rule->validate($model)->isValid());
        $this->assertCount(1, $rule->validate($model)->getErrors());
        $this->assertEquals(['Model should have "1" error attributes.'], $rule->validate($model)->getErrors());
    }

    public function testAtLeastMinAttribute(): void
    {
        $model = new \stdClass();
        $model->attr1 = 1;
        $model->attr2 = null;

        $rule = new AtLeast(['attributes' => ['attr1'], 'alternativeAttributes' => ['attr2'], 'min' => 2]);

        $this->assertFalse($rule->validate($model)->isValid());
        $this->assertCount(1, $rule->validate($model)->getErrors());
        $this->assertEquals(['Model should have "2" error attributes.'], $rule->validate($model)->getErrors());
    }

    public function testAtLeastWithInvalidParams(): void
    {
        $model = new \stdClass();
        $model->attr1 = 1;
        $model->attr2 = null;

        $rule = new AtLeast(['at1' => ['attr1'], 'at2' => ['attr2']]);

        $this->assertFalse($rule->validate($model)->isValid());
        $this->assertCount(1, $rule->validate($model)->getErrors());
        $this->assertEquals(['Model should have "1" error attributes.'], $rule->validate($model)->getErrors());
    }
}
