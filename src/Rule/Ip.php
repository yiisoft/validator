<?php

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\IpHelper;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;

/**
 * The validator checks if the attribute value is a valid IPv4/IPv6 address or subnet.
 *
 * It also may change attribute's value if normalization of IPv6 expansion is enabled.
 *
 * The following are examples of validation rules using this validator:
 *
 * ```php
 * ['ip_address', 'ip'], // IPv4 or IPv6 address
 * ['ip_address', 'ip', 'ipv6' => false], // IPv4 address (IPv6 is disabled)
 * ['ip_address', 'ip', 'subnet' => true], // requires a CIDR prefix (like 10.0.0.1/24) for the IP address
 * ['ip_address', 'ip', 'subnet' => null], // CIDR prefix is optional
 * ['ip_address', 'ip', 'subnet' => null, 'normalize' => true], // CIDR prefix is optional and will be added when missing
 * ['ip_address', 'ip', 'ranges' => ['192.168.0.0/24']], // only IP addresses from the specified subnet are allowed
 * ['ip_address', 'ip', 'ranges' => ['!192.168.0.0/24', 'any']], // any IP is allowed except IP in the specified subnet
 * ['ip_address', 'ip', 'expandIPv6' => true], // expands IPv6 address to a full notation format
 * ```
 *
 * @property array $ranges The IPv4 or IPv6 ranges that are allowed or forbidden. See [[setRanges()]] for
 * detailed description.
 */
class Ip extends Rule
{
    /**
     * Negation char.
     *
     * Used to negate [[ranges]] or [[networks]] or to negate validating value when [[negation]] is set to `true`.
     * @see negation
     * @see networks
     * @see ranges
     */
    private const NEGATION_CHAR = '!';

