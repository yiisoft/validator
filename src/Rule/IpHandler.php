<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use InvalidArgumentException;
use RuntimeException;
use Yiisoft\NetworkUtilities\IpHelper;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

use function is_string;

/**
 * Checks if the value is a valid IPv4/IPv6 address or subnet.
 *
 * It also may change the value if normalization of IPv6 expansion is enabled.
 */
final class IpHandler implements RuleHandlerInterface
{
    /**
     * Negation char.
     *
     * Used to negate {@see $ranges} or {@see $network} or to negate validating value when {@see $allowNegation}
     * is used.
     */
    private const NEGATION_CHAR = '!';

    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Ip) {
            throw new UnexpectedRuleException(Ip::class, $rule);
        }

        $result = new Result();
        if (!is_string($value)) {
            return $result->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getAttribute(),
                'type' => get_debug_type($value),
            ]);
        }

        if (preg_match($this->getIpParsePattern(), $value, $matches) === 0) {
            return $result->addError($rule->getMessage(), ['attribute' => $context->getAttribute(), 'value' => $value]);
        }

        $negation = !empty($matches['not'] ?? null);
        $ip = $matches['ip'];
        $cidr = $matches['cidr'] ?? null;
        $ipCidr = $matches['ipCidr'];
        // Exception can not be thrown here because of the check above (regular expression in "getIpParsePattern()").
        $ipVersion = IpHelper::getIpVersion($ip);

        $result = $this->validateValueParts($rule, $result, $cidr, $negation, $value, $context);
        if (!$result->isValid()) {
            return $result;
        }

        $result = $this->validateVersion($rule, $result, $ipVersion, $value, $context);
        if (!$result->isValid()) {
            return $result;
        }

        return $this->validateCidr($rule, $result, $cidr, $ipCidr, $value, $context);
    }

    /**
     * Used to get the Regexp pattern for initial IP address parsing.
     */
    private function getIpParsePattern(): string
    {
        return '/^(?<not>' . preg_quote(
            self::NEGATION_CHAR,
            '/'
        ) . ')?(?<ipCidr>(?<ip>(?:' . IpHelper::IPV4_PATTERN . ')|(?:' . IpHelper::IPV6_PATTERN . '))(?:\/(?<cidr>-?\d+))?)$/';
    }

    private function validateValueParts(
        Ip $rule,
        Result $result,
        ?string $cidr,
        bool $negation,
        string $value,
        ValidationContext $context
    ): Result {
        if ($cidr === null && $rule->isRequireSubnet()) {
            $result->addError(
                $rule->getNoSubnetMessage(),
                [
                    'attribute' => $context->getAttribute(),
                    'value' => $value,
                ],
            );
            return $result;
        }
        if ($cidr !== null && !$rule->isAllowSubnet()) {
            $result->addError(
                $rule->getHasSubnetMessage(),
                [
                    'attribute' => $context->getAttribute(),
                    'value' => $value,
                ],
            );
            return $result;
        }
        if ($negation && !$rule->isAllowNegation()) {
            $result->addError(
                $rule->getMessage(),
                [
                    'attribute' => $context->getAttribute(),
                    'value' => $value,
                ],
            );
            return $result;
        }
        return $result;
    }

    private function validateVersion(
        Ip $rule,
        Result $result,
        int $ipVersion,
        string $value,
        ValidationContext $context
    ): Result {
        if ($ipVersion === IpHelper::IPV6 && !$rule->isAllowIpv6()) {
            $result->addError(
                $rule->getIpv6NotAllowedMessage(),
                [
                    'attribute' => $context->getAttribute(),
                    'value' => $value,
                ],
            );
            return $result;
        }
        if ($ipVersion === IpHelper::IPV4 && !$rule->isAllowIpv4()) {
            $result->addError(
                $rule->getIpv4NotAllowedMessage(),
                [
                    'attribute' => $context->getAttribute(),
                    'value' => $value,
                ],
            );
            return $result;
        }
        return $result;
    }

    private function validateCidr(
        Ip $rule,
        Result $result,
        ?string $cidr,
        string $ipCidr,
        string $value,
        ValidationContext $context
    ): Result {
        if ($cidr !== null) {
            try {
                IpHelper::getCidrBits($ipCidr);
            } catch (InvalidArgumentException) {
                $result->addError(
                    $rule->getWrongCidrMessage(),
                    [
                        'attribute' => $context->getAttribute(),
                        'value' => $value,
                    ],
                );
                return $result;
            }
        }
        if (!$rule->isAllowed($ipCidr)) {
            $result->addError(
                $rule->getNotInRangeMessage(),
                [
                    'attribute' => $context->getAttribute(),
                    'value' => $value,
                ],
            );
            return $result;
        }
        return $result;
    }
}
