<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Benchmark;

use Generator;
use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\ParamProviders;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use PhpBench\Benchmark\Metadata\Annotations\Warmup;
use Yiisoft\Validator\Rule\BoolValue;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\ValidatorInterface;

/**
 * @BeforeMethods("setUp")
 */
final class MainBench
{
    private ValidatorInterface $validator;

    public function setUp(array $params): void
    {
        $this->validator = new Validator();
    }

    /**
     * @Revs(1000)
     * @Iterations(10)
     * @ParamProviders("provider")
     * @Warmup(1)
     */
    public function benchValidate(array $params): void
    {
        $rules = $this->generateRules($params['rules'], $params['count']);
        $this->validator->validate($params['data'], $rules);
    }

    public function provider(): Generator
    {
        $data = [
            'bool' => true,
            'int' => 555,
        ];
        yield 'simple 1' => [
            'data' => $data,
            'rules' => [
                'bool' => new BoolValue(),
                'int' => new Number(asInteger: true),
            ],
            'count' => 1,
        ];
        yield 'simple 10' => [
            'data' => $data,
            'rules' => [
                'bool' => new BoolValue(),
                'int' => new Number(asInteger: true),
            ],
            'count' => 10,
        ];
        yield 'simple 100' => [
            'data' => $data,
            'rules' => [
                'bool' => new BoolValue(),
                'int' => new Number(asInteger: true),
            ],
            'count' => 100,
        ];
    }

    public function generateRules(array $rules, int $count): iterable
    {
        foreach ($rules as $attribute => $rule) {
            yield $attribute => $this->cloneRule($rule, $count);
        }
    }

    private function cloneRule(mixed $rule, int $count): Generator
    {
        for ($i = 0; $i < $count; $i++) {
            yield clone $rule;
        }
    }
}
