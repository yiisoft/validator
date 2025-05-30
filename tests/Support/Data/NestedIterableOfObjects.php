<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;

final class NestedIterableOfObjects
{
    /**
     * @var iterable<ObjectForIterableCollection>
     */
    #[Each(new Nested())]
    private iterable $collection = [];

    public function setCollection(iterable $collection): self
    {
        $this->collection = $collection;
        return $this;
    }

    public function getCollection(): iterable
    {
        return $this->collection;
    }
}
