<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

final class RulesDumper
{
    private ?FormatterInterface $formatter;

    public function __construct(?FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * Return all attribute rules as array.
     *
     * For example:
     *
     * ```php
     * [
     *    'amount' => [
     *        [
     *            'number',
     *            'integer' => true,
     *            'max' => 100,
     *            'notANumberMessage' => 'Value must be an integer.',
     *            'tooBigMessage' => 'Value must be no greater than 100.'
     *        ],
     *        ['callback'],
     *    ],
     *    'name' => [
     *        [
     *            'hasLength',
     *            'max' => 20,
     *            'message' => 'Value must contain at most 20 characters.'
     *        ],
     *    ],
     * ]
     * ```
     *
     * @param iterable $rules
     *
     * @return array
     */
    public function asArray(iterable $rules): array
    {
        $rulesOfArray = [];
        foreach ($rules as $attribute => $rulesSet) {
            if (is_array($rulesSet)) {
                $rulesSet = new Rules($rulesSet);
            }
            if (!$rulesSet instanceof Rules) {
                throw new \InvalidArgumentException(sprintf(
                    'Value should be instance of %s or an array of rules, %s given.',
                    Rules::class,
                    is_object($rulesSet) ? get_class($rulesSet) : gettype($rulesSet)
                ));
            }
            $rulesOfArray[$attribute] = $rulesSet->withFormatter($this->formatter)->asArray();
        }
        return $rulesOfArray;
    }

    public function withFormatter(?FormatterInterface $formatter): self
    {
        $new = clone $this;
        $new->formatter = $formatter;
        return $new;
    }
}