    /**
     * @var array The network aliases, that can be used in [[ranges]].
     *  - key - alias name
     *  - value - array of strings. String can be an IP range, IP address or another alias. String can be
     *    negated with [[NEGATION_CHAR]] (independent of `negation` option).
     *
     * The following aliases are defined by default:
     *  - `*`: `any`
     *  - `any`: `0.0.0.0/0, ::/0`
     *  - `private`: `10.0.0.0/8, 172.16.0.0/12, 192.168.0.0/16, fd00::/8`
     *  - `multicast`: `224.0.0.0/4, ff00::/8`
     *  - `linklocal`: `169.254.0.0/16, fe80::/10`
     *  - `localhost`: `127.0.0.0/8', ::1`
     *  - `documentation`: `192.0.2.0/24, 198.51.100.0/24, 203.0.113.0/24, 2001:db8::/32`
     *  - `system`: `multicast, linklocal, localhost, documentation`
     */
    private $networks = [
        '*' => ['any'],
        'any' => ['0.0.0.0/0', '::/0'],
        'private' => ['10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16', 'fd00::/8'],
        'multicast' => ['224.0.0.0/4', 'ff00::/8'],
        'linklocal' => ['169.254.0.0/16', 'fe80::/10'],
        'localhost' => ['127.0.0.0/8', '::1'],
        'documentation' => ['192.0.2.0/24', '198.51.100.0/24', '203.0.113.0/24', '2001:db8::/32'],
        'system' => ['multicast', 'linklocal', 'localhost', 'documentation'],
    ];
    /**
     * @var bool whether the validating value can be an IPv6 address. Defaults to `true`.
     */
    private $ipv6 = true;
    /**
     * @var bool whether the validating value can be an IPv4 address. Defaults to `true`.
     */
    private $ipv4 = true;
    /**
     * @var bool whether the address can be an IP with CIDR subnet, like `192.168.10.0/24`.
     * The following values are possible:
     *
     * - `false` - the address must not have a subnet (default).
     * - `true` - specifying a subnet is required.
     * - `null` - specifying a subnet is optional.
     */
    private $subnet = false;
    /**
     * @var bool whether to add the CIDR prefix with the smallest length (32 for IPv4 and 128 for IPv6) to an
     * address without it. Works only when `subnet` is not `false`. For example:
     *  - `10.0.1.5` will normalized to `10.0.1.5/32`
     *  - `2008:db0::1` will be normalized to `2008:db0::1/128`
     *    Defaults to `false`.
     * @see subnet
     */
    private $normalize = false;
    /**
     * @var bool whether address may have a [[NEGATION_CHAR]] character at the beginning.
     * Defaults to `false`.
     */
    private $negation = false;
    /**
     * @var bool whether to expand an IPv6 address to the full notation format.
     * Defaults to `false`.
     */
    private $expandIPv6 = false;
    /**
     * @var string Regexp-pattern to validateValue IPv4 address
     */
    private $ipv4Pattern = '/^(?:(?:2(?:[0-4][0-9]|5[0-5])|[0-1]?[0-9]?[0-9])\.){3}(?:(?:2([0-4][0-9]|5[0-5])|[0-1]?[0-9]?[0-9]))$/';
    /**
     * @var string Regexp-pattern to validateValue IPv6 address
     */
    private $ipv6Pattern = '/^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))$/';
    /**
     * @var string user-defined error message is used when validation fails due to the wrong IP address format.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated
     * - `{value}`: the value of the attribute being validated
     */
    private $message;
    /**
     * @var string user-defined error message is used when validation fails due to the disabled IPv6 validation.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated
     * - `{value}`: the value of the attribute being validated
     *
     * @see ipv6
     */
    private $ipv6NotAllowed;
    /**
     * @var string user-defined error message is used when validation fails due to the disabled IPv4 validation.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated
     * - `{value}`: the value of the attribute being validated
     *
     * @see ipv4
     */
    private $ipv4NotAllowed;
    /**
     * @var string user-defined error message is used when validation fails due to the wrong CIDR.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated
     * - `{value}`: the value of the attribute being validated
     * @see subnet
     */
    private $wrongCidr;
    /**
     * @var string user-defined error message is used when validation fails due to subnet [[subnet]] set to 'only',
     * but the CIDR prefix is not set.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated
     * - `{value}`: the value of the attribute being validated
     *
     * @see subnet
     */
    private $noSubnet;
    /**
     * @var string user-defined error message is used when validation fails
     * due to [[subnet]] is false, but CIDR prefix is present.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated
     * - `{value}`: the value of the attribute being validated
     *
     * @see subnet
     */
    private $hasSubnet;
    /**
     * @var string user-defined error message is used when validation fails due to IP address
     * is not not allowed by [[ranges]] check.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated
     * - `{value}`: the value of the attribute being validated
     *
     * @see ranges
     */
    private $notInRange;

    /**
     * @var array
     */
    private $ranges = [];

    public function __construct()
    {
        $this->message = $this->formatMessage('{attribute} must be a valid IP address.');
        $this->ipv6NotAllowed = $this->formatMessage('{attribute} must not be an IPv6 address.');
        $this->ipv4NotAllowed = $this->formatMessage('{attribute} must not be an IPv4 address.');
        $this->wrongCidr = $this->formatMessage('{attribute} contains wrong subnet mask.');
        $this->noSubnet = $this->formatMessage('{attribute} must be an IP address with specified subnet.');
        $this->hasSubnet = $this->formatMessage('{attribute} must not be a subnet.');
        $this->notInRange = $this->formatMessage('{attribute} is not in the allowed range.');
    }

