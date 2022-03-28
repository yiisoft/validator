<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use InvalidArgumentException;
use RuntimeException;
use Yiisoft\NetworkUtilities\IpHelper;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\ValidationContext;

use function array_key_exists;
use function is_string;
use function strlen;

/**
 * Checks if the value is a valid IPv4/IPv6 address or subnet.
 *
 * It also may change the value if normalization of IPv6 expansion is enabled.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Ip extends Rule
{
    /**
     * Negation char.
     *
     * Used to negate {@see $ranges} or {@see $network} or to negate validating value when {@see $allowNegation}
     * is used.
     */
    private const NEGATION_CHAR = '!';
    /**
     * @see $networks
     */
    private array $defaultNetworks = [
        '*' => ['any'],
        'any' => ['0.0.0.0/0', '::/0'],
        'private' => ['10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16', 'fd00::/8'],
        'multicast' => ['224.0.0.0/4', 'ff00::/8'],
        'linklocal' => ['169.254.0.0/16', 'fe80::/10'],
        'localhost' => ['127.0.0.0/8', '::1'],
        'documentation' => ['192.0.2.0/24', '198.51.100.0/24', '203.0.113.0/24', '2001:db8::/32'],
        'system' => ['multicast', 'linklocal', 'localhost', 'documentation'],
    ];

    public function __construct(
        /**
         * @var array Custom network aliases, that can be used in {@see $ranges}.
         *
         *  - key - alias name
         *  - value - array of strings. String can be an IP range, IP address or another alias. String can be
         *    negated with {@see NEGATION_CHAR} (independent of {@see $allowNegation} option).
         *
         * The following aliases are defined by default in {@see $defaultNetworks} and will be merged with custom ones:
         *
         *  - `*`: `any`
         *  - `any`: `0.0.0.0/0, ::/0`
         *  - `private`: `10.0.0.0/8, 172.16.0.0/12, 192.168.0.0/16, fd00::/8`
         *  - `multicast`: `224.0.0.0/4, ff00::/8`
         *  - `linklocal`: `169.254.0.0/16, fe80::/10`
         *  - `localhost`: `127.0.0.0/8', ::1`
         *  - `documentation`: `192.0.2.0/24, 198.51.100.0/24, 203.0.113.0/24, 2001:db8::/32`
         *  - `system`: `multicast, linklocal, localhost, documentation`
         *
         * @see $defaultNetworks
         */
        private array $networks = [],
        /**
         * @var bool whether the validating value can be an IPv4 address. Defaults to `true`.
         */
        private bool $allowIpv4 = true,
        /**
         * @var bool whether the validating value can be an IPv6 address. Defaults to `true`.
         */
        private bool $allowIpv6 = true,
        /**
         * @var bool whether the address can be an IP with CIDR subnet, like `192.168.10.0/24`.
         * The following values are possible:
         *
         * - `false` - the address must not have a subnet (default).
         * - `true` - specifying a subnet is optional.
         */
        private bool $allowSubnet = false,
        private bool $requireSubnet = false,
        /**
         * @var bool whether address may have a {@see NEGATION_CHAR} character at the beginning.
         * Defaults to `false`.
         */
        private bool $allowNegation = false,
        /**
         * @var string user-defined error message is used when validation fails due to the wrong IP address format.
         *
         * You may use the following placeholders in the message:
         *
         * - `{attribute}`: the label of the attribute being validated
         * - `{value}`: the value of the attribute being validated
         */
        private string $message = 'Must be a valid IP address.',
        /**
         * @var string user-defined error message is used when validation fails due to the disabled IPv4 validation.
         *
         * You may use the following placeholders in the message:
         *
         * - `{attribute}`: the label of the attribute being validated
         * - `{value}`: the value of the attribute being validated
         *
         * @see $allowIpv4
         */
        private string $ipv4NotAllowedMessage = 'Must not be an IPv4 address.',
        /**
         * @var string user-defined error message is used when validation fails due to the disabled IPv6 validation.
         *
         * You may use the following placeholders in the message:
         *
         * - `{attribute}`: the label of the attribute being validated
         * - `{value}`: the value of the attribute being validated
         *
         * @see $allowIpv6
         */
        private string $ipv6NotAllowedMessage = 'Must not be an IPv6 address.',
        /**
         * @var string user-defined error message is used when validation fails due to the wrong CIDR.
         *
         * You may use the following placeholders in the message:
         *
         * - `{attribute}`: the label of the attribute being validated
         * - `{value}`: the value of the attribute being validated
         *
         * @see $allowSubnet
         */
        private string $wrongCidrMessage = 'Contains wrong subnet mask.',
        /**
         * @var string user-defined error message is used when validation fails due to subnet {@see $allowSubnet} is
         * used, but the CIDR prefix is not set.
         *
         * You may use the following placeholders in the message:
         *
         * - `{attribute}`: the label of the attribute being validated
         * - `{value}`: the value of the attribute being validated
         *
         * @see $allowSubnet
         */
        private string $noSubnetMessage = 'Must be an IP address with specified subnet.',
        /**
         * @var string user-defined error message is used when validation fails
         * due to {@see $allowSubnet} is false, but CIDR prefix is present.
         *
         * You may use the following placeholders in the message:
         *
         * - `{attribute}`: the label of the attribute being validated
         * - `{value}`: the value of the attribute being validated
         *
         * @see $allowSubnet
         */
        private string $hasSubnetMessage = 'Must not be a subnet.',
        /**
         * @var string user-defined error message is used when validation fails due to IP address
         * is not allowed by {@see $ranges} check.
         *
         * You may use the following placeholders in the message:
         *
         * - `{attribute}`: the label of the attribute being validated
         * - `{value}`: the value of the attribute being validated
         *
         * @see $ranges
         */
        private string $notInRangeMessage = 'Is not in the allowed range.',
        /**
         * @var array The IPv4 or IPv6 ranges that are allowed or forbidden.
         *
         * The following preparation tasks are performed:
         *
         * - Recursively substitutes aliases (described in {@see $networks}) with their values.
         * - Removes duplicates.
         *
         * When the array is empty, or the option not set, all IP addresses are allowed.
         *
         * Otherwise, the rules are checked sequentially until the first match is found.
         * An IP address is forbidden, when it has not matched any of the rules.
         *
         * Example:
         *
         * ```php
         * (new Ip(ranges: [
         *     '192.168.10.128'
         *     '!192.168.10.0/24',
         *     'any' // allows any other IP addresses
         * ]);
         * ```
         *
         * In this example, access is allowed for all the IPv4 and IPv6 addresses excluding the `192.168.10.0/24`
         * subnet. IPv4 address `192.168.10.128` is also allowed, because it is listed before the restriction.
         */
        private array $ranges = [],
        ?FormatterInterface $formatter = null,
        bool $skipOnEmpty = false,
        bool $skipOnError = false,
        $when = null
    ) {
        parent::__construct(formatter: $formatter, skipOnEmpty: $skipOnEmpty, skipOnError: $skipOnError, when: $when);

        foreach ($networks as $key => $_values) {
            if (array_key_exists($key, $this->defaultNetworks)) {
                throw new RuntimeException("Network alias \"{$key}\" already set as default.");
            }
        }

        $this->networks = array_merge($this->defaultNetworks, $this->networks);

        if ($requireSubnet) {
            $this->allowSubnet = true;
        }

        $this->ranges = $this->prepareRanges($ranges);
    }

    protected function validateValue($value, ?ValidationContext $context = null): Result
    {
        if (!$this->allowIpv4 && !$this->allowIpv6) {
            throw new RuntimeException('Both IPv4 and IPv6 checks can not be disabled at the same time.');
        }
        $result = new Result();
        if (!is_string($value)) {
            $result->addError($this->formatMessage($this->message));
            return $result;
        }

        if (preg_match($this->getIpParsePattern(), $value, $matches) === 0) {
            $result->addError($this->formatMessage($this->message));
            return $result;
        }
        $negation = !empty($matches['not'] ?? null);
        $ip = $matches['ip'];
        $cidr = $matches['cidr'] ?? null;
        $ipCidr = $matches['ipCidr'];

        try {
            $ipVersion = IpHelper::getIpVersion($ip, false);
        } catch (InvalidArgumentException $e) {
            $result->addError($this->formatMessage($this->message));
            return $result;
        }

        if ($this->requireSubnet === true && $cidr === null) {
            $result->addError($this->formatMessage($this->noSubnetMessage));
            return $result;
        }
        if ($this->allowSubnet === false && $cidr !== null) {
            $result->addError($this->formatMessage($this->hasSubnetMessage));
            return $result;
        }
        if ($this->allowNegation === false && $negation) {
            $result->addError($this->formatMessage($this->message));
            return $result;
        }
        if ($ipVersion === IpHelper::IPV6 && !$this->allowIpv6) {
            $result->addError($this->formatMessage($this->ipv6NotAllowedMessage));
            return $result;
        }
        if ($ipVersion === IpHelper::IPV4 && !$this->allowIpv4) {
            $result->addError($this->formatMessage($this->ipv4NotAllowedMessage));
            return $result;
        }
        if (!$result->isValid()) {
            return $result;
        }
        if ($cidr !== null) {
            try {
                IpHelper::getCidrBits($ipCidr);
            } catch (InvalidArgumentException $e) {
                $result->addError($this->formatMessage($this->wrongCidrMessage));
                return $result;
            }
        }
        if (!$this->isAllowed($ipCidr)) {
            $result->addError($this->formatMessage($this->notInRangeMessage));
            return $result;
        }

        return $result;
    }

    public function getRanges(): array
    {
        return $this->ranges;
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
     * Prepares array to fill in {@see $ranges}.
     *
     *  - Recursively substitutes aliases, described in {@see $networks} with their values,
     *  - Removes duplicates.
     *
     * @see $networks
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
                    $result[] = ($isRangeNegated && !$isReplacementNegated ? self::NEGATION_CHAR : '') . $replacement;
                }
            } else {
                $result[] = $string;
            }
        }

        return array_unique($result);
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

    public function getOptions(): array
    {
        return array_merge(parent::getOptions(), [
            'allowIpv4' => $this->allowIpv4,
            'allowIpv6' => $this->allowIpv6,
            'allowSubnet' => $this->allowSubnet,
            'requireSubnet' => $this->requireSubnet,
            'allowNegation' => $this->allowNegation,
            'message' => $this->formatMessage($this->message),
            'ipv4NotAllowedMessage' => $this->formatMessage($this->ipv4NotAllowedMessage),
            'ipv6NotAllowedMessage' => $this->formatMessage($this->ipv6NotAllowedMessage),
            'wrongCidrMessage' => $this->formatMessage($this->wrongCidrMessage),
            'noSubnetMessage' => $this->formatMessage($this->noSubnetMessage),
            'hasSubnetMessage' => $this->formatMessage($this->hasSubnetMessage),
            'notInRangeMessage' => $this->formatMessage($this->notInRangeMessage),
            'ranges' => $this->ranges,
        ]);
    }
}
