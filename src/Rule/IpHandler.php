<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use InvalidArgumentException;
use Yiisoft\NetworkUtilities\IpHelper;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

use function is_string;

/**
 * Checks if the value is a valid IPv4/IPv6 address or subnet.
 *
 * @see Ip
 */
final class IpHandler implements RuleHandlerInterface
{
    /**
     * Negation character.
     *
     * Used to negate {@see $ranges} or {@see $network} or to negate value validated when {@see $allowNegation}
     * is used.
     */
    private const NEGATION_CHARACTER = '!';

    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Ip) {
            throw new UnexpectedRuleException(Ip::class, $rule);
        }

        if (!is_string($value)) {
            return (new Result())->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'Attribute' => $context->getCapitalizedTranslatedAttribute(),
                'type' => get_debug_type($value),
            ]);
        }

        if (preg_match(self::getIpParsePattern(), $value, $matches) === 0) {
            return self::getGenericErrorResult($rule->getMessage(), $context, $value);
        }

        $negation = !empty($matches['not'] ?? null);
        $ip = $matches['ip'];
        $cidr = $matches['cidr'] ?? null;
        $ipCidr = $matches['ipCidr'];
        /**
         * Exception handling and validation in IpHelper are not needed because of the check above (regular expression
         * in "getIpParsePattern()").
         *
         * @infection-ignore-all
         */
        $ipVersion = IpHelper::getIpVersion($ip, validate: false);

        $result = self::validateValueParts($rule, $cidr, $negation, $value, $context);
        if ($result !== null) {
            return $result;
        }

        $result = self::validateVersion($rule, $ipVersion, $value, $context);
        if ($result !== null) {
            return $result;
        }

        $result = self::validateCidr($rule, $cidr, $ipCidr, $value, $context);
        return $result ?? new Result();
    }

    /**
     * Used to get the Regexp pattern for initial IP address parsing.
     *
     * @return string Regular expression pattern.
     * @psalm-return non-empty-string
     */
    private static function getIpParsePattern(): string
    {
        return '/^(?<not>' .
            self::NEGATION_CHARACTER .
            ')?(?<ipCidr>(?<ip>(?:' . IpHelper::IPV4_PATTERN . ')|(?:' . IpHelper::IPV6_PATTERN . '))(?:\/(?<cidr>-?\d+))?)$/';
    }

    /**
     * Validates value parts.
     *
     * @param Ip $rule Instance of the rule.
     * @param string|null $cidr CIDR for subnet check.
     * @param bool $negation If negation is used.
     * @param string $value Value validated.
     * @param ValidationContext $context Validation context.
     *
     * @return Result|null Validation result.
     */
    private static function validateValueParts(
        Ip $rule,
        ?string $cidr,
        bool $negation,
        string $value,
        ValidationContext $context
    ): Result|null {
        if ($cidr === null && $rule->isSubnetRequired()) {
            return self::getGenericErrorResult($rule->getNoSubnetMessage(), $context, $value);
        }

        if ($cidr !== null && !$rule->isSubnetAllowed()) {
            return self::getGenericErrorResult($rule->getHasSubnetMessage(), $context, $value);
        }

        if ($negation && !$rule->isNegationAllowed()) {
            return self::getGenericErrorResult($rule->getMessage(), $context, $value);
        }

        return null;
    }

    /**
     * Validate that IP protocol version is within enabled ones.
     *
     * @param Ip $rule Instance of the rule.
     * @param int $ipVersion Version of the IP protocol.
     * @param string $value Value validated.
     * @param ValidationContext $context Validation context.
     *
     * @return Result|null Validation result.
     */
    private static function validateVersion(
        Ip $rule,
        int $ipVersion,
        string $value,
        ValidationContext $context
    ): Result|null {
        if ($ipVersion === IpHelper::IPV6 && !$rule->isIpv6Allowed()) {
            return self::getGenericErrorResult($rule->getIpv6NotAllowedMessage(), $context, $value);
        }

        if ($ipVersion === IpHelper::IPV4 && !$rule->isIpv4Allowed()) {
            return self::getGenericErrorResult($rule->getIpv4NotAllowedMessage(), $context, $value);
        }

        return null;
    }

    /**
     * Validate that CIDR is valid and the value is in range.
     *
     * @param Ip $rule Instance of the rule.
     * @param string|null $cidr CIDR for subnet check.
     * @param string $ipCidr IP CIDR.
     * @param string $value Value validated.
     * @param ValidationContext $context Validation context.
     *
     * @return Result|null Validation result.
     */
    private static function validateCidr(
        Ip $rule,
        ?string $cidr,
        string $ipCidr,
        string $value,
        ValidationContext $context
    ): Result|null {
        if ($cidr !== null) {
            try {
                IpHelper::getCidrBits($ipCidr);
            } catch (InvalidArgumentException) {
                return self::getGenericErrorResult($rule->getWrongCidrMessage(), $context, $value);
            }
        }

        if (!$rule->isAllowed($ipCidr)) {
            return self::getGenericErrorResult($rule->getNotInRangeMessage(), $context, $value);
        }

        return null;
    }

    /**
     * Creates a new result with an error.
     *
     * @param string $message Error message.
     * @param ValidationContext $context  Validation context.
     * @param string $value Value validated.
     *
     * @return Result Validation result.
     */
    private static function getGenericErrorResult(string $message, ValidationContext $context, string $value): Result
    {
        return (new Result())->addError($message, [
            'attribute' => $context->getTranslatedAttribute(),
            'Attribute' => $context->getCapitalizedTranslatedAttribute(),
            'value' => $value,
        ]);
    }
}