    /**
     * Set the IPv4 or IPv6 ranges that are allowed or forbidden.
     *
     * The following preparation tasks are performed:
     *
     * - Recursively substitutes aliases (described in [[networks]]) with their values.
     * - Removes duplicates
     *
     * @param array $ranges the IPv4 or IPv6 ranges that are allowed or forbidden.
     *
     * When the array is empty, or the option not set, all IP addresses are allowed.
     *
     * Otherwise, the rules are checked sequentially until the first match is found.
     * An IP address is forbidden, when it has not matched any of the rules.
     *
     * Example:
     *
     * ```php
     * [
     *      'ranges' => [
     *          '192.168.10.128'
     *          '!192.168.10.0/24',
     *          'any' // allows any other IP addresses
     *      ]
     * ]
     * ```
     *
     * In this example, access is allowed for all the IPv4 and IPv6 addresses excluding the `192.168.10.0/24` subnet.
     * IPv4 address `192.168.10.128` is also allowed, because it is listed before the restriction.
     * @property array the IPv4 or IPv6 ranges that are allowed or forbidden.
     * See [[setRanges()]] for detailed description.
     */
    public function setRanges($ranges)
    {
        $this->ranges = $this->prepareRanges((array)$ranges);
    }

    /**
     * @return array The IPv4 or IPv6 ranges that are allowed or forbidden.
     */
    public function getRanges()
    {
        return $this->ranges;
    }

    /**
     * @param bool $ipv6
     *
     * @return Ip
     */
    public function useIpv6(bool $ipv6): self
    {
        $this->ipv6 = $ipv6;

        return $this;
    }

    /**
     * @param bool $ipv4
     *
     * @return Ip
     */
    public function useIpv4($ipv4): self
    {
        $this->ipv4 = $ipv4;

        return $this;
    }

    /**
     * @param bool $subnet
     *
     * @return Ip
     */
    public function useSubnet($subnet): self
    {
        $this->subnet = $subnet;

        return $this;
    }

    /**
     * @param bool $negation
     *
     * @return Ip
     */
    public function useNegation(bool $negation): self
    {
        $this->negation = $negation;

        return $this;
    }

    protected function validateValue($value): Result
    {
        if (!$this->ipv4 && !$this->ipv6) {
            throw new \RuntimeException('Both IPv4 and IPv6 checks can not be disabled at the same time');
        }

        return $this->validateSubnet($value);
    }

    /**
     * Validates an IPv4/IPv6 address or subnet.
     *
     * @param $ip string
     * @return Result
     * string - the validation was successful;
     * array  - an error occurred during the validation.
     * Array[0] contains the text of an error, array[1] contains values for the placeholders in the error message
     */
    private function validateSubnet(string $ip): Result
    {
        $result = new Result();
        if (!is_string($ip)) {
            $result->addError($this->message);
            return $result;
        }

        $negation = null;
        $cidr = null;

        if (preg_match($this->getIpParsePattern(), $ip, $matches)) {
            $negation = ($matches[1] !== '') ? $matches[1] : null;
            $ip = $matches[2];
            $cidr = $matches[4] ?? null;
        }

        if ($this->subnet === true && $cidr === null) {
            $result->addError($this->noSubnet);
            return $result;
        }
        if ($this->subnet === false && $cidr !== null) {
            $result->addError($this->hasSubnet);
            return $result;
        }
        if ($this->negation === false && $negation !== null) {
            $result->addError($this->message);
            return $result;
        }

        if ($this->getIpVersion($ip) === IpHelper::IPV6) {
            if ($cidr !== null) {
                if ($cidr > IpHelper::IPV6_ADDRESS_LENGTH || $cidr < 0) {
                    $result->addError($this->wrongCidr);
                    return $result;
                }
            } else {
                $cidr = IpHelper::IPV6_ADDRESS_LENGTH;
            }

            if (!$this->validateIPv6($ip)) {
                $result->addError($this->message);
                return $result;
            }
            if (!$this->ipv6) {
                $result->addError($this->ipv6NotAllowed);
                return $result;
            }

            if ($this->expandIPv6) {
                $ip = $this->expandIPv6($ip);
            }
        } else {
            if ($cidr !== null) {
                if ($cidr > IpHelper::IPV4_ADDRESS_LENGTH || $cidr < 0) {
                    $result->addError($this->wrongCidr);
                    return $result;
                }
            } else {
                $isCidrDefault = true;
                $cidr = IpHelper::IPV4_ADDRESS_LENGTH;
            }
            if (!$this->validateIPv4($ip)) {
                $result->addError($this->message);
                return $result;
            }
            if (!$this->ipv4) {
                $result->addError($this->ipv4NotAllowed);
                return $result;
            }
        }

        if (!$this->isAllowed($ip, $cidr)) {
            $result->addError($this->notInRange);
            return $result;
        }


        return $result;
    }

