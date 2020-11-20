<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\NetworkUtilities\IpHelper;
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
 * @property array $ranges The IPv4 or IPv6 ranges that are allowed or forbidden. See [[setRanges()]] for
 * detailed description.
 */
class Ip extends Rule
{
    /**
     * Negation char.
     *
     * Used to negate [[ranges]] or [[networks]] or to negate validating value when [[negation]] is set to `true`.
     *
     * @see allowNegation
     * @see networks
     * @see ranges
     */
    private const NEGATION_CHAR = '!';

    /**
     * @var array The network aliases, that can be used in {@see ranges()}.
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
    private array $networks = [
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
    private bool $allowIpv6 = true;

    /**
     * @var bool whether the validating value can be an IPv4 address. Defaults to `true`.
     */
    private bool $allowIpv4 = true;

    /**
     * @var bool whether the address can be an IP with CIDR subnet, like `192.168.10.0/24`.
     * The following values are possible:
     *
     * - `false` - the address must not have a subnet (default).
     * - `true` - specifying a subnet is optional.
     */
    private bool $allowSubnet = false;

    /**
     * @var bool
     */
    private bool $requireSubnet = false;

    /**
     * @var bool whether address may have a [[NEGATION_CHAR]] character at the beginning.
     * Defaults to `false`.
     */
    private bool $allowNegation = false;

    /**
     * @var string user-defined error message is used when validation fails due to the wrong IP address format.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated
     * - `{value}`: the value of the attribute being validated
     */
    private string $message = 'Must be a valid IP address.';

    /**
     * @var string user-defined error message is used when validation fails due to the disabled IPv6 validation.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated
     * - `{value}`: the value of the attribute being validated
     *
     * @see allowIpv6
     */
    private string $ipv6NotAllowed = 'Must not be an IPv6 address.';

    /**
     * @var string user-defined error message is used when validation fails due to the disabled IPv4 validation.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated
     * - `{value}`: the value of the attribute being validated
     *
     * @see allowIpv4
     */
    private string $ipv4NotAllowed = 'Must not be an IPv4 address.';

    /**
     * @var string user-defined error message is used when validation fails due to the wrong CIDR.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated
     * - `{value}`: the value of the attribute being validated
     * @see allowSubnet
     */
    private string $wrongCidr = 'Contains wrong subnet mask.';

    /**
     * @var string user-defined error message is used when validation fails due to subnet [[subnet]] set to 'only',
     * but the CIDR prefix is not set.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated
     * - `{value}`: the value of the attribute being validated
     *
     * @see allowSubnet
     */
    private string $noSubnet = 'Must be an IP address with specified subnet.';

    /**
     * @var string user-defined error message is used when validation fails
     * due to [[subnet]] is false, but CIDR prefix is present.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated
     * - `{value}`: the value of the attribute being validated
     *
     * @see allowSubnet
     */
    private string $hasSubnet = 'Must not be a subnet.';

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
    private string $notInRange = 'Is not in the allowed range.';

    /**
     * @var array
     */
    private array $ranges = [];

    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        if (!$this->allowIpv4 && !$this->allowIpv6) {
            throw new \RuntimeException('Both IPv4 and IPv6 checks can not be disabled at the same time');
        }
        $result = new Result();
        if (!is_string($value)) {
            $result->addError($this->translateMessage($this->message));
            return $result;
        }

        if (preg_match($this->getIpParsePattern(), $value, $matches) === 0) {
            $result->addError($this->translateMessage($this->message));
            return $result;
        }
        $negation = !empty($matches['not'] ?? null);
        $ip = $matches['ip'];
        $cidr = $matches['cidr'] ?? null;
        $ipCidr = $matches['ipCidr'];

        try {
            $ipVersion = IpHelper::getIpVersion($ip, false);
        } catch (\InvalidArgumentException $e) {
            $result->addError($this->translateMessage($this->message));
            return $result;
        }

        if ($this->requireSubnet === true && $cidr === null) {
            $result->addError($this->translateMessage($this->noSubnet));
            return $result;
        }
        if ($this->allowSubnet === false && $cidr !== null) {
            $result->addError($this->translateMessage($this->hasSubnet));
            return $result;
        }
        if ($this->allowNegation === false && $negation) {
            $result->addError($this->translateMessage($this->message));
            return $result;
        }
        if ($ipVersion === IpHelper::IPV6 && !$this->allowIpv6) {
            $result->addError($this->translateMessage($this->ipv6NotAllowed));
            return $result;
        }
        if ($ipVersion === IpHelper::IPV4 && !$this->allowIpv4) {
            $result->addError($this->translateMessage($this->ipv4NotAllowed));
            return $result;
        }
        if (!$result->isValid()) {
            return $result;
        }
        if ($cidr !== null) {
            try {
                IpHelper::getCidrBits($ipCidr);
            } catch (\InvalidArgumentException $e) {
                $result->addError($this->translateMessage($this->wrongCidr));
                return $result;
            }
        }
        if (!$this->isAllowed($ipCidr)) {
            $result->addError($this->translateMessage($this->notInRange));
            return $result;
        }

