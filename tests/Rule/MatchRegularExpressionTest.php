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
        $this->assertSame('matchRegularExpression', (new MatchRegularExpression(self::PATTERN))->getName());
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
        $rule = new MatchRegularExpression(self::PATTERN);
        $this->assertSame($rule->validate($data)->isValid(), $expectedIsValid);

        $rule = new MatchRegularExpression(self::PATTERN, not: true);
        $this->assertSame($rule->validate($data)->isValid(), $expectedIsValidForInverseRule);
    }

    public function testMessage(): void
    {
        $rule = new MatchRegularExpression(self::PATTERN, message: 'Custom message.');
        $this->assertSame(['Custom message.'], $rule->validate('b./')->getErrorMessages());
    }

    public function testIncorrectInputMessage(): void
    {
        $rule = new MatchRegularExpression(self::PATTERN, incorrectInputMessage: 'Custom message.');
        $this->assertSame(['Custom message.'], $rule->validate(null)->getErrorMessages());
    }

    public function getOptionsProvider(): array
    {
        return [
            [
                new MatchRegularExpression(self::PATTERN),
                [
                    'pattern' => self::PATTERN,
                    'not' => false,
                    'incorrectInputMessage' => 'Value should be string.',
                    'message' => 'Value is invalid.',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new MatchRegularExpression(self::PATTERN, not: true),
                [
                    'pattern' => self::PATTERN,
                    'not' => true,
                    'incorrectInputMessage' => 'Value should be string.',
                    'message' => 'Value is invalid.',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
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
