<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\AtLeast;
use Yiisoft\Validator\Rule\AtLeastHandler;
use Yiisoft\Validator\Tests\Support\ValidatorFactory;
use Yiisoft\Validator\Tests\Support\RuleWithCustomHandler;
use Yiisoft\Validator\Validator;

final class AtLeastTest extends TestCase
{
    public function testGetName(): void
    {
        $rule = new AtLeast([]);
        $this->assertSame('atLeast', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new AtLeast(['attr1', 'attr2']),
                [
                    'attributes' => [
                        'attr1',
                        'attr2',
                    ],
                    'min' => 1,
                    'message' => [
                        'message' => 'The model is not valid. Must have at least "{min}" filled attributes.',
                        'parameters' => ['min' => 1],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new AtLeast(['attr1', 'attr2'], min: 2),
                [
                    'attributes' => [
                        'attr1',
                        'attr2',
                    ],
                    'min' => 2,
                    'message' => [
                        'message' => 'The model is not valid. Must have at least "{min}" filled attributes.',
                        'parameters' => ['min' => 2],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataOptions
     */
    public function testOptions(AtLeast $rule, array $expectedOptions): void
    {
        $options = $rule->getOptions();
        $this->assertSame($expectedOptions, $options);
    }

    public function dataValidationPassed(): array
    {
        return [
            [
                new class () {
                    public $attr1 = 1;
                    public $attr2 = null;
                },
                [new AtLeast(['attr1', 'attr2'])],
            ],
            [
                new class () {
                    public $attr1 = null;
                    public $attr2 = 1;
                },
                [new AtLeast(['attr2'])],
            ],
            [
                ['attr1' => 1, 'attr2' => null],
                [new AtLeast(['attr1', 'attr2'])],
            ],
            [
                ['attr1' => null, 'attr2' => 1],
                [new AtLeast(['attr2'])],
            ],
            [
                new class () {
                    public $obj;

                    public function __construct()
                    {
                        $this->obj = new class () {
                            public $attr1 = 1;
                            public $attr2 = null;
                        };
                    }
                },
                ['obj' => new AtLeast(['attr1', 'attr2'])],
            ],
            [
                new class () {
                    public $obj;

                    public function __construct()
                    {
                        $this->obj = new class () {
                            public $attr1 = null;
                            public $attr2 = 1;
                        };
                    }
                },
                ['obj' => new AtLeast(['attr2'])],
            ],
            [
                ['obj' => ['attr1' => 1, 'attr2' => null]],
                ['obj' => new AtLeast(['attr1', 'attr2'])],
            ],
            [
                ['obj' => ['attr1' => null, 'attr2' => 1]],
                ['obj' => new AtLeast(['attr2'])],
            ],
        ];
    }

    /**
     * @dataProvider dataValidationPassed
     */
    public function testValidationPassed(mixed $data, array $rules): void
    {
        $result = $this->createValidator()->validate($data, $rules);

        $this->assertTrue($result->isValid());
    }

    public function dataValidationFailed(): array
    {
        return [
            [
                new class () {
                    public $attr1 = 1;
                    public $attr2 = null;
                },
                [new AtLeast(['attr2'])],
                ['' => ['The model is not valid. Must have at least "1" filled attributes.']],
            ],
            [
                new class () {
                    public $attr1 = 1;
                    public $attr2 = null;
                },
                [new AtLeast(['attr1', 'attr2'], min: 2)],
                ['' => ['The model is not valid. Must have at least "2" filled attributes.']],
            ],
        ];
    }

    /**
     * @dataProvider dataValidationFailed
     */
    public function testValidationFailed(object $object, array $rules, array $errorMessagesIndexedByPath): void
    {
        $result = $this->createValidator()->validate($object, $rules);

        $this->assertFalse($result->isValid());
        $this->assertSame($errorMessagesIndexedByPath, $result->getErrorMessagesIndexedByPath());
    }

    public function testCustomErrorMessage(): void
    {
        $object = new class () {
            public $attr1 = 1;
            public $attr2 = null;
        };
        $rules = [new AtLeast(['attr1', 'attr2'], min: 2, message: 'Custom error')];

        $result = $this->createValidator()->validate($object, $rules);

        $this->assertFalse($result->isValid());
        $this->assertSame(
            ['' => ['Custom error']],
            $result->getErrorMessagesIndexedByPath()
        );
    }

    public function testDifferentRuleInHandler(): void
    {
        $rule = new RuleWithCustomHandler(AtLeastHandler::class);
        $validator = $this->createValidator();

        $this->expectExceptionMessageMatches(
            '/.*' . preg_quote(AtLeast::class) . '.*' . preg_quote(RuleWithCustomHandler::class) . '.*/'
        );
        $validator->validate([], [$rule]);
    }

    private function createValidator(): Validator
    {
        return ValidatorFactory::make();
    }
}
