<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Tests\Stub\CustomUrlRule;

class GroupRuleTest extends TestCase
{
    public function validateProvider(): array
    {
        return [
            ['http://домен.рф', true],
            ['http://доменбольшедвадцатизнаков.рф', false],
            [null, false],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate(mixed $value, bool $expectedIsValid): void
    {
        $rule = new CustomUrlRule();
        $result = $rule->validate($value);

        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function testErrorMessage(): void
    {
        $rule = new CustomUrlRule();
        $result = $rule->validate('domain');

        $this->assertEquals(['This value is not a valid.'], $result->getErrorMessages());
    }

    public function testCustomErrorMessage(): void
    {
        $rule = new CustomUrlRule(message: 'This value is not valid custom url');
        $result = $rule->validate('domain');

        $this->assertEquals(['This value is not valid custom url'], $result->getErrorMessages());
    }

    public function testOptions(): void
    {
        $rule = new CustomUrlRule();
        $expectedOptions = [
            [
                'required',
                'message' => 'Value cannot be blank.',
                'skipOnEmpty' => false,
                'skipOnError' => false,
            ],
            [
                'url',
                'pattern' => '/^{schemes}:\/\/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)(?::\d{1,5})?([?\/#].*$|$)/',
                'validSchemes' => ['http', 'https',],
                'enableIDN' => true,
                'message' => 'This value is not a valid URL.',
                'skipOnEmpty' => false,
                'skipOnError' => false,
            ],
            [
                'hasLength',
                'min' => null,
                'max' => 20,
                'message' => 'This value must be a string.',
                'tooShortMessage' => 'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
                'tooLongMessage' => 'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
                'encoding' => 'UTF-8',
                'skipOnEmpty' => false,
                'skipOnError' => false,
            ],
        ];

        $this->assertEquals($expectedOptions, $rule->getOptions());
    }
}
