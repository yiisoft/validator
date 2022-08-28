<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use InvalidArgumentException;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Strings\StringHelper;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Formatter;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

use function is_array;
use function is_int;
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
    private FormatterInterface $formatter;

    public function __construct(?FormatterInterface $formatter = null)
    {
        $this->formatter = $formatter ?? new Formatter();
    }

    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Nested) {
            throw new UnexpectedRuleException(Nested::class, $rule);
        }

        if ($rule->getRules() === null) {
            if (!is_object($value)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Nested rule without rules could be used for objects only. %s given.',
                        get_debug_type($value)
                    )
                );
            }

            $dataSet = new ObjectDataSet($value, $rule->getPropertyVisibility());

            return $context->getValidator()->validate($dataSet);
        }

        if (is_array($value)) {
            $data = $value;
        } elseif (is_object($value)) {
            $data = (new ObjectDataSet($value, $rule->getPropertyVisibility()))->getData();
        } else {
            $message = sprintf(
                'Value should be an array or an object. %s given.',
                get_debug_type($value)
            );
            $formattedMessage = $this->formatter->format(
                $message,
                ['attribute' => $context->getAttribute(), 'value' => $value]
            );
            return (new Result())->addError($formattedMessage);
        }

        $compoundResult = new Result();
        $results = [];
        foreach ($rule->getRules() as $valuePath => $rules) {
            if ($rule->getRequirePropertyPath() && !ArrayHelper::pathExists($data, $valuePath)) {
                $formattedMessage = $this->formatter->format(
                    $rule->getNoPropertyPathMessage(),
                    ['path' => $valuePath, 'attribute' => $context->getAttribute(), 'value' => $data]
                );
                /**
                 * @psalm-suppress InvalidScalarArgument
                 */
                $compoundResult->addError($formattedMessage, StringHelper::parsePath($valuePath));

                continue;
            }

            $validatedValue = ArrayHelper::getValueByPath($data, $valuePath);
            $rules = is_array($rules) ? $rules : [$rules];

            $itemResult = $context->getValidator()->validate($validatedValue, $rules);

            if ($itemResult->isValid()) {
                continue;
            }

            $result = new Result();

            foreach ($itemResult->getErrors() as $error) {
                $errorValuePath = is_int($valuePath) ? [$valuePath] : StringHelper::parsePath($valuePath);
                if (!empty($error->getValuePath())) {
                    array_push($errorValuePath, ...$error->getValuePath());
                }
                /**
                 * @psalm-suppress InvalidScalarArgument
                 */
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