        return $result;
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
     * (new Ip())->ranges([
     *          '192.168.10.128'
     *          '!192.168.10.0/24',
     *          'any' // allows any other IP addresses
     *      ]);
     * ```
     *
     * In this example, access is allowed for all the IPv4 and IPv6 addresses excluding the `192.168.10.0/24` subnet.
     * IPv4 address `192.168.10.128` is also allowed, because it is listed before the restriction.
     */
    public function ranges(array $ranges): self
    {
        $new = clone $this;
        $new->ranges = $this->prepareRanges($ranges);
        return $new;
    }

    /**
     * Define network alias to be used in {@see ranges()}
     *
     * @param string $name name of the network
     * @param array $ranges ranges
     * @return self
     */
    public function network(string $name, array $ranges): self
    {
        if (array_key_exists($name, $this->networks)) {
            throw new \RuntimeException("Network alias \"{$name}\" already set");
        }

        $new = clone $this;
        $new->networks[$name] = $ranges;
        return $new;
    }

    public function getRanges(): array
    {
        return $this->ranges;
    }

    public function allowIpv4(): self
    {
        $new = clone $this;
        $new->allowIpv4 = true;
        return $new;
    }

    public function disallowIpv4(): self
    {
        $new = clone $this;
        $new->allowIpv4 = false;
        return $new;
    }

    public function allowIpv6(): self
    {
        $new = clone $this;
        $new->allowIpv6 = true;
        return $new;
    }

    public function disallowIpv6(): self
    {
        $new = clone $this;
        $new->allowIpv6 = false;
        return $new;
    }

    public function allowNegation(): self
    {
        $new = clone $this;
        $new->allowNegation = true;
        return $new;
    }

    public function disallowNegation(): self
    {
        $new = clone $this;
        $new->allowNegation = false;
        return $new;
    }

    public function allowSubnet(): self
    {
        $new = clone $this;
        $new->allowSubnet = true;
        $new->requireSubnet = false;
        return $new;
    }

    public function requireSubnet(): self
    {
        $new = clone $this;
        $new->allowSubnet = true;
        $new->requireSubnet = true;
        return $new;
    }

    public function disallowSubnet(): self
    {
        $new = clone $this;
        $new->allowSubnet = false;
        $new->requireSubnet = false;
        return $new;
    }

    /**
     * The method checks whether the IP address with specified CIDR is allowed according to the [[ranges]] list.
     *
     * @return bool
     * @see ranges
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
     * Parses IP address/range for the negation with [[NEGATION_CHAR]].
     *
     * @param $string
     * @return array `[0 => bool, 1 => string]`
     *  - boolean: whether the string is negated
     *  - string: the string without negation (when the negation were present)
     */
    private function parseNegatedRange($string): array
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
     * @param array $ranges
     * @return array
     * @see networks
     */
    private function prepareRanges(array $ranges): array
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
     * Used to get the Regexp pattern for initial IP address parsing.
     * @return string
     */
    public function getIpParsePattern(): string
    {
        return '/^(?<not>' . preg_quote(
            static::NEGATION_CHAR,
            '/'
        ) . ')?(?<ipCidr>(?<ip>(?:' . IpHelper::IPV4_PATTERN . ')|(?:' . IpHelper::IPV6_PATTERN . '))(?:\/(?<cidr>-?\d+))?)$/';
    }

    public function getOptions(): array
    {
        return array_merge(
            parent::getOptions(),
            [
                'message' => $this->translateMessage($this->message),
                'allowIpv4' => $this->allowIpv4,
                'ipv4NotAllowedMessage' => $this->translateMessage($this->ipv4NotAllowed),
                'allowIpv6' => $this->allowIpv6,
                'ipv6NotAllowedMessage' => $this->translateMessage($this->ipv6NotAllowed),
                'allowSubnet' => $this->allowSubnet,
                'hasSubnetMessage' => $this->translateMessage($this->hasSubnet),
                'requireSubnet' => $this->requireSubnet,
                'noSubnetMessage' => $this->translateMessage($this->noSubnet),
                'allowNegation' => $this->allowNegation,
                'wrongCidrMessage' => $this->translateMessage($this->wrongCidr),
                'ranges' => $this->ranges,
                'notInRangeMessage' => $this->translateMessage($this->notInRange)
            ],
        );
    }
}
