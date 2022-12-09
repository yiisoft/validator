<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Base;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Validator;

abstract class RuleTestCase extends TestCase
{
    abstract public function dataValidationPassed(): array;

    /**
     * @dataProvider dataValidationPassed
     */
    public function testValidationPassed(mixed $data, ?array $rules = null): void
    {
        $result = (new Validator())->validate($data, $rules);

        $this->assertTrue($result->isValid());
    }

    abstract public function dataValidationFailed(): array;

    /**
     * @dataProvider dataValidationFailed
     */
    public function testValidationFailed(mixed $data, array|null $rules, array $errorMessagesIndexedByPath): void
    {
        $result = (new Validator())->validate($data, $rules);

        $this->assertFalse($result->isValid());
        $this->assertSame($errorMessagesIndexedByPath, $result->getErrorMessagesIndexedByPath());
    }
}
