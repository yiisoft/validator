<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use InvalidArgumentException;
use RuntimeException;
use Yiisoft\NetworkUtilities\IpHelper;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Trait\FormatMessageTrait;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\Exception\UnexpectedRuleException;

use function is_string;

/**
 * Checks if the value is a valid IPv4/IPv6 address or subnet.
 *
 * It also may change the value if normalization of IPv6 expansion is enabled.
 */
final class IpHandler implements RuleHandlerInterface
{
    use FormatMessageTrait;

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

        $this->checkAllowedVersions($rule);
        $result = new Result();
        if (!is_string($value)) {
            $formattedMessage = $this->formatMessage($rule->getMessage());
            $result->addError($formattedMessage);
            return $result;
        }

        if (preg_match($rule->getIpParsePattern(), $value, $matches) === 0) {
            $formattedMessage = $this->formatMessage($rule->getMessage());
            $result->addError($formattedMessage);
            return $result;
        }
        $negation = !empty($matches['not'] ?? null);
        $ip = $matches['ip'];
        $cidr = $matches['cidr'] ?? null;
        $ipCidr = $matches['ipCidr'];

        try {
            $ipVersion = IpHelper::getIpVersion($ip, false);
        } catch (InvalidArgumentException $e) {
            $formattedMessage = $this->formatMessage($rule->getMessage());
            $result->addError($formattedMessage);
            return $result;
        }

        $result = $this->validateValueParts($rule, $result, $cidr, $negation);
        if (!$result->isValid()) {
            return $result;
        }
        $result = $this->validateVersion($rule, $result, $ipVersion);
        if (!$result->isValid()) {
            return $result;
        }
        return $this->validateCidr($rule, $result, $cidr, $ipCidr);
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

    private function checkAllowedVersions(Ip $rule): void
    {
        if (!$rule->isAllowIpv4() && !$rule->isAllowIpv6()) {
            throw new RuntimeException('Both IPv4 and IPv6 checks can not be disabled at the same time.');
        }
    }

    private function validateValueParts(Ip $rule, Result $result, ?string $cidr, bool $negation): Result
    {
        if ($cidr === null && $rule->isRequireSubnet()) {
            $formattedMessage = $this->formatMessage($rule->getNoSubnetMessage());
            $result->addError($formattedMessage);
            return $result;
        }
        if ($cidr !== null && !$rule->isAllowSubnet()) {
            $formattedMessage = $rule->getHasSubnetMessage();
            $result->addError($formattedMessage);
            return $result;
        }
        if ($negation && !$rule->isAllowNegation()) {
            $formattedMessage = $this->formatMessage($rule->getMessage());
            $result->addError($formattedMessage);
            return $result;
        }
        return $result;
    }

    private function validateVersion(Ip $rule, Result $result, int $ipVersion): Result
    {
        if ($ipVersion === IpHelper::IPV6 && !$rule->isAllowIpv6()) {
            $formattedMessage = $this->formatMessage($rule->getIpv6NotAllowedMessage());
            $result->addError($formattedMessage);
            return $result;
        }
        if ($ipVersion === IpHelper::IPV4 && !$rule->isAllowIpv4()) {
            $formattedMessage = $this->formatMessage($rule->getIpv4NotAllowedMessage());
            $result->addError($formattedMessage);
            return $result;
        }
        return $result;
    }

    private function validateCidr(Ip $rule, Result $result, $cidr, $ipCidr): Result
    {
        if ($cidr !== null) {
            try {
                IpHelper::getCidrBits($ipCidr);
            } catch (InvalidArgumentException $e) {
                $formattedMessage = $this->formatMessage($rule->getWrongCidrMessage());
                $result->addError($formattedMessage);
                return $result;
            }
        }
        if (!$rule->isAllowed($ipCidr)) {
            $formattedMessage = $this->formatMessage($rule->getNotInRangeMessage());
            $result->addError($formattedMessage);
            return $result;
        }
        return $result;
    }
}
