<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Debug;

use Traversable;
use Yiisoft\Validator\Result;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\CollectorTrait;
use Yiisoft\Yii\Debug\Collector\IndexCollectorInterface;

final class ValidatorCollector implements CollectorInterface, IndexCollectorInterface
{
    use CollectorTrait;

    private array $validations = [];

    public function getCollected(): array
    {
        return $this->validations;
    }

    public function collect(mixed $value, Result $result, ?iterable $rules = null): void
    {
        if (!$this->isActive()) {
            return;
        }

        $this->validations[] = [
            'value' => $value,
            'rules' => $rules instanceof Traversable ? iterator_to_array($rules, true) : (array) $rules,
            'result' => $result->isValid(),
            'errors' => $result->getErrors(),
        ];
    }

    private function reset(): void
    {
        $this->validations = [];
    }

    public function getIndexData(): array
    {
        $count = count($this->validations);
        $countValid = count(array_filter($this->validations, fn (array $data) => $data['result']));
        $countInvalid = $count - $countValid;

        return [
            'validator' => [
                'total' => $count,
                'valid' => $countValid,
                'invalid' => $countInvalid,
            ],
        ];
    }
}
