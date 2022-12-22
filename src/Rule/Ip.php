<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use InvalidArgumentException;
use Yiisoft\NetworkUtilities\IpHelper;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleWithOptionsInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

use function array_key_exists;
use function strlen;

/**
 * Checks if the value is a valid IPv4/IPv6 address or subnet.
 *
 * @psalm-import-type WhenType from WhenInterface
 *
 * @see IpHandler
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Ip implements RuleWithOptionsInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * Negation character.
     *
     * Used to negate {@see $ranges} or {@see $network} or to negate value validated when {@see $allowNegation}
     * is used.
     */
    private const NEGATION_CHARACTER = '!';
    /**
     * @psalm-var array<string, list<string>>
     *
     * @var array Default network aliases that can be used in {@see $ranges}.
     *
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

    /**
     * @param array $networks Custom network aliases, that can be used in {@see $ranges}:
     *
     *  - key - alias name.
     *  - value - array of strings. String can be an IP range, IP address or another alias. String can be negated
     * with {@see NEGATION_CHARACTER} (independent of {@see $allowNegation} option).
     *
     * The following aliases are defined by default in {@see $defaultNetworks} and will be merged with custom ones:
     *
     *  - `*`: `any`.
     *  - `any`: `0.0.0.0/0, ::/0`.
     *  - `private`: `10.0.0.0/8, 172.16.0.0/12, 192.168.0.0/16, fd00::/8`.
     *  - `multicast`: `224.0.0.0/4, ff00::/8`.
     *  - `linklocal`: `169.254.0.0/16, fe80::/10`.
     *  - `localhost`: `127.0.0.0/8', ::1`.
     *  - `documentation`: `192.0.2.0/24, 198.51.100.0/24, 203.0.113.0/24, 2001:db8::/32`.
     *  - `system`: `multicast, linklocal, localhost, documentation`.
     * @psalm-param array<string, list<string>> $networks
     *
     * @param bool $allowIpv4 Whether the validating value can be an IPv4 address. Defaults to `true`.
     * @param bool $allowIpv6 Whether the validating value can be an IPv6 address. Defaults to `true`.
     * @param bool $allowSubnet Whether the address can be an IP with CIDR subnet, like `192.168.10.0/24`.
     * The following values are possible:
     *
     * - `false` - the address must not have a subnet (default).
     * - `true` - specifying a subnet is optional.
     * @param bool $requireSubnet Whether subnet is required.
     * @param bool $allowNegation Whether an address may have a `!` negation character at the beginning.
     * @param string $incorrectInputMessage A message used when the input it incorrect.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated.
     * - `{type}`: the type of the value being validated.
     * @param string $message Error message used when validation fails due to the wrong IP address format.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated.
     * - `{value}`: the value being validated.
     * @param string $ipv4NotAllowedMessage Error message used when validation fails due to the disabled IPv4
     * validation when {@see $allowIpv4} is set.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated.
     * - `{value}`: the value being validated.
     * @param string $ipv6NotAllowedMessage Error message used when validation fails due to the disabled IPv6
     * validation when {@see $allowIpv6} is set.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated.
     * - `{value}`: the value being validated.
     * @param string $wrongCidrMessage string Error message used when validation fails due to the wrong CIDR when
     * {@see $allowSubnet} is set.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated.
     * - `{value}`: the value being validated.
     * @param string $noSubnetMessage Error message used when validation fails due to {@see $allowSubnet} is used, but
     * the CIDR prefix is not set.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated.
     * - `{value}`: the value being validated.
     * @param string $hasSubnetMessage Error message used when validation fails due to {@see $allowSubnet} is false, but
     * CIDR prefix is present.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated.
     * - `{value}`: the value being validated.
     * @param string $notInRangeMessage Error message used when validation fails due to IP address is not allowed by
     * {@see $ranges} check.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated.
     * - `{value}`: the value being validated.
     * @param string[] $ranges The IPv4 or IPv6 ranges that are allowed or forbidden.
     *
     * The following preparation tasks are performed:
     *
     * - Recursively substitute aliases (described in {@see $networks}) with their values.
     * - Remove duplicates.
     *
     * When the array is empty or the option not set, all IP addresses are allowed.
     *
     * Otherwise, the rules are checked sequentially until the first match is found. An IP address is forbidden,
     * when it has not matched any of the rules.
     *
     * Example:
     *
     * ```php
     * new Ip(ranges: [
     *     '192.168.10.128'
     *     '!192.168.10.0/24',
     *     'any' // allows any other IP addresses
     * ]);
     * ```
     *
     * In this example, access is allowed for all the IPv4 and IPv6 addresses excluding the `192.168.10.0/24`
     * subnet. IPv4 address `192.168.10.128` is also allowed, because it is listed before the restriction.
     * @param bool|callable|null $skipOnEmpty Whether to skip this rule if the validated value is empty / not passed.
     * See {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this rule if any of the previous rules gave an error. See
     * {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying the rule. See {@see WhenInterface}.
     * @psalm-param WhenType $when
     *
     * @throws InvalidArgumentException If configuration is wrong.
     */
    public function __construct(
        private array $networks = [],
        private bool $allowIpv4 = true,
        private bool $allowIpv6 = true,
        private bool $allowSubnet = false,
        private bool $requireSubnet = false,
        private bool $allowNegation = false,
        private string $incorrectInputMessage = 'The value must have a string type.',
        private string $message = 'Must be a valid IP address.',
        private string $ipv4NotAllowedMessage = 'Must not be an IPv4 address.',
        private string $ipv6NotAllowedMessage = 'Must not be an IPv6 address.',
        private string $wrongCidrMessage = 'Contains wrong subnet mask.',
        private string $noSubnetMessage = 'Must be an IP address with specified subnet.',
        private string $hasSubnetMessage = 'Must not be a subnet.',
        private string $notInRangeMessage = 'Is not in the allowed range.',
        private array $ranges = [],
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null,
    ) {
        if (!$this->allowIpv4 && !$this->allowIpv6) {
            throw new InvalidArgumentException('Both IPv4 and IPv6 checks can not be disabled at the same time.');
        }

        foreach ($networks as $key => $_values) {
            if (array_key_exists($key, $this->defaultNetworks)) {
                throw new InvalidArgumentException("Network alias \"{$key}\" already set as default.");
            }
        }

        $this->networks = array_merge($this->defaultNetworks, $this->networks);

        if ($requireSubnet) {
            // Might be a bug of XDebug, because this line is covered by tests (see "IpTest").
            // @codeCoverageIgnoreStart
            $this->allowSubnet = true;
            // @codeCoverageIgnoreEnd
        }

        $this->ranges = $this->prepareRanges($ranges);
    }

    public function getName(): string
    {
        return 'ip';
    }

    /**
     * Get custom network aliases, that can be used in {@see $ranges}.
     *
     * @return array Network aliases.
     *
     * @see $networks
     */
    public function getNetworks(): array
    {
        return $this->networks;
    }

    /**
     * Whether the validating value can be an IPv4 address
     *
     * @return bool Whether the validating value can be an IPv4 address. Defaults to `true`.
     *
     * @see $allowIpv4
     */
    public function isIpv4Allowed(): bool
    {
        return $this->allowIpv4;
    }

    /**
     * Whether the validating value can be an IPv6 address.
     *
     * @return bool Whether the validating value can be an IPv6 address. Defaults to `true`.
     *
     * @see $allowIpv6
     */
    public function isIpv6Allowed(): bool
    {
        return $this->allowIpv6;
    }

    /**
     * Whether the address can be an IP with CIDR subnet, like `192.168.10.0/24`.
     *
     * @return bool Whether the address can be an IP with CIDR subnet.
     *
     * @see $allowSubnet
     */
    public function isSubnetAllowed(): bool
    {
        return $this->allowSubnet;
    }

    /**
     * Whether subnet is required.
     *
     * @return bool Whether subnet is required.
     *
     * @see $requireSubnet
     */
    public function isSubnetRequired(): bool
    {
        return $this->requireSubnet;
    }

    /**
     * Whether an address may have a `!` negation character at the beginning.
     *
     * @return bool Whether an address may have a `!` negation character at the beginning.
     *
     * @see $allowNegation
     */
    public function isNegationAllowed(): bool
    {
        return $this->allowNegation;
    }

    /**
     * Get a message used when the input it incorrect.
     *
     * @return string Error message
     *
     * @see $incorrectInputMessage
     */
    public function getIncorrectInputMessage(): string
    {
        return $this->incorrectInputMessage;
    }

    /**
     * Get an error message used when validation fails due to the wrong IP address format.
     *
     * @return string Error message.
     *
     * @see $message
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Get an error message used when validation fails due to the disabled IPv4 validation when
     * {@see $allowIpv4} is set.
     *
     * @return string Error message.
     *
     * @see $ipv4NotAllowedMessage
     */
    public function getIpv4NotAllowedMessage(): string
    {
        return $this->ipv4NotAllowedMessage;
    }

    /**
     * Get error message used when validation fails due to the disabled IPv6 validation when
     * {@see $allowIpv6} is set.
     *
     * @return string Error message.
     *
     * @see $ipv6NotAllowedMessage
     */
    public function getIpv6NotAllowedMessage(): string
    {
        return $this->ipv6NotAllowedMessage;
    }

    /**
     * Get error message used when validation fails due to the wrong CIDR when
     * {@see $allowSubnet} is set.
     *
     * @return string Error message.
     *
     * @see $wrongCidrMessage
     */
    public function getWrongCidrMessage(): string
    {
        return $this->wrongCidrMessage;
    }

    /**
     * Get error message used when validation fails due to {@see $allowSubnet} is used, but
     * the CIDR prefix is not set.
     *
     * @return string Error message.
     *
     * @see $getNoSubnetMessage
     */
    public function getNoSubnetMessage(): string
    {
        return $this->noSubnetMessage;
    }

    /**
     * Get error message used when validation fails due to {@see $allowSubnet} is false, but
     * CIDR prefix is present.
     *
     * @return string Error message.
     *
     * @see $hasSubnetMessage
     */
    public function getHasSubnetMessage(): string
    {
        return $this->hasSubnetMessage;
    }

    /**
     * Get error message used when validation fails due to IP address is not allowed by
     * {@see $ranges} check.
     *
     * @return string Error message.
     *
     * @see $notInRangeMessage
     */
    public function getNotInRangeMessage(): string
    {
        return $this->notInRangeMessage;
    }

    /**
     * Get the IPv4 or IPv6 ranges that are allowed or forbidden.
     *
     * @return string[] The IPv4 or IPv6 ranges that are allowed or forbidden.
     *
     * @see $ranges
     */
    public function getRanges(): array
    {
        return $this->ranges;
    }

    /**
     * Parses IP address/range for the negation with {@see NEGATION_CHARACTER}.
     *
     * @return array The result array consists of 2 elements:
     * - `boolean`: whether the string is negated
     * - `string`: the string without negation (when the negation were present)
     * @psalm-return array{0: bool, 1: string}
     */
    private function parseNegatedRange(string $string): array
    {
        $isNegated = str_starts_with($string, self::NEGATION_CHARACTER);
        return [$isNegated, $isNegated ? substr($string, strlen(self::NEGATION_CHARACTER)) : $string];
    }

    /**
     * Prepares array to fill in {@see $ranges}:
     *
     *  - Recursively substitutes aliases, described in {@see $networks} with their values.
     *  - Removes duplicates.
     *
     * @param string[] $ranges
     *
     * @return string[]
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
                    $result[] = ($isRangeNegated && !$isReplacementNegated ? self::NEGATION_CHARACTER : '') . $replacement;
                }
            } else {
                $result[] = $string;
            }
        }

        return array_unique($result);
    }

    /**
     * Whether the IP address with specified CIDR is allowed according to the {@see $ranges} list.
     */
    public function isAllowed(string $ip): bool
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

    public function getOptions(): array
    {
        return [
            'networks' => $this->networks,
            'allowIpv4' => $this->allowIpv4,
            'allowIpv6' => $this->allowIpv6,
            'allowSubnet' => $this->allowSubnet,
            'requireSubnet' => $this->requireSubnet,
            'allowNegation' => $this->allowNegation,
            'incorrectInputMessage' => [
                'template' => $this->incorrectInputMessage,
                'parameters' => [],
            ],
            'message' => [
                'template' => $this->message,
                'parameters' => [],
            ],
            'ipv4NotAllowedMessage' => [
                'template' => $this->ipv4NotAllowedMessage,
                'parameters' => [],
            ],
            'ipv6NotAllowedMessage' => [
                'template' => $this->ipv6NotAllowedMessage,
                'parameters' => [],
            ],
            'wrongCidrMessage' => [
                'template' => $this->wrongCidrMessage,
                'parameters' => [],
            ],
            'noSubnetMessage' => [
                'template' => $this->noSubnetMessage,
                'parameters' => [],
            ],
            'hasSubnetMessage' => [
                'template' => $this->hasSubnetMessage,
                'parameters' => [],
            ],
            'notInRangeMessage' => [
                'template' => $this->notInRangeMessage,
                'parameters' => [],
            ],
            'ranges' => $this->ranges,
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandler(): string
    {
        return IpHandler::class;
    }
}
