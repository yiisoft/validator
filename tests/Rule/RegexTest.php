<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\Regex;

class RegexTest extends TestCase
{
    private const PATTERN = '/^[a-zA-Z0-9](\.)?([^\/]*)$/m';

    public function testGetName(): void
    {
        $rule = new Regex(self::PATTERN);
        $this->assertSame('regex', $rule->getName());
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
        $rule = new Regex(self::PATTERN);
        $this->assertSame($rule->validate($data)->isValid(), $expectedIsValid);

        $rule = new Regex(self::PATTERN, not: true);
        $this->assertSame($rule->validate($data)->isValid(), $expectedIsValidForInverseRule);
    }

    public function testMessage(): void
    {
        $rule = new Regex(self::PATTERN, message: 'Custom message.');
        $this->assertSame(['Custom message.'], $rule->validate('b./')->getErrorMessages());
    }

    public function testIncorrectInputMessage(): void
    {
        $rule = new Regex(self::PATTERN, incorrectInputMessage: 'Custom message.');
        $this->assertSame(['Custom message.'], $rule->validate(null)->getErrorMessages());
    }

    public function getOptionsProvider(): array
    {
        return [
            [
                new Regex(self::PATTERN),
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
                new Regex(self::PATTERN, not: true),
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
    public function testGetOptions(Rule $rule, array $expectedOptions): void
    {
        $this->assertEquals($expectedOptions, $rule->getOptions());
    }
}
