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
        $rule = new CustomUrlRule();

        $this->assertTrue($rule->validate('http://домен.рф')->isValid());
        $this->assertFalse($rule->validate('http://доменбольшедвадцатизнаков.рф')->isValid());
        $this->assertFalse($rule->validate(null)->isValid());
    }

    public function testErrorMessage(): void
    {
        $rule = new CustomUrlRule();
        $this->assertEquals(['This value is not a valid.'], $rule->validate('domain')->getErrorMessages());
    }

    public function testCustomErrorMessage(): void
    {
        $rule = new CustomUrlRule(message: 'This value is not valid custom url');
        $this->assertEquals(['This value is not valid custom url'], $rule->validate('domain')->getErrorMessages());
    }

    public function testOptions(): void
    {
        $rule = new CustomUrlRule();
        $this->assertEquals([
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
        ], $rule->getOptions());
    }
}
