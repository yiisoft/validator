<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Benchmark;

use Generator;
use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\ParamProviders;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use PhpBench\Benchmark\Metadata\Annotations\Warmup;
use Yiisoft\Validator\Rule\Boolean;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Tests\Stub\FakeValidatorFactory;
use Yiisoft\Validator\ValidatorInterface;

/**
 * @BeforeMethods("setUp")
 */
final class MainBench
{
    private ValidatorInterface $validator;

    public function setUp(array $params): void
    {
        $this->validator = FakeValidatorFactory::make();
    }

    /**
     * @Revs(1000)
     * @Iterations(10)
     * @ParamProviders("provider")
     * @Warmup(1)
     */
    public function benchValidate(array $params): void
    {
        $this->validator->validate($params['data'], $params['rules']);
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
                'bool' => $this->generateRules(new Boolean(), 1),
                'int' => $this->generateRules(new Number(asInteger: true), 1),
            ],
        ];
        yield 'simple 10' => [
            'data' => $data,
            'rules' => [
                'bool' => $this->generateRules(new Boolean(), 10),
                'int' => $this->generateRules(new Number(asInteger: true), 10),
            ],
        ];
        yield 'simple 100' => [
            'data' => $data,
            'rules' => [
                'bool' => $this->generateRules(new Boolean(), 100),
                'int' => $this->generateRules(new Number(asInteger: true), 100),
            ],
        ];
    }

    private function generateRules(object $rule, int $count): array
    {
        $rules = [];
        for ($i = 0; $i < $count; $i++) {
            $rules[] = clone $rule;
        }
        return  $rules;
    }
}
