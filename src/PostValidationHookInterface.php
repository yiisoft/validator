<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * An optional interface for data set ({@see DataSetInterface}) to implement. It provides
 * {@see processValidationResult()} method-based hook allowing to execute custom code after a validation {@see Result}
 * has been formed.
 */
interface PostValidationHookInterface extends DataSetInterface
{
    /**
     * Method-based hook allowing to execute custom code after a validation {@see Result} has been formed.
     *
     * @param Result $result A validation {@see Result} instance.
     *
     * @see https://github.com/yiisoft/form/blob/4b385ff44ee1a5f402471e7a1e6aafff31a391fb/src/FormModel.php#L202 for
     * usage in `FormModel`.
     */
    public function processValidationResult(Result $result): void;
}
