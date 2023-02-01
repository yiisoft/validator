<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use ReturnTypeWillChange;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\RuleInterface;

class IteratorWithBooleanKey implements \Iterator
{
    private int $position = 0;
    private array $array;

    public function __construct()
    {
        $this->array = [new Length(min: 1), new Length(min: 1)];
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    #[ReturnTypeWillChange]
    public function current(): RuleInterface
    {
        return $this->array[$this->position];
    }

    #[ReturnTypeWillChange]
    public function key(): bool
    {
        return (bool) $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return isset($this->array[$this->position]);
    }
}
