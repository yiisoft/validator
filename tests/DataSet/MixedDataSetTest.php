<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\DataSet;

use BadMethodCallException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\DataSet\SingleValueDataSet;

final class MixedDataSetTest extends TestCase
{
    public function testGetData(): void
    {
        $data = new SingleValueDataSet(['test' => 'hello']);
        $this->assertSame(['test' => 'hello'], $data->getData());
    }

    public function testHasAttribute(): void
    {
        $data = new SingleValueDataSet(['test' => 'hello']);

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Single value data set does not support attributes.');
        $data->hasAttribute('test');
    }

    public function testGetAttributeValue(): void
    {
        $data = new SingleValueDataSet(['test' => 'hello']);

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Single value data set does not support attributes.');
        $data->getAttributeValue('test');
    }
}
