<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Allows implementing post-validation processing in the data set object itself.
 *
 * @link https://github.com/yiisoft/form/blob/d2333f9a0a77f6dcb00db1ab8ee95ec4426ea133/src/FormModel.php#L202
 */
interface PostValidationHookInterface extends DataSetInterface
{
    public function processValidationResult(Result $result): void;
}
