<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

interface ObjectDataSetInterface extends DataSetInterface
{
    public function getPropertyVisibility(): int;

    public function getObject(): object;
}
