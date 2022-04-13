<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\HasLength;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\ValidationContext;
use function is_string;

/**
 * Validates that the value is of certain length.
 *
 * Note, this rule should only be used with strings.
 */
final class HasLengthValidator implements RuleValidatorInterface
{
    public function __construct(
    ) {
    }

    /**
     * @param $value
     * @param HasLength $config
     * @param ValidationContext|null $context
     *
     * @return Result
     */
    public function validate($value, object $config, ?ValidationContext $context = null): Result
    {
        $result = new Result();

        if (!is_string($value)) {
            $result->addError($config->message);
            return $result;
        }

        $length = mb_strlen($value, $config->encoding);

        if ($config->min !== null && $length < $config->min) {
            $result->addError($config->tooShortMessage, ['min' => $config->min]);
        }
        if ($config->max !== null && $length > $config->max) {
            $result->addError($config->tooLongMessage, ['max' => $config->max]);
        }

        return $result;
    }
}
