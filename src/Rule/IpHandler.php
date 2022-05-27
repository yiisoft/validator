<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use InvalidArgumentException;
use RuntimeException;
use Yiisoft\NetworkUtilities\IpHelper;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;
use function is_string;
use Yiisoft\Validator\Exception\UnexpectedRuleException;

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

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Ip) {
            throw new UnexpectedRuleException(Ip::class, $rule);
        }

        if (!$rule->isAllowIpv4() && !$rule->isAllowIpv6()) {
            throw new RuntimeException('Both IPv4 and IPv6 checks can not be disabled at the same time.');
        }
        $result = new Result();
        if (!is_string($value)) {
            $result->addError($rule->getMessage());
            return $result;
        }

        if (preg_match($rule->getIpParsePattern(), $value, $matches) === 0) {
            $result->addError($rule->getMessage());
            return $result;
        }
        $negation = !empty($matches['not'] ?? null);
        $ip = $matches['ip'];
        $cidr = $matches['cidr'] ?? null;
        $ipCidr = $matches['ipCidr'];

        try {
            $ipVersion = IpHelper::getIpVersion($ip, false);
        } catch (InvalidArgumentException $e) {
            $result->addError($rule->getMessage());
            return $result;
        }

        if ($rule->isRequireSubnet() && $cidr === null) {
            $result->addError($rule->getNoSubnetMessage());
            return $result;
        }
        if (!$rule->isAllowSubnet() && $cidr !== null) {
            $result->addError($rule->getHasSubnetMessage());
            return $result;
        }
        if (!$rule->isAllowNegation() && $negation) {
            $result->addError($rule->getMessage());
            return $result;
        }
        if ($ipVersion === IpHelper::IPV6 && !$rule->isAllowIpv6()) {
            $result->addError($rule->getIpv6NotAllowedMessage());
            return $result;
        }
        if ($ipVersion === IpHelper::IPV4 && !$rule->isAllowIpv4()) {
            $result->addError($rule->getIpv4NotAllowedMessage());
            return $result;
        }
        if (!$result->isValid()) {
            return $result;
        }
        if ($cidr !== null) {
            try {
                IpHelper::getCidrBits($ipCidr);
            } catch (InvalidArgumentException $e) {
                $result->addError($rule->getWrongCidrMessage());
                return $result;
            }
        }
        if (!$rule->isAllowed($ipCidr)) {
            $result->addError($rule->getNotInRangeMessage());
            return $result;
        }

        return $result;
    }

    /**
     * Used to get the Regexp pattern for initial IP address parsing.
     */
    public function getIpParsePattern(): string
    {
        return '/^(?<not>' . preg_quote(
            self::NEGATION_CHAR,
            '/'
        ) . ')?(?<ipCidr>(?<ip>(?:' . IpHelper::IPV4_PATTERN . ')|(?:' . IpHelper::IPV6_PATTERN . '))(?:\/(?<cidr>-?\d+))?)$/';
    }
}
