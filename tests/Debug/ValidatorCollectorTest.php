<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Debug;

use Yiisoft\Validator\Debug\ValidatorCollector;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Tests\Shared\AbstractCollectorTestCase;

final class ValidatorCollectorTest extends AbstractCollectorTestCase
{
    /**
     * @param CollectorInterface|ValidatorCollector $collector
     */
    protected function collectTestData(CollectorInterface|ValidatorCollector $collector): void
    {
        $ruleNumber = new Number(min: 200);

        $result = new Result();
        $result->addError($ruleNumber->getLessThanMinMessage());

        $collector->collect(123, $result, [$ruleNumber]);

        $result = new Result();

        $collector->collect(500, $result, [$ruleNumber]);
    }

    protected function getCollector(): CollectorInterface
    {
        return new ValidatorCollector();
    }

    protected function checkSummaryData(array $data): void
    {
        $this->assertArrayHasKey('total', $data);
        $this->assertArrayHasKey('valid', $data);
        $this->assertArrayHasKey('invalid', $data);

        $this->assertEquals(2, $data['total']);
        $this->assertEquals(1, $data['valid']);
        $this->assertEquals(1, $data['invalid']);
    }
}
