<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Exception\CallbackRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\Callback;

class CallbackTest extends TestCase
{
    public function testValidate(): void
    {
        $rule = new Callback(
            static function ($value): Result {
                $result = new Result();
                if ($value !== 42) {
                    $result->addError('Value should be 42!');
                }
                return $result;
            }
        );

        $result = $rule->validate(41);

        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrors());
        $this->assertEquals('Value should be 42!', $result->getErrors()[0]);
    }

    public function testThrowExceptionWithInvalidReturn(): void
    {
        $this->expectException(CallbackRuleException::class);

        (new Callback(
            static fn () => 'invalid return'
        ))->validate(null);
    }

    public function testName(): void
    {
        $this->assertEquals('callback', (new Callback(static function ($value) {
            return $value;
        }))->getName());
    }

    public function optionsProvider(): array
    {
        return [
            [
                (new Callback(static function ($value) {
                    return $value;
                })),
                [
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ],
            ],
            [
                (new Callback(static function ($value) {
                    return $value;
                }))->skipOnEmpty(true),
                [
                    'skipOnEmpty' => true,
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
