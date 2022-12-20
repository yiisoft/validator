<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * @internal
 */
interface DataWrapperInterface extends DataSetInterface
{
    public function getSource(): mixed;
}
