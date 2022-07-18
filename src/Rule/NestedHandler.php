<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;

use function is_array;
use function is_object;

/**
 * Can be used for validation of nested structures.
 *
 * For example, we have an inbound request with the following structure:
 *
 * ```php
 * $request = [
 *     'author' => [
 *         'name' => 'Dmitry',
 *         'age' => 18,
 *     ],
 * ];
 * ```
 *
 * So to make validation we can configure it like this:
 *
 * ```php
 * $rule = new Nested([
 *     'author' => new Nested([
 *         'name' => [new HasLength(min: 3)],
 *         'age' => [new Number(min: 18)],
 *     )];
 * ]);
 * ```
 */
final class NestedHandler implements RuleHandlerInterface
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Nested) {
            throw new UnexpectedRuleException(Nested::class, $rule);
        }

        $compoundResult = new Result();
        if (!is_object($value) && !is_array($value)) {
            $message = sprintf('Value should be an array or an object. %s given.', gettype($value));
            $formattedMessage = $this->translator->translate(
                $message,
                ['attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $compoundResult->addError($formattedMessage);

            return $compoundResult;
        }

        $value = (array)$value;

        $results = [];
        foreach ($rule->getRules() as $valuePath => $rules) {
            $result = new Result();

            if ($rule->isErrorWhenPropertyPathIsNotFound() && !ArrayHelper::pathExists($value, $valuePath)) {
                $formattedMessage = $this->translator->translate(
                    $rule->getPropertyPathIsNotFoundMessage(),
                    ['path' => $valuePath, 'attribute' => $context?->getAttribute(), 'value' => $value]
                );
                $compoundResult->addError($formattedMessage, [$valuePath]);

                continue;
            }

            $rules = is_array($rules) ? $rules : [$rules];
            $validatedValue = ArrayHelper::getValueByPath($value, $valuePath);

            $itemResult = $context?->getValidator()->validate($validatedValue, $rules);

            if ($itemResult->isValid()) {
                continue;
            }

            foreach ($itemResult->getErrors() as $error) {
                $errorValuePath = is_int($valuePath) ? [$valuePath] : explode('.', $valuePath);
                if (!empty($error->getValuePath())) {
                    array_push($errorValuePath, ...$error->getValuePath());
                }
                $result->addError($error->getMessage(), $errorValuePath);
            }
            $results[] = $result;
        }

        foreach ($results as $result) {
            foreach ($result->getErrors() as $error) {
                $compoundResult->addError($error->getMessage(), $error->getValuePath());
            }
        }

        return $compoundResult;
    }
}
