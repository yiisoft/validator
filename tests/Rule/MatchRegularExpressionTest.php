<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\MatchRegularExpression;

/**
 * @group validators
 */
class MatchRegularExpressionTest extends TestCase
{
    private const PATTERN = '/^[a-zA-Z0-9](\.)?([^\/]*)$/m';

    public function testGetName(): void
    {
        $this->assertSame('matchRegularExpression', MatchRegularExpression::rule(self::PATTERN)->getName());
    }

    public function validateDataProvider(): array
    {
        return [
            [['a', 'b'], false, false],
            [null, false, false],
            [new stdClass(), false, false],

            ['b.4', true, false],
            ['b./', false, true],
        ];
    }

    /**
     * @dataProvider validateDataProvider
     */
    public function testValidate($data, bool $expectedIsValid, bool $expectedIsValidForInverseRule): void
    {
        $rule = MatchRegularExpression::rule(self::PATTERN);
        $this->assertSame($rule->validate($data)->isValid(), $expectedIsValid);
        $this->assertSame($rule->not()->validate($data)->isValid(), $expectedIsValidForInverseRule);
    }

    public function testMessage(): void
    {
        $rule = MatchRegularExpression::rule(self::PATTERN)->message('Custom message.');
        $this->assertSame(['Custom message.'], $rule->validate('b./')->getErrorMessages());
    }

    public function testIncorrectInputMessage(): void
    {
        $rule = MatchRegularExpression::rule(self::PATTERN)->incorrectInputMessage('Custom message.');
        $this->assertSame(['Custom message.'], $rule->validate(null)->getErrorMessages());
    }

    public function getOptionsProvider(): array
    {
        return [
            [
                MatchRegularExpression::rule(self::PATTERN),
                [
                    'incorrectInputMessage' => 'Value should be string.',
                    'message' => 'Value is invalid.',
                    'not' => false,
                    'pattern' => self::PATTERN,
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ],
            ],
            [
                MatchRegularExpression::rule(self::PATTERN)->not(),
                [
                    'incorrectInputMessage' => 'Value should be string.',
                    'message' => 'Value is invalid.',
                    'not' => true,
                    'pattern' => self::PATTERN,
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ],
            ],
        ];
    }

    /**
     * @dataProvider getOptionsProvider
     */
    public function testGetOptions(Rule $rule, array $expected): void
    {
        $this->assertEquals($expected, $rule->getOptions());
    }
}
