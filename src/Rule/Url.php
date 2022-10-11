<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use RuntimeException;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\WhenInterface;

use function function_exists;

/**
 * Validates that the value is a valid HTTP or HTTPS URL.
 *
 * Note that this rule only checks if the URL scheme and host part are correct.
 * It does not check the remaining parts of a URL.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Url implements SerializableRuleInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    public function __construct(
        /**
         * @var string the regular expression used to validate the value.
         * The pattern may contain a `{schemes}` token that will be replaced
         * by a regular expression which represents the {@see $schemes}.
         *
         * Note that if you want to reuse the pattern in HTML5 input it should have ^ and $, should not have any
         * modifiers and should not be case-insensitive.
         */
        private string $pattern = '/^{schemes}:\/\/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)(?::\d{1,5})?([?\/#].*$|$)/',
        /**
         * @var array list of URI schemes which should be considered valid. By default, http and https
         * are considered to be valid schemes.
         */
        private array $validSchemes = ['http', 'https'],
        /**
         * @var bool whether validation process should take into account IDN (internationalized
         * domain names). Defaults to false meaning that validation of URLs containing IDN will always
         * fail. Note that in order to use IDN validation you have to install and enable `intl` PHP
         * extension, otherwise an exception would be thrown.
         */
        private bool $enableIDN = false,
        private string $message = 'This value is not a valid URL.',

        /**
         * @var bool|callable|null
         */
        private $skipOnEmpty = null,
        private bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null
         */
        private ?Closure $when = null,
    ) {
        if ($enableIDN && !function_exists('idn_to_ascii')) {
            throw new RuntimeException('In order to use IDN validation intl extension must be installed and enabled.');
        }
    }

    public function getName(): string
    {
        return 'url';
    }

    public function getPattern(): string
    {
        if (str_contains($this->pattern, '{schemes}')) {
            return str_replace('{schemes}', '((?i)' . implode('|', $this->validSchemes) . ')', $this->pattern);
        }

        return $this->pattern;
    }

    /**
     * @return array|string[]
     */
    public function getValidSchemes(): array
    {
        return $this->validSchemes;
    }

    public function isEnableIDN(): bool
    {
        return $this->enableIDN;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getOptions(): array
    {
        return [
            'pattern' => $this->getPattern(),
            'validSchemes' => $this->validSchemes,
            'enableIDN' => $this->enableIDN,
            'message' => [
                'message' => $this->message,
            ],
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandlerClassName(): string
    {
        return UrlHandler::class;
    }
}
