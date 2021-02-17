<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Allows to implement post-validation processing in the data set object itself.
 *
 * @see \Yiisoft\Form\FormModel::processValidationResult
 */
interface PostValidationHookInterface extends DataSetInterface
{
    public function processValidationResult(ResultSet $resultSet): void;
}
