<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use InvalidArgumentException;
use RuntimeException;
use Yiisoft\NetworkUtilities\IpHelper;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;
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

    public function validate(mixed $value, object $rule, ValidatorInterface $validator, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Ip) {
            throw new UnexpectedRuleException(Ip::class, $rule);
        }

        if (!$rule->allowIpv4 && !$rule->allowIpv6) {
            throw new RuntimeException('Both IPv4 and IPv6 checks can not be disabled at the same time.');
        }
        $result = new Result();
        if (!is_string($value)) {
            $result->addError($rule->message);
            return $result;
        }

        if (preg_match($rule->getIpParsePattern(), $value, $matches) === 0) {
            $result->addError($rule->message);
            return $result;
        }
        $negation = !empty($matches['not'] ?? null);
        $ip = $matches['ip'];
        $cidr = $matches['cidr'] ?? null;
        $ipCidr = $matches['ipCidr'];

        try {
            $ipVersion = IpHelper::getIpVersion($ip, false);
        } catch (InvalidArgumentException $e) {
            $result->addError($rule->message);
            return $result;
        }

        if ($rule->requireSubnet === true && $cidr === null) {
            $result->addError($rule->noSubnetMessage);
            return $result;
        }
        if ($rule->allowSubnet === false && $cidr !== null) {
            $result->addError($rule->hasSubnetMessage);
            return $result;
        }
        if ($rule->allowNegation === false && $negation) {
            $result->addError($rule->message);
            return $result;
        }
        if ($ipVersion === IpHelper::IPV6 && !$rule->allowIpv6) {
            $result->addError($rule->ipv6NotAllowedMessage);
            return $result;
        }
        if ($ipVersion === IpHelper::IPV4 && !$rule->allowIpv4) {
            $result->addError($rule->ipv4NotAllowedMessage);
            return $result;
        }
        if (!$result->isValid()) {
            return $result;
        }
        if ($cidr !== null) {
            try {
                IpHelper::getCidrBits($ipCidr);
            } catch (InvalidArgumentException $e) {
                $result->addError($rule->wrongCidrMessage);
                return $result;
            }
        }
        if (!$rule->isAllowed($ipCidr)) {
            $result->addError($rule->notInRangeMessage);
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
