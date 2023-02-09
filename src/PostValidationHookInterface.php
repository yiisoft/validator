<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * An optional interface for validated objects to implement. It provides {@see processValidationResult()} method-based
 * hook allowing to execute custom code after a validation {@see Result} has been formed.
 */
interface PostValidationHookInterface
{
    /**
     * Method-based hook allowing to execute custom code after a validation {@see Result} has been formed.
     *
     * @param Result $result A validation {@see Result} instance.
     */
    public function processValidationResult(Result $result): void;
}
