<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Benchmark;

use Generator;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\ParamProviders;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use ReflectionProperty;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\Tests\Stub\ObjectWithDifferentPropertyVisibility;

class ObjectDataSetBench
{
    private static $currentPropertyVisibility = ReflectionProperty::IS_PUBLIC;

    public function provideObjectDataSets(): Generator
    {
        yield 'one instance' => [
            'dataSet' => new ObjectDataSet(new ObjectWithDifferentPropertyVisibility()),
        ];
    }

    /**
     * @Revs(100000)
     * @Iterations(5)
     * @ParamProviders({"provideObjectDataSets"})
     */
    public function benchGetRulesWithOneInstance(array $params): void
    {
        $dataSet = $params['dataSet'];
        $dataSet->getRules();
    }

    /**
     * @Revs(100000)
     * @Iterations(5)
     */
    public function benchGetRulesWithNewInstanceAndCache(): void
    {
        $dataSet = new ObjectDataSet(new ObjectWithDifferentPropertyVisibility());
        $dataSet->getRules();
    }
}
