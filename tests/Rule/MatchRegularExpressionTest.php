<?php
namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\MatchRegularExpression;

/**
 * @group validators
 */
class MatchRegularExpressionTest extends TestCase
{
    public function testvalidate()
    {
        $rule = new MatchRegularExpression('/^[a-zA-Z0-9](\.)?([^\/]*)$/m');
        $this->assertTrue($rule->validate('b.4')->isValid());
        $this->assertFalse($rule->validate('b./')->isValid());
        $this->assertFalse($rule->validate(['a', 'b'])->isValid());

        $rule = (new MatchRegularExpression('/^[a-zA-Z0-9](\.)?([^\/]*)$/m'))->not();
        $this->assertFalse($rule->validate('b.4')->isValid());
        $this->assertTrue($rule->validate('b./')->isValid());
        $this->assertFalse($rule->validate(['a', 'b'])->isValid());
    }
}
