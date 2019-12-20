<?php

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\DataSetInterface;
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
 * @property array $ranges The IPv4 or IPv6 ranges that are allowed or forbidden. See [[ranges()]] for
 * detailed description.
 */
class Ip extends Rule
{
    public const IPV4 = 4;
    public const IPV6 = 6;

    /**
     * The length of IPv6 address in bits
     */
    const IPV6_ADDRESS_LENGTH = 128;
    /**
     * The length of IPv4 address in bits
     */
    const IPV4_ADDRESS_LENGTH = 32;

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
     * @return $this
     * See [[ranges()]] for detailed description.
     */
    public function ranges($ranges): self
    {
        $this->ranges = $this->prepareRanges((array)$ranges);

        return $this;
    }

    /**
     * @return array The IPv4 or IPv6 ranges that are allowed or forbidden.
     */
    public function getRanges()
    {
        return $this->ranges;
    }

    public function validateValue($value): Result
    {
        if (!$this->ipv4 && !$this->ipv6) {
            throw new \RuntimeException('Both IPv4 and IPv6 checks can not be disabled at the same time');
        }

        $result = $this->validateSubnet($value);
        if (is_array($result)) {
            $result[1] = array_merge(['ip' => is_array($value) ? 'array()' : $value], $result[1]);
            return $result;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAttribute(DataSetInterface $model, $attribute): Result
    {
        $value = $model->$attribute;

        $result = $this->validateSubnet($value);
        if ($value != $model->$attribute) {
            $model->$attribute = $value;
        }
//        if (is_array($result)) {
//            $result[1] = array_merge(['ip' => is_array($value) ? 'array()' : $value], $result[1]);
//            $this->addError($model, $attribute, $result[0], $result[1]);
//        }

        return $result;
    }

    /**
     * Validates an IPv4/IPv6 address or subnet.
     *
     * @param $ip string
     * @return string|array
     * string - the validation was successful;
     * array  - an error occurred during the validation.
     * Array[0] contains the text of an error, array[1] contains values for the placeholders in the error message
     */
    private function validateSubnet(string &$ipAddress)
    {
        $result = new Result();

        $ip = $ipAddress;

        if (!is_string($ip)) {
            $result->addError($this->message);
            return $result;
        }

        $negation = null;
        $cidr = null;
        $isCidrDefault = false;

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

        if (static::getIpVersion($ip) === self::IPV6) {
            if ($cidr !== null) {
                if ($cidr > self::IPV6_ADDRESS_LENGTH || $cidr < 0) {
                    $result->addError($this->wrongCidr);
                    return $result;
                }
            } else {
                $isCidrDefault = true;
                $cidr = self::IPV6_ADDRESS_LENGTH;
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
                $ip = self::_expandIPv6($ip);
            }
        } else {
            if ($cidr !== null) {
                if ($cidr > self::IPV4_ADDRESS_LENGTH || $cidr < 0) {
                    $result->addError($this->wrongCidr);
                    return $result;
                }
            } else {
                $isCidrDefault = true;
                $cidr = self::IPV4_ADDRESS_LENGTH;
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

        $ipAddress = $negation . $ip;

        if ($this->subnet !== false && (!$isCidrDefault || $isCidrDefault && $this->normalize)) {
            $ipAddress .= "/$cidr";
        }

        return $result;
    }

    /**
     * Expands an IPv6 address to it's full notation.
     *
     * For example `2001:db8::1` will be expanded to `2001:0db8:0000:0000:0000:0000:0000:0001`
     *
     * @param string $ip the original valid IPv6 address
     * @return string the expanded IPv6 address
     */
    private static function _expandIPv6($ip)
    {
        $hex = unpack('H*hex', inet_pton($ip));
        return substr(preg_replace('/([a-f0-9]{4})/i', '$1:', $hex['hex']), 0, -1);
    }

    public function expandIPv6(bool $value): self
    {
        $this->expandIPv6 = $value;

        return $this;
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
     * Gets the IP version. Does not perform IP address validation.
     *
     * @param string $ip the valid IPv4 or IPv6 address.
     * @return int [[IPV4]] or [[IPV6]]
     */
    public static function getIpVersion($ip)
    {
        return strpos($ip, ':') === false ? self::IPV4 : self::IPV6;
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
     * Checks whether IP address or subnet $subnet is contained by $subnet.
     *
     * For example, the following code checks whether subnet `192.168.1.0/24` is in subnet `192.168.0.0/22`:
     *
     * ```php
     * IpHelper::inRange('192.168.1.0/24', '192.168.0.0/22'); // true
     * ```
     *
     * In case you need to check whether a single IP address `192.168.1.21` is in the subnet `192.168.1.0/24`,
     * you can use any of theses examples:
     *
     * ```php
     * IpHelper::inRange('192.168.1.21', '192.168.1.0/24'); // true
     * IpHelper::inRange('192.168.1.21/32', '192.168.1.0/24'); // true
     * ```
     *
     * @param string $ip the valid IPv4 or IPv6 address or CIDR range, e.g.: `10.0.0.0/8` or `2001:af::/64`
     * @param int $cidr
     * @param string $range the valid IPv4 or IPv6 CIDR range, e.g. `10.0.0.0/8` or `2001:af::/64`
     * @return bool whether $subnet is contained by $range
     *
     * @see https://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing
     */
    public static function inRange($ip, $cidr, $range)
    {
        $subnet = $ip . '/' . $cidr;
        list($ip, $mask) = array_pad(explode('/', $subnet), 2, null);
        list($net, $netMask) = array_pad(explode('/', $range), 2, null);

        $ipVersion = static::getIpVersion($ip);
        $netVersion = static::getIpVersion($net);
        if ($ipVersion !== $netVersion) {
            return false;
        }

        $maxMask = $ipVersion === self::IPV4 ? self::IPV4_ADDRESS_LENGTH : self::IPV6_ADDRESS_LENGTH;
        $mask = isset($mask) ? $mask : $maxMask;
        $netMask = isset($netMask) ? $netMask : $maxMask;

        $binIp = static::ip2bin($ip);
        $binNet = static::ip2bin($net);
        return substr($binIp, 0, $netMask) === substr($binNet, 0, $netMask) && $mask >= $netMask;
    }

    /**
     * Converts IP address to bits representation.
     *
     * @param string $ip the valid IPv4 or IPv6 address
     * @return string bits as a string
     */
    public static function ip2bin($ip)
    {
        if (static::getIpVersion($ip) === self::IPV4) {
            return str_pad(base_convert(ip2long($ip), 10, 2), self::IPV4_ADDRESS_LENGTH, '0', STR_PAD_LEFT);
        }

        $unpack = unpack('A16', inet_pton($ip));
        $binStr = array_shift($unpack);
        $bytes = self::IPV6_ADDRESS_LENGTH / 8; // 128 bit / 8 = 16 bytes
        $result = '';
        while ($bytes-- > 0) {
            $result = sprintf('%08b', isset($binStr[$bytes]) ? ord($binStr[$bytes]) : '0') . $result;
        }
        return $result;
    }

    public function ipv4(bool $value): self
    {
        $this->ipv4 = $value;

        return $this;
    }

    public function ipv6(bool $value): self
    {
        $this->ipv6 = $value;

        return $this;
    }

    /**
     * @param bool|null $value
     * @return $this
     */
    public function subnet(bool $value = null): self
    {
        $this->subnet = $value;

        return $this;
    }

    public function negation(bool $value): self
    {
        $this->negation = $value;

        return $this;
    }

    public function normalize(bool $value): self
    {
        $this->normalize = $value;

        return $this;
    }
}
