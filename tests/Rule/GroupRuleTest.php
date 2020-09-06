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

    public function testOptions(): void
    {
        $validator = new CustomUrlRule();
        $this->assertEquals([
            [
                'required',
                'message' => 'Value cannot be blank.',
                'skipOnEmpty' => false,
                'skipOnError' => true,
            ],
            [
                'url',
                'message' => 'This value is not a valid URL.',
                'enableIDN' => true,
                'validSchemes' => ['http', 'https',],
                'pattern' => '/^{schemes}:\\/\\/(([A-Z0-9][A-Z0-9_-]*)(\\.[A-Z0-9][A-Z0-9_-]*)+)(?::\\d{1,5})?(?:$|[?\\/#])/i',
                'skipOnEmpty' => false,
                'skipOnError' => true,
            ],
            [
                'hasLength',
                'message' => 'This value must be a string.',
                'min' => null,
                'tooShortMessage' => 'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
                'max' => 20,
                'tooLongMessage' => 'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
                'encoding' => 'UTF-8',
                'skipOnEmpty' => false,
                'skipOnError' => true,
            ],
        ], $validator->getOptions());
    }
}
