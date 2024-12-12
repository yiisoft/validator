<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

class UuidHandler implements RuleHandlerInterface {
    private const PATTERN = '\A[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}\z';
    private const NIL = '00000000-0000-0000-0000-000000000000';

    /**
     * @param mixed $value
     * @param RuleInterface $rule
     * @param ValidationContext $context
     * @return Result
     */
    public function validate(mixed $value, RuleInterface $rule, ValidationContext $context): Result {
        if (!$rule instanceof Uuid) {
            throw new UnexpectedRuleException(Uuid::class, $rule);
        }

        $result = new Result();

        if ($this->validateUuid($value)) {
            return $result;
        }

        return $result->addError($rule->getMessage(), [
            'property' => $context->getTranslatedProperty(),
            'Property' => $context->getCapitalizedTranslatedProperty(),
        ]);
    }

    /**
     * @param string $uuid
     * @return bool
     */
    protected function validateUuid(string $uuid): bool {
        $uuid = str_replace(['urn:', 'uuid:', 'URN:', 'UUID:', '{', '}'], '', $uuid);

        return $uuid === self::NIL || preg_match('/' . self::PATTERN . '/Dms', $uuid);
    }
}