    /**
     * Expands an IPv6 address to it's full notation.
     *
     * For example `2001:db8::1` will be expanded to `2001:0db8:0000:0000:0000:0000:0000:0001`.
     *
     * @param string $ip the original IPv6
     * @return string the expanded IPv6
     */
    private function expandIPv6($ip)
    {
        return IpHelper::expandIPv6($ip);
    }

    /**
     * The method checks whether the IP address with specified CIDR is allowed according to the [[ranges]] list.
     *
     * @param string $ip
     * @param int $cidr
     * @return bool
     * @see ranges
     */
    private function isAllowed($ip, $cidr)
    {
        if (empty($this->ranges)) {
            return true;
        }

        foreach ($this->ranges as $string) {
            [$isNegated, $range] = $this->parseNegatedRange($string);
            if ($this->inRange($ip, $cidr, $range)) {
                return !$isNegated;
            }
        }

        return false;
    }

    /**
     * Parses IP address/range for the negation with [[NEGATION_CHAR]].
     *
     * @param $string
     * @return array `[0 => bool, 1 => string]`
     *  - boolean: whether the string is negated
     *  - string: the string without negation (when the negation were present)
     */
    private function parseNegatedRange($string)
    {
        $isNegated = strpos($string, static::NEGATION_CHAR) === 0;
        return [$isNegated, $isNegated ? substr($string, strlen(static::NEGATION_CHAR)) : $string];
    }

    /**
     * Prepares array to fill in [[ranges]].
     *
     *  - Recursively substitutes aliases, described in [[networks]] with their values,
     *  - Removes duplicates.
     *
     * @param $ranges
     * @return array
     * @see networks
     */
    private function prepareRanges($ranges)
    {
        $result = [];
        foreach ($ranges as $string) {
            [$isRangeNegated, $range] = $this->parseNegatedRange($string);
            if (isset($this->networks[$range])) {
                $replacements = $this->prepareRanges($this->networks[$range]);
                foreach ($replacements as &$replacement) {
                    [$isReplacementNegated, $replacement] = $this->parseNegatedRange($replacement);
                    $result[] = ($isRangeNegated && !$isReplacementNegated ? static::NEGATION_CHAR : '') . $replacement;
                }
            } else {
                $result[] = $string;
            }
        }

        return array_unique($result);
    }

    /**
     * Validates IPv4 address.
     *
     * @param string $value
     * @return bool
     */
    protected function validateIPv4($value)
    {
        return preg_match($this->ipv4Pattern, $value) !== 0;
    }

    /**
     * Validates IPv6 address.
     *
     * @param string $value
     * @return bool
     */
    protected function validateIPv6($value)
    {
        return preg_match($this->ipv6Pattern, $value) !== 0;
    }

    /**
     * Gets the IP version.
     *
     * @param string $ip
     * @return int
     */
    private function getIpVersion($ip)
    {
        return IpHelper::getIpVersion($ip);
    }

    /**
     * Used to get the Regexp pattern for initial IP address parsing.
     * @return string
     */
    public function getIpParsePattern()
    {
        return '/^(' . preg_quote(static::NEGATION_CHAR, '/') . '?)(.+?)(\/(\d+))?$/';
    }

    /**
     * Checks whether the IP is in subnet range.
     *
     * @param string $ip an IPv4 or IPv6 address
     * @param int $cidr
     * @param string $range subnet in CIDR format e.g. `10.0.0.0/8` or `2001:af::/64`
     * @return bool
     */
    private function inRange($ip, $cidr, $range)
    {
        return IpHelper::inRange($ip . '/' . $cidr, $range);
    }
}
