<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\DataSet;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\DataSet\SingleValueDataSet;

final class SingleValueDataSetTest extends TestCase
{
    public function testBase(): void
    {
        $data = new SingleValueDataSet(['test' => 'hello']);

        $this->assertNull($data->getData());
        $this->assertSame(['test' => 'hello'], $data->getSource());
        $this->assertFalse($data->hasProperty('test'));
        $this->assertNull($data->getPropertyValue('test'));
    }
}
