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
            0 =>
                [
                    0 => 'required',
                    1 =>
                        [
                            'skipOnEmpty' => false,
                            'skipOnError' => true,
                            'message' => 'Value cannot be blank.',
                        ],
                ],
            1 =>
                [
                    0 => 'url',
                    1 =>
                        [
                            'skipOnEmpty' => false,
                            'skipOnError' => true,
                            'message' => 'This value is not a valid URL.',
                            'enableIDN' => true,
                            'validSchemes' =>
                                [
                                    0 => 'http',
                                    1 => 'https',
                                ],
                            'pattern' => '/^{schemes}:\\/\\/(([A-Z0-9][A-Z0-9_-]*)(\\.[A-Z0-9][A-Z0-9_-]*)+)(?::\\d{1,5})?(?:$|[?\\/#])/i',
                        ],
                ],
            2 =>
                [
                    0 => 'hasLength',
                    1 =>
                        [
                            'skipOnEmpty' => false,
                            'skipOnError' => true,
                            'message' => 'This value must be a string.',
                            'min' => NULL,
                            'tooShortMessage' => 'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
                            'max' => 20,
                            'tooLongMessage' => 'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
                            'encoding' => 'UTF-8',
                        ],
                ],
        ], $validator->getOptions());
    }
}
