<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use RuntimeException;

trait IdnDependencyTrait
{
    /**
     * @var bool|null A separate property is used for easier testing.
     */
    private ?bool $idnFunctionExists = null;

    private function initIdnFunctionExists(): void
    {
        $this->idnFunctionExists = function_exists('idn_to_ascii');
    }

    /**
     * @throws RuntimeException
     */
    private function idnFunctionRequired(): void
    {
        if ($this->enableIDN && !$this->idnFunctionExists) {
            throw new RuntimeException('In order to use IDN validation intl extension must be installed and enabled.');
        }
    }
}
