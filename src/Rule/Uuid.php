<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Yiisoft\Validator\DumpedRuleInterface;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class Uuid implements DumpedRuleInterface, SkipOnEmptyInterface, SkipOnErrorInterface, WhenInterface {
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @param bool $replaceChars
     * @param string $message
     * @param string $notPassedMessage
     * @param bool $skipOnError
     * @param Closure|null $when
     */
    public function __construct(
        private bool $replaceChars = false,
        private string       $message = 'The value of {property} does not conform to the UUID format.',
        private string       $notPassedMessage = '{Property} not passed.',
        private bool         $skipOnError = false,
        private Closure|null $when = null,
    ) {
    }

    /**
     * @return bool
     */
    public function getReplaceChars(): bool {
        return $this->replaceChars;
    }

    /**
     * Gets error message used when validation fails because the validated value is empty.
     *
     * @return string Error message / template.
     *
     * @see $message
     */
    public function getMessage(): string {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return self::class;
    }

    /**
     * @return array
     */
    public function getOptions(): array {
        return [];
    }

    /**
     * @return string
     */
    public function getHandler(): string {
        return UuidHandler::class;
    }
}
