<?php


namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;

class CallbackTest extends TestCase
{
    public function testValidate(): void
    {
        $rule = new Callback(static function ($value): Result {
            $result = new Result();
            if ($value !== 42) {
                $result = $result->addError('Value should be 42!');
            }
            return $result;
        });

        $result = $rule->validate(41);

        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrors());
    }
}
