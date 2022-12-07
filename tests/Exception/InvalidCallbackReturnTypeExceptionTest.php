<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Exception\InvalidCallbackReturnTypeException;

final class InvalidCallbackReturnTypeExceptionTest extends TestCase
{
    public function testGetCode(): void
    {
        $exception = new InvalidCallbackReturnTypeException('test');
        $this->assertSame(0, $exception->getCode());
    }

    public function testReturnTypes(): void
    {
        $exception = new InvalidCallbackReturnTypeException('test');
        $this->assertIsString($exception->getName());
        $this->assertIsString($exception->getSolution());
    }
}
