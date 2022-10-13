<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Base;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Tests\Support\ValidatorFactory;

abstract class RuleTestCase extends TestCase
{
    abstract function dataValidationPassed(): array;

    public function beforeTestValidationPassed(): void
    {
    }

    /**
     * @dataProvider dataValidationPassed
     */
    public function testValidationPassed(mixed $data, array $rules): void
    {
        $this->beforeTestValidationPassed();

        $result = ValidatorFactory::make()->validate($data, $rules);

        $this->assertTrue($result->isValid());
    }

    abstract function dataValidationFailed(): array;

    public function beforeTestValidationFailed(): void
    {
    }

    /**
     * @dataProvider dataValidationFailed
     */
    public function testValidationFailed(mixed $data, array $rules, array $errorMessagesIndexedByPath): void
    {
        $this->beforeTestValidationFailed();

        $result = ValidatorFactory::make()->validate($data, $rules);

        $this->assertFalse($result->isValid());
        $this->assertSame($errorMessagesIndexedByPath, $result->getErrorMessagesIndexedByPath());
    }
}
