<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\DataSet;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\DataSet\ArrayDataSet;

final class ArrayDataSetTest extends TestCase
{
    public function testBase(): void
    {
        $data = new ArrayDataSet(['test' => 'hello']);

        $this->assertNull($data->getAttributeValue('non-exist'));
        $this->assertSame('hello', $data->getAttributeValue('test'));
        $this->assertSame(['test' => 'hello'], $data->getData());
    }
}
