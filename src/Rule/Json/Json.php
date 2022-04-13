<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Json;

use Attribute;
use Closure;
use Yiisoft\Validator\ParametrizedRuleInterface;
use Yiisoft\Validator\Rule\RuleNameTrait;

/**
 * Validates that the value is a valid json.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Json implements ParametrizedRuleInterface
{
    use RuleNameTrait;

    public function __construct(
        public string   $message = 'The value is not JSON.',
        public bool     $skipOnEmpty = false,
        public bool     $skipOnError = false,
        public ?Closure $when = null,
    ) {
    }

    public function getOptions(): array
    {
        return [
            'message' => [
                'message' => $this->message,
            ],
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
        ];
    }
}
