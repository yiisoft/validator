<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\RuleResult;
use Yiisoft\Validator\Rule\Callback;

class CallbackTest extends TestCase
{
    public function testValidate(): void
    {
        $rule = new Callback(
            static function ($value): RuleResult {
                $result = new RuleResult();
                if ($value !== 42) {
                    $result->addError('Value should be 42!');
                }
                return $result;
            }
        );

        $result = $rule->validate(41);

        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrors());
    }
}
