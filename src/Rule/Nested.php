<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use InvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;
use Traversable;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\BeforeValidationInterface;
use Yiisoft\Validator\Rule\Trait\BeforeValidationTrait;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\RulesDumper;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\ValidationContext;

use function array_pop;
use function count;
use function implode;
use function is_array;
use function ltrim;
use function rtrim;
use function sprintf;

/**
 * Can be used for validation of nested structures.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Nested implements SerializableRuleInterface, BeforeValidationInterface
{
    use BeforeValidationTrait;
    use RuleNameTrait;

    private const SEPARATOR = '.';
    private const EACH_SHORTCUT = '*';

    public function __construct(
        /**
         * @var iterable<\Closure|\Closure[]|RuleInterface|RuleInterface[]>
         */
        private iterable $rules = [],
        private bool $requirePropertyPath = false,
        private string $noPropertyPathMessage = 'Property path "{path}" is not found.',
        private bool $normalizeRules = true,
        private bool $skipOnEmpty = false,
        private bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null
         */
        private ?Closure $when = null,
    ) {
        $rules = $rules instanceof Traversable ? iterator_to_array($rules) : $rules;
        if (empty($rules)) {
            throw new InvalidArgumentException('Rules must not be empty.');
        }

        if (self::checkRules($rules)) {
            $message = sprintf('Each rule should be an instance of %s.', RuleInterface::class);
            throw new InvalidArgumentException($message);
        }

        $this->rules = $rules;

        if ($this->normalizeRules === true) {
            $this->normalizeRules();
        }
    }

    /**
     * @return iterable<\Closure|\Closure[]|RuleInterface|RuleInterface[]>
     */
    public function getRules(): iterable
    {
        return $this->rules;
    }

    /**
     * @return bool
     */
    public function getRequirePropertyPath(): bool
    {
        return $this->requirePropertyPath;
    }

    /**
     * @return string
     */
    public function getNoPropertyPathMessage(): string
    {
        return $this->noPropertyPathMessage;
    }

    private static function checkRules($rules): bool
    {
        return array_reduce(
            $rules,
            function (bool $carry, $rule) {
                return $carry || (is_array($rule) ? self::checkRules($rule) : !$rule instanceof RuleInterface);
            },
            false
        );
    }

    private function normalizeRules(): void
    {
        /** @var iterable $rules */
        $rules = $this->getRules();
        while (true) {
            $breakWhile = true;
            $rulesMap = [];

            foreach ($rules as $valuePath => $rule) {
                if ($valuePath === self::EACH_SHORTCUT) {
                    throw new InvalidArgumentException('Bare shortcut is prohibited. Use "Each" rule instead.');
                }

                $parts = ArrayHelper::parsePath((string) $valuePath, preserveDelimiterEscaping: true);
                if (count($parts) === 1) {
                    continue;
                }

                $breakWhile = false;

                $lastValuePath = array_pop($parts);
                $lastValuePath = ltrim($lastValuePath, '.');
                $lastValuePath = str_replace('\\' . self::EACH_SHORTCUT, self::EACH_SHORTCUT, $lastValuePath);

                $remainingValuePath = implode(self::EACH_SHORTCUT, $parts);
                $remainingValuePath = rtrim($remainingValuePath, self::SEPARATOR);

                if (!isset($rulesMap[$remainingValuePath])) {
                    $rulesMap[$remainingValuePath] = [];
                }

                $rulesMap[$remainingValuePath][$lastValuePath] = $rule;
                unset($rules[$valuePath]);
            }

            foreach ($rulesMap as $valuePath => $nestedRules) {
                $rules[$valuePath] = new Each([new self($nestedRules, normalizeRules: false)]);
            }

            if ($breakWhile === true) {
                break;
            }
        }

        $this->rules = $rules;
    }

    #[ArrayShape([
        'requirePropertyPath' => 'bool',
        'noPropertyPathMessage' => 'array',
        'skipOnEmpty' => 'bool',
        'skipOnError' => 'bool',
        'rules' => 'array',
    ])]
    public function getOptions(): array
    {
        return [
            'requirePropertyPath' => $this->getRequirePropertyPath(),
            'noPropertyPathMessage' => [
                'message' => $this->getNoPropertyPathMessage(),
            ],
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
            'rules' => (new RulesDumper())->asArray($this->rules),
        ];
    }

    public function getHandlerClassName(): string
    {
        return NestedHandler::class;
    }
}
