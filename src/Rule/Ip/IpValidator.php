<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Ip;

use InvalidArgumentException;
use RuntimeException;
use Yiisoft\NetworkUtilities\IpHelper;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;
use function is_string;
use function strlen;

/**
 * Checks if the value is a valid IPv4/IPv6 address or subnet.
 *
 * It also may change the value if normalization of IPv6 expansion is enabled.
 */
final class IpValidator
{
    public static function getConfigClassName(): string
    {
        return Ip::class;
    }

    /**
     * Negation char.
     *
     * Used to negate {@see $ranges} or {@see $network} or to negate validating value when {@see $allowNegation}
     * is used.
     */
    private const NEGATION_CHAR = '!';

    public function validate(mixed $value, object $config, ?ValidationContext $context = null): Result
    {
        if (!$config->allowIpv4 && !$config->allowIpv6) {
            throw new RuntimeException('Both IPv4 and IPv6 checks can not be disabled at the same time.');
        }
        $result = new Result();
        if (!is_string($value)) {
            $result->addError($config->message);
            return $result;
        }

        if (preg_match($config->getIpParsePattern(), $value, $matches) === 0) {
            $result->addError($config->message);
            return $result;
        }
        $negation = !empty($matches['not'] ?? null);
        $ip = $matches['ip'];
        $cidr = $matches['cidr'] ?? null;
        $ipCidr = $matches['ipCidr'];

        try {
            $ipVersion = IpHelper::getIpVersion($ip, false);
        } catch (InvalidArgumentException $e) {
            $result->addError($config->message);
            return $result;
        }

        if ($config->requireSubnet === true && $cidr === null) {
            $result->addError($config->noSubnetMessage);
            return $result;
        }
        if ($config->allowSubnet === false && $cidr !== null) {
            $result->addError($config->hasSubnetMessage);
            return $result;
        }
        if ($config->allowNegation === false && $negation) {
            $result->addError($config->message);
            return $result;
        }
        if ($ipVersion === IpHelper::IPV6 && !$config->allowIpv6) {
            $result->addError($config->ipv6NotAllowedMessage);
            return $result;
        }
        if ($ipVersion === IpHelper::IPV4 && !$config->allowIpv4) {
            $result->addError($config->ipv4NotAllowedMessage);
            return $result;
        }
        if (!$result->isValid()) {
            return $result;
        }
        if ($cidr !== null) {
            try {
                IpHelper::getCidrBits($ipCidr);
            } catch (InvalidArgumentException $e) {
                $result->addError($config->wrongCidrMessage);
                return $result;
            }
        }
        if (!$this->isAllowed($ipCidr)) {
            $result->addError($config->notInRangeMessage);
            return $result;
        }

        return $result;
    }

    /**
     * The method checks whether the IP address with specified CIDR is allowed according to the {@see $ranges} list.
     */
    private function isAllowed(string $ip): bool
    {
        if (empty($this->ranges)) {
            return true;
        }

        foreach ($this->ranges as $string) {
            [$isNegated, $range] = $this->parseNegatedRange($string);
            if (IpHelper::inRange($ip, $range)) {
                return !$isNegated;
            }
        }

        return false;
    }

    /**
     * Parses IP address/range for the negation with {@see NEGATION_CHAR}.
     *
     * @param $string
     *
     * @return array `[0 => bool, 1 => string]`
     *  - boolean: whether the string is negated
     *  - string: the string without negation (when the negation were present)
     */
    private function parseNegatedRange($string): array
    {
        $isNegated = strpos($string, self::NEGATION_CHAR) === 0;
        return [$isNegated, $isNegated ? substr($string, strlen(self::NEGATION_CHAR)) : $string];
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
