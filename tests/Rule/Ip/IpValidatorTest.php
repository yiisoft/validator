<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Ip;

use RuntimeException;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\Ip\Ip;
use Yiisoft\Validator\Rule\Ip\IpValidator;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\Tests\Rule\AbstractRuleValidatorTest;

/**
 * @group t
 */
final class IpValidatorTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $ranges = ['10.0.0.1', '!10.0.0.0/8', '!babe::/8', 'any'];
        $rule = new Ip();
        $ruleRange = new Ip(ranges: $ranges);
        $ruleRequiredSubnet = new Ip(requireSubnet: true);

        return [
            [$rule, 'not.an.ip', [new Error($rule->message, [])]],
            [$rule, 'bad:forSure', [new Error($rule->message, [])]],
            [$rule, ['what an array', '??'], [new Error($rule->message, [])]],
            [$rule, 123456, [new Error($rule->message, [])]],
            [$rule, true, [new Error($rule->message, [])]],
            [$rule, false, [new Error($rule->message, [])]],

            [$rule, '2008:fz::0', [new Error($rule->message, [])]],
            [$rule, '2008:fa::0::1', [new Error($rule->message, [])]],
            [$rule, '!2008:fa::0::1', [new Error($rule->message, [])]],
            [$rule, '2008:fa::0:1/64', [new Error($rule->hasSubnetMessage, [])]],

            [$ruleRange, 'babe::cafe', [new Error($rule->notInRangeMessage, [])]],
            [$ruleRange, '10.0.0.2', [new Error($rule->notInRangeMessage, [])]],

            [$rule, '192.168.005.001', [new Error($rule->message, [])]], // Leading zeroes are not supported
            [$rule, '192.168.5.321', [new Error($rule->message, [])]],
            [$rule, '!192.168.5.32', [new Error($rule->message, [])]],
            [$rule, '192.168.5.32/11', [new Error($rule->hasSubnetMessage, [])]],
            [new Ip(allowIpv4: false), '192.168.10.11', [new Error($rule->ipv4NotAllowedMessage, [])]],
            [new Ip(allowSubnet: true), '192.168.5.32/33', [new Error($rule->wrongCidrMessage, [])]],
            [new Ip(allowSubnet: true), '192.168.5.32/af', [new Error($rule->message, [])]],
            [new Ip(allowSubnet: true), '192.168.5.32/11/12', [new Error($rule->message, [])]],
            [$ruleRequiredSubnet, '10.0.0.1', [new Error($rule->noSubnetMessage, [])]],
            [new Ip(requireSubnet: true, allowNegation: true), '!!192.168.5.32/32', [new Error($rule->message, [])]],

            [new Ip(allowIpv4: false, allowSubnet: true), '!2008:fa::0:1/0', [new Error($rule->message, [])]],
            [new Ip(allowIpv4: false, allowSubnet: true), '2008:fz::0/129', [new Error($rule->message, [])]],
            [new Ip(allowIpv4: false, requireSubnet: true), '2008:db0::1', [new Error($rule->noSubnetMessage, [])]],
            [new Ip(allowIpv4: false, requireSubnet: true, allowNegation: true), '!!2008:fa::0:1/64', [new Error($rule->message, [])]],

            [$rule, '192.168.005.001', [new Error($rule->message, [])]], // Leading zeroes are not allowed
            [$rule, '192.168.5.321', [new Error($rule->message, [])]],
            [$rule, '!192.168.5.32', [new Error($rule->message, [])]],
            [$rule, '192.168.5.32/11', [new Error($rule->hasSubnetMessage, [])]],
            [$rule, '2008:fz::0', [new Error($rule->message, [])]],
            [$rule, '2008:fa::0::1', [new Error($rule->message, [])]],
            [$rule, '!2008:fa::0::1', [new Error($rule->message, [])]],
            [$rule, '2008:fa::0:1/64', [new Error($rule->hasSubnetMessage, [])]],
            [new Ip(allowIpv4: false), '192.168.10.11', [new Error($rule->ipv4NotAllowedMessage, [])]],
            [new Ip(allowIpv6: false), '2008:fa::1', [new Error($rule->ipv6NotAllowedMessage, [])]],
            [$ruleRequiredSubnet, '!2008:fa::0:1/0', [new Error($rule->message, [])]],
            [$ruleRequiredSubnet, '2008:fz::0/129', [new Error($rule->message, [])]],
            [$ruleRequiredSubnet, '192.168.5.32/33', [new Error($rule->wrongCidrMessage, [])]],
            [$ruleRequiredSubnet, '192.168.5.32/af', [new Error($rule->message, [])]],
            [$ruleRequiredSubnet, '192.168.5.32/11/12', [new Error($rule->message, [])]],
            [$ruleRequiredSubnet, '2008:db0::1', [new Error($rule->noSubnetMessage, [])]],
            [$ruleRequiredSubnet, '10.0.0.1', [new Error($rule->noSubnetMessage, [])]],
            [new Ip(requireSubnet: true, allowNegation: true), '!!192.168.5.32/32', [new Error($rule->message, [])]],
            [new Ip(requireSubnet: true, allowNegation: true), '!!2008:fa::0:1/64', [new Error($rule->message, [])]],

            [new Ip(ranges: ['10.0.1.0/24']), '192.5.1.1', [new Error($rule->notInRangeMessage, [])]],
            [new Ip(ranges: ['10.0.1.0/24']), '10.0.3.2', [new Error($rule->notInRangeMessage, [])]],
            [new Ip(ranges: ['!10.0.1.0/24', '10.0.0.0/8', 'localhost']), '10.0.1.2', [new Error($rule->notInRangeMessage, [])]],
            [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '!10.0.0.0/8', 'localhost']), '10.2.2.2', [new Error($rule->notInRangeMessage, [])]],
            [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '!10.0.0.0/8', 'localhost']), '10.0.1.1/22', [new Error($rule->notInRangeMessage, [])]],
            [new Ip(ranges: ['2001:db0:1:1::/64']), '2001:db0:1:2::7', [new Error($rule->notInRangeMessage, [])]],
            [new Ip(ranges: ['!2001:db0::/32', '2001:db0:1:2::/64']), '2001:db0:1:2::7', [new Error($rule->notInRangeMessage, [])]],

            [new Ip(ranges: ['10.0.1.0/24']), '192.5.1.1', [new Error($rule->notInRangeMessage, [])]],
            [new Ip(ranges: ['10.0.1.0/24']), '2001:db0:1:2::7', [new Error($rule->notInRangeMessage, [])]],
            [new Ip(ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', '127.0.0.1']), '10.0.3.2', [new Error($rule->notInRangeMessage, [])]],
            [new Ip(ranges: ['!system', 'any']), '127.0.0.1', [new Error($rule->notInRangeMessage, [])]],
            [new Ip(ranges: ['!system', 'any']), 'fe80::face', [new Error($rule->notInRangeMessage, [])]],

            [
                new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!any']),
                '10.2.2.2',
                [new Error($rule->notInRangeMessage, [])],
            ],
            [
                new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!any']),
                '10.0.1.1/22',
                [new Error($rule->notInRangeMessage, [])],
            ],

            [$rule, '01.01.01.01', [new Error($rule->message, [])]],
            [$rule, '010.010.010.010', [new Error($rule->message, [])]],
            [$rule, '001.001.001.001', [new Error($rule->message, [])]],
        ];
    }

    public function passedValidationProvider(): array
    {
        $ranges = ['10.0.0.1', '!10.0.0.0/8', '!babe::/8', 'any'];
        $rule = new Ip();
        $ruleRange = new Ip(ranges: $ranges);
        $ruleAllowedSubnet = new Ip(allowSubnet: true);
        $ruleRequiredSubnet = new Ip(requireSubnet: true);

        return [
            [$rule, '192.168.10.11'],

            [$ruleRange, '10.0.0.1'],
            [$ruleRange, '192.168.5.101'],
            [$ruleRange, 'cafe::babe'],

            [$ruleAllowedSubnet, '192.168.5.32/11'],
            [$ruleAllowedSubnet, '192.168.5.32/32'],
            [$ruleAllowedSubnet, '0.0.0.0/0'],

            [$ruleRequiredSubnet, '10.0.0.1/24'],
            [$ruleRequiredSubnet, '10.0.0.1/0'],
            [new Ip(requireSubnet: true, allowNegation: true), '!192.168.5.32/32'],

            [$rule, '2008:fa::1'],
            [$rule, '2008:00fa::0001'],
            [new Ip(allowIpv4: false), '2008:fa::1'],
            [new Ip(allowIpv4: false, allowSubnet: true), '2008:fa::0:1/64'],
            [new Ip(allowIpv4: false, allowSubnet: true), '2008:fa::0:1/128'],
            [new Ip(allowIpv4: false, allowSubnet: true), '2008:fa::0:1/0'],
            [new Ip(allowIpv4: false, requireSubnet: true), '2008:db0::1/64'],
            [new Ip(allowIpv4: false, requireSubnet: true, allowNegation: true), '!2008:fa::0:1/64'],

            [$rule, '192.168.10.11'],
            [$rule, '2008:fa::1'],
            [$rule, '2008:00fa::0001'],

            [new Ip(allowIpv4: false), '2008:fa::1'],
            [new Ip(allowIpv6: false), '192.168.10.11'],

            [$ruleRequiredSubnet, '192.168.5.32/11'],
            [$ruleRequiredSubnet, '192.168.5.32/32'],
            [$ruleRequiredSubnet, '0.0.0.0/0'],
            [$ruleRequiredSubnet, '2008:fa::0:1/64'],
            [$ruleRequiredSubnet, '2008:fa::0:1/128'],
            [$ruleRequiredSubnet, '2008:fa::0:1/0'],
            [$ruleRequiredSubnet, '10.0.0.1/24'],
            [$ruleRequiredSubnet, '10.0.0.1/0'],
            [$ruleRequiredSubnet, '2008:db0::1/64'],

            [new Ip(requireSubnet: true, allowNegation: true), '!192.168.5.32/32'],
            [new Ip(requireSubnet: true, allowNegation: true), '!2008:fa::0:1/64'],

            [new Ip(ranges: ['10.0.1.0/24']), '10.0.1.2'],
            [new Ip(ranges: ['10.0.1.0/24']), '10.0.1.2'],
            [new Ip(ranges: ['!10.0.1.0/24', '10.0.0.0/8', 'localhost']), '127.0.0.1'],
            [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '!10.0.0.0/8', 'localhost']), '10.0.1.2'],
            [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '!10.0.0.0/8', 'localhost']), '127.0.0.1'],
            [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '!10.0.0.0/8', 'localhost']), '10.0.1.28/28'],

            [new Ip(ranges: ['2001:db0:1:1::/64']), '2001:db0:1:1::6'],
            [new Ip(ranges: ['2001:db0:1:2::/64']), '2001:db0:1:2::7'],
            [new Ip(allowSubnet: true, ranges: ['2001:db0:1:2::/64', '!2001:db0::/32']), '2001:db0:1:2::7'],

            [new Ip(ranges: ['10.0.1.0/24']), '10.0.1.2'],
            [new Ip(ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', '127.0.0.1']), '2001:db0:1:2::7'],
            [new Ip(ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', '127.0.0.1']), '10.0.1.2'],
            [new Ip(ranges: ['!system', 'any']), '8.8.8.8'],
            [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!any']), '10.0.1.2'],
            [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!any']), '2001:db0:1:2::7'],
            [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!any']), '127.0.0.1'],
            [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!any']), '10.0.1.28/28'],

            [new Ip(networks: ['myNetworkEu' => ['1.2.3.4/10', '5.6.7.8']], ranges: ['myNetworkEu']), '1.2.3.4'],
            [new Ip(networks: ['myNetworkEu' => ['1.2.3.4/10', '5.6.7.8']], ranges: ['myNetworkEu']), '5.6.7.8'],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [new Ip(allowSubnet: true, message: 'Custom error'), '192.168.5.32/af', [new Error('Custom error', [])]],
        ];
    }

    /**
     * @param string[] $ranges
     * @param string[] $expectedRanges
     *
     * @dataProvider rangesForSubstitutionProvider
     */
    public function testRangesForSubstitution(array $ranges, array $expectedRanges): void
    {
        $rule = new Ip(ranges: $ranges);
        $this->assertEquals($expectedRanges, $rule->getRanges());
    }

    public function rangesForSubstitutionProvider(): array
    {
        return [
            'ipv4' => [['10.0.0.1'], ['10.0.0.1']],
            'any' => [['192.168.0.32', 'fa::/32', 'any'], ['192.168.0.32', 'fa::/32', '0.0.0.0/0', '::/0']],
            'ipv4+!private' => [
                ['10.0.0.1', '!private'],
                ['10.0.0.1', '!10.0.0.0/8', '!172.16.0.0/12', '!192.168.0.0/16', '!fd00::/8'],
            ],
            'private+!system' => [
                ['private', '!system'],
                [
                    '10.0.0.0/8',
                    '172.16.0.0/12',
                    '192.168.0.0/16',
                    'fd00::/8',
                    '!224.0.0.0/4',
                    '!ff00::/8',
                    '!169.254.0.0/16',
                    '!fe80::/10',
                    '!127.0.0.0/8',
                    '!::1',
                    '!192.0.2.0/24',
                    '!198.51.100.0/24',
                    '!203.0.113.0/24',
                    '!2001:db8::/32',
                ],
            ],
        ];
    }

    public function testInitException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Both IPv4 and IPv6 checks can not be disabled at the same time');

        $rule = new Ip(allowIpv4: false, allowIpv6: false);
        $this->validate('', $rule);
    }

    protected function getValidator(): RuleValidatorInterface
    {
        return new IpValidator();
    }

    protected function getConfigClassName(): string
    {
        return Ip::class;
    }
}
