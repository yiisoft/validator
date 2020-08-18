<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Tests\Stub\CustomUrlRule;

/**
 * @group validators
 */
class GroupRuleTest extends TestCase
{
    public function testValidate(): void
    {
        $validator = new CustomUrlRule();

        $this->assertTrue($validator->validate('http://домен.рф')->isValid());
        $this->assertFalse($validator->validate('http://доменбольшедвадцатизнаков.рф')->isValid());
        $this->assertFalse($validator->validate(null)->isValid());
    }

    public function testErrorMessage(): void
    {
        $validator = new CustomUrlRule();
        $this->assertEquals(['This value is not a valid.'], $validator->validate('domain')->getErrors());
    }

    public function testCustomErrorMessage(): void
    {
        $validator = (new CustomUrlRule())->message('This value is not valid custom url');
        $this->assertEquals(['This value is not valid custom url'], $validator->validate('domain')->getErrors());
    }
}
