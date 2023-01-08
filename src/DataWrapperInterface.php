<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Data wrapper interface provides access to raw data behind a data set.
 *
 * @internal
 */
interface DataWrapperInterface extends DataSetInterface
{
    /**
     * Get raw data that is wrapped.
     *
     * @return mixed Raw data.
     */
    public function getSource(): mixed;
}
