<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\DataSet;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\DataSet\MixedDataSet;

final class MixedDataSetTest extends TestCase
{
    public function testBase(): void
    {
        $data = new MixedDataSet(['test' => 'hello']);

        $this->assertNull($data->getAttributeValue('test'));
        $this->assertSame(['test' => 'hello'], $data->getData());
    }
}
