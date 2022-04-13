<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Each;

use InvalidArgumentException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;

/**
 * Validates an array by checking each of its elements against a set of rules.
 */
final class EachValidator
{
    public static function getConfigClassName(): string
    {
        return Each::class;
    }

    public function validate(mixed $value, object $config, ?ValidationContext $context = null): Result
    {
        if ($config->ruleSet === null) {
            throw new InvalidArgumentException('Rules are required.');
        }

        $result = new Result();
        if (!is_iterable($value)) {
            $result->addError($config->incorrectInputMessage);

            return $result;
        }

        foreach ($value as $index => $item) {
            $itemResult = $config->ruleSet->validate($item, $context);
            if ($itemResult->isValid()) {
                continue;
            }

            foreach ($itemResult->getErrors() as $error) {
                if (!is_array($item)) {
                    // TODO: move back errorKey
                    $errorKey = [$index];
                    $formatMessage = true;
                } else {
                    $errorKey = [$index, ...$error->getValuePath()];
                    $formatMessage = false;
                }

                if (!$formatMessage) {
                    $result->addError($error->getMessage());
                } else {
                    $result->addError($config->message, [
                        'error' => $error->getMessage(),
                        'value' => $item,
                    ]);
                }
            }
        }

        return $result;
    }

}
