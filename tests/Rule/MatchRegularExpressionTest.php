<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\MatchRegularExpression;

/**
 * @group validators
 */
class MatchRegularExpressionTest extends TestCase
{
    public function testValidate(): void
    {
        $rule = MatchRegularExpression::rule('/^[a-zA-Z0-9](\.)?([^\/]*)$/m');
        $this->assertTrue($rule->validate('b.4')->isValid());
        $this->assertFalse($rule->validate('b./')->isValid());
        $this->assertFalse($rule->validate(['a', 'b'])->isValid());

        $rule = MatchRegularExpression::rule('/^[a-zA-Z0-9](\.)?([^\/]*)$/m')->not();
        $this->assertFalse($rule->validate('b.4')->isValid());
        $this->assertTrue($rule->validate('b./')->isValid());
        $this->assertFalse($rule->validate(['a', 'b'])->isValid());
    }

    public function testName(): void
    {
        $this->assertEquals('matchRegularExpression', MatchRegularExpression::rule('/^[a-zA-Z0-9](\.)?([^\/]*)$/m')->getName());
    }

    public function optionsProvider(): array
    {
        $pattern = '/^[a-zA-Z0-9](\.)?([^\/]*)$/m';
        return [
            [
                MatchRegularExpression::rule($pattern),
                [
                    'message' => 'Value is invalid.',
                    'not' => false,
                    'pattern' => $pattern,
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ],
            ],
            [
                MatchRegularExpression::rule($pattern)->not(),
                [
                    'message' => 'Value is invalid.',
                    'not' => true,
                    'pattern' => $pattern,
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
        $this->assertEquals($expected, $rule->getOptions());
    }
}
