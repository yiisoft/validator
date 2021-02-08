<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Allows to make validation post-processing.
 *
 * @example {@see \Yiisoft\Form\FormModel::processValidationResult}
 */
interface PostValidationHookInterface extends DataSetInterface
{
    public function processValidationResult(ResultSet $resultSet): void;
}
