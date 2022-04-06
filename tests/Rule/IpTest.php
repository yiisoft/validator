<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yiisoft\Validator\Rule\Ip;

class IpTest extends TestCase
{
    public function testInitException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Both IPv4 and IPv6 checks can not be disabled at the same time');

        $rule = new Ip(allowIpv4: false, allowIpv6: false);
        $rule->validate('');
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

    public function validateWithRangesProvider(): array
    {
        $ranges = ['10.0.0.1', '!10.0.0.0/8', '!babe::/8', 'any'];

        return [
            [$ranges, '10.0.0.1', true],
            [$ranges, '10.0.0.2', false],
            [$ranges, '192.168.5.101', true],
            [$ranges, 'cafe::babe', true],
            [$ranges, 'babe::cafe', false],
        ];
    }

    /**
     * @param string[] $ranges
     *
     * @dataProvider validateWithRangesProvider
     */
    public function testValidateWithRanges(array $ranges, string $value, bool $expectedIsValid): void
    {
        $rule = new Ip(ranges: $ranges);
        $result = $rule->validate($value);

        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function badIPsProvider(): array
    {
        return [
            'notIpString' => ['not.an.ip'],
            'notIpString2' => ['bad:forSure'],
            'array' => [['what an array', '??']],
            'int' => [123456],
            'boolTrue' => [true],
            'boolFalse' => [false],
        ];
    }

    /**
     * @dataProvider badIPsProvider
     */
    public function testValidateBadIPs(mixed $value): void
    {
        $rule = new Ip();
        $result = $rule->validate($value);

        $this->assertFalse($result->isValid());
    }

    public function validateIPv4Provider(): array
    {
        return [
            [new Ip(), '192.168.10.11', true],
            [new Ip(), '192.168.005.001', false], // Leading zeroes are not supported
            [new Ip(), '192.168.5.321', false],
            [new Ip(), '!192.168.5.32', false],
            [new Ip(), '192.168.5.32/11', false],

            [new Ip(allowIpv4: false), '192.168.10.11', false],

            [new Ip(allowSubnet: true), '192.168.5.32/11', true],
            [new Ip(allowSubnet: true), '192.168.5.32/32', true],
            [new Ip(allowSubnet: true), '0.0.0.0/0', true],
            [new Ip(allowSubnet: true), '192.168.5.32/33', false],
            [new Ip(allowSubnet: true), '192.168.5.32/33', false],
            [new Ip(allowSubnet: true), '192.168.5.32/af', false],
            [new Ip(allowSubnet: true), '192.168.5.32/11/12', false],

            [new Ip(requireSubnet: true), '10.0.0.1/24', true],
            [new Ip(requireSubnet: true), '10.0.0.1/0', true],
            [new Ip(requireSubnet: true), '10.0.0.1', false],
            [new Ip(requireSubnet: true, allowNegation: true), '!192.168.5.32/32', true],
            [new Ip(requireSubnet: true, allowNegation: true), '!!192.168.5.32/32', false],
        ];
    }

    /**
     * @dataProvider validateIPv4Provider
     */
    public function testValidateIPv4(Ip $rule, string $value, bool $expectedIsValid): void
    {
        $result = $rule->validate($value);
        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function validateIPv6Provider(): array
    {
        return [
            [new Ip(), '2008:fa::1', true],
            [new Ip(), '2008:00fa::0001', true],
            [new Ip(), '2008:fz::0', false],
            [new Ip(), '2008:fa::0::1', false],
            [new Ip(), '!2008:fa::0::1', false],
            [new Ip(), '2008:fa::0:1/64', false],

            [new Ip(allowIpv4: false), '2008:fa::1', true],

            [new Ip(allowIpv4: false, allowSubnet: true), '2008:fa::0:1/64', true],
            [new Ip(allowIpv4: false, allowSubnet: true), '2008:fa::0:1/128', true],
            [new Ip(allowIpv4: false, allowSubnet: true), '2008:fa::0:1/0', true],
            [new Ip(allowIpv4: false, allowSubnet: true), '!2008:fa::0:1/0', false],
            [new Ip(allowIpv4: false, allowSubnet: true), '2008:fz::0/129', false],

            [new Ip(allowIpv4: false, requireSubnet: true), '2008:db0::1/64', true],
            [new Ip(allowIpv4: false, requireSubnet: true), '2008:db0::1', false],

            [new Ip(allowIpv4: false, requireSubnet: true, allowNegation: true), '!2008:fa::0:1/64', true],
            [new Ip(allowIpv4: false, requireSubnet: true, allowNegation: true), '!!2008:fa::0:1/64', false],
        ];
    }

    /**
     * @dataProvider validateIPv6Provider
     */
    public function testValidateIPv6(Ip $rule, string $value, bool $expectedIsValid): void
    {
        $result = $rule->validate($value);
        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function validateIPvBothProvider(): array
    {
        return [
            [new Ip(), '192.168.10.11', true],
            [new Ip(), '2008:fa::1', true],
            [new Ip(), '2008:00fa::0001', true],
            [new Ip(), '192.168.005.001', false], // Leading zeroes are not allowed
            [new Ip(), '192.168.5.321', false],
            [new Ip(), '!192.168.5.32', false],
            [new Ip(), '192.168.5.32/11', false],
            [new Ip(), '2008:fz::0', false],
            [new Ip(), '2008:fa::0::1', false],
            [new Ip(), '!2008:fa::0::1', false],
            [new Ip(), '2008:fa::0:1/64', false],

            [new Ip(allowIpv4: false), '192.168.10.11', false],
            [new Ip(allowIpv4: false), '2008:fa::1', true],

            [new Ip(allowIpv6: false), '192.168.10.11', true],
            [new Ip(allowIpv6: false), '2008:fa::1', false],

            [new Ip(requireSubnet: true), '192.168.5.32/11', true],
            [new Ip(requireSubnet: true), '192.168.5.32/32', true],
            [new Ip(requireSubnet: true), '0.0.0.0/0', true],
            [new Ip(requireSubnet: true), '2008:fa::0:1/64', true],
            [new Ip(requireSubnet: true), '2008:fa::0:1/128', true],
            [new Ip(requireSubnet: true), '2008:fa::0:1/0', true],
            [new Ip(requireSubnet: true), '!2008:fa::0:1/0', false],
            [new Ip(requireSubnet: true), '192.168.5.32/33', false],
            [new Ip(requireSubnet: true), '2008:fz::0/129', false],
            [new Ip(requireSubnet: true), '192.168.5.32/33', false],
            [new Ip(requireSubnet: true), '192.168.5.32/af', false],
            [new Ip(requireSubnet: true), '192.168.5.32/11/12', false],
            [new Ip(requireSubnet: true), '10.0.0.1/24', true],
            [new Ip(requireSubnet: true), '10.0.0.1/0', true],
            [new Ip(requireSubnet: true), '2008:db0::1/64', true],
            [new Ip(requireSubnet: true), '2008:db0::1', false],
            [new Ip(requireSubnet: true), '10.0.0.1', false],

            [new Ip(requireSubnet: true, allowNegation: true), '!192.168.5.32/32', true],
            [new Ip(requireSubnet: true, allowNegation: true), '!2008:fa::0:1/64', true],
            [new Ip(requireSubnet: true, allowNegation: true), '!!192.168.5.32/32', false],
            [new Ip(requireSubnet: true, allowNegation: true), '!!2008:fa::0:1/64', false],
        ];
    }

    /**
     * @dataProvider validateIPvBothProvider
     */
    public function testValidateIPvBoth(Ip $rule, string $value, bool $expectedIsValid): void
    {
        $result = $rule->validate($value);
        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function validateRangeIPv4Provider(): array
    {
        return [
            [new Ip(ranges: ['10.0.1.0/24']), '10.0.1.2', true],
            [new Ip(ranges: ['10.0.1.0/24']), '192.5.1.1', false],
            [new Ip(ranges: ['10.0.1.0/24']), '10.0.1.2', true],
            [new Ip(ranges: ['10.0.1.0/24']), '10.0.3.2', false],

            [new Ip(ranges: ['!10.0.1.0/24', '10.0.0.0/8', 'localhost']), '10.0.1.2', false],
            [new Ip(ranges: ['!10.0.1.0/24', '10.0.0.0/8', 'localhost']), '127.0.0.1', true],

            [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '!10.0.0.0/8', 'localhost']), '10.0.1.2', true],
            [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '!10.0.0.0/8', 'localhost']), '127.0.0.1', true],
            [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '!10.0.0.0/8', 'localhost']), '10.0.1.28/28', true],
            [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '!10.0.0.0/8', 'localhost']), '10.2.2.2', false],
            [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '!10.0.0.0/8', 'localhost']), '10.0.1.1/22', false],
        ];
    }

    /**
     * @dataProvider validateRangeIPv4Provider
     */
    public function testValidateRangeIPv4(Ip $rule, string $value, bool $expectedIsValid): void
    {
        $result = $rule->validate($value);
        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function validateRangeIPv6Provider(): array
    {
        return [
            [new Ip(ranges: ['2001:db0:1:1::/64']), '2001:db0:1:1::6', true],
            [new Ip(ranges: ['2001:db0:1:1::/64']), '2001:db0:1:2::7', false],

            [new Ip(ranges: ['2001:db0:1:2::/64']), '2001:db0:1:2::7', true],

            [new Ip(ranges: ['!2001:db0::/32', '2001:db0:1:2::/64']), '2001:db0:1:2::7', false],

            [new Ip(allowSubnet: true, ranges: ['2001:db0:1:2::/64', '!2001:db0::/32']), '2001:db0:1:2::7', true],
        ];
    }

    /**
     * @dataProvider validateRangeIPv6Provider
     */
    public function testValidateRangeIPv6(Ip $rule, string $value, bool $expectedIsValid): void
    {
        $result = $rule->validate($value);
        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function validateRangeIPvBothProvider(): array
    {
        return [
            [new Ip(ranges: ['10.0.1.0/24']), '10.0.1.2', true],
            [new Ip(ranges: ['10.0.1.0/24']), '192.5.1.1', false],
            [new Ip(ranges: ['10.0.1.0/24']), '2001:db0:1:2::7', false],

            [new Ip(ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', '127.0.0.1']), '2001:db0:1:2::7', true],
            [new Ip(ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', '127.0.0.1']), '10.0.1.2', true],
            [new Ip(ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', '127.0.0.1']), '10.0.3.2', false],

            [new Ip(ranges: ['!system', 'any']), '127.0.0.1', false],
            [new Ip(ranges: ['!system', 'any']), 'fe80::face', false],
            [new Ip(ranges: ['!system', 'any']), '8.8.8.8', true],

            [
                new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!any']),
                '10.0.1.2',
                true,
            ],
            [
                new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!any']),
                '2001:db0:1:2::7',
                true,
            ],
            [
                new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!any']),
                '127.0.0.1',
                true,
            ],
            [
                new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!any']),
                '10.0.1.28/28',
                true,
            ],
            [
                new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!any']),
                '10.2.2.2',
                false,
            ],
            [
                new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!any']),
                '10.0.1.1/22',
                false,
            ],
        ];
    }

    /**
     * @dataProvider validateRangeIPvBothProvider
     */
    public function testValidateRangeIPvBoth(Ip $rule, string $value, bool $expectedIsValid): void
    {
        $result = $rule->validate($value);
        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function validateIPv4LeadingZeroProvider(): array
    {
        return [
            ['01.01.01.01'],
            ['010.010.010.010'],
            ['001.001.001.001'],
        ];
    }

    /**
     * @dataProvider validateIPv4LeadingZeroProvider
     */
    public function testValidateIPv4LeadingZero(string $value): void
    {
        $rule = new Ip();
        $result = $rule->validate($value);

        $this->assertFalse($result->isValid());
    }

    public function networkAliasProvider(): array
    {
        return [
            ['1.2.3.4'],
            ['5.6.7.8'],
        ];
    }

    /**
     * @dataProvider networkAliasProvider
     */
    public function testNetworkAlias(string $value): void
    {
        $rule = new Ip(networks: ['myNetworkEu' => ['1.2.3.4/10', '5.6.7.8']], ranges: ['myNetworkEu']);
        $result = $rule->validate($value);

        $this->assertTrue($result->isValid());
    }

    public function testNetworkAliasException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Network alias "*" already set as default');

        new Ip(networks: ['*' => ['wrong']], ranges: ['*']);
    }

    public function testGetName(): void
    {
        $this->assertEquals('ip', (new Ip())->getName());
    }

    public function getOptionsProvider(): array
    {
        return [
            [
                new Ip(),
                [
                    'allowIpv4' => true,
                    'allowIpv6' => true,
                    'allowSubnet' => false,
                    'requireSubnet' => false,
                    'allowNegation' => false,
                    'message' => 'Must be a valid IP address.',
                    'ipv4NotAllowedMessage' => 'Must not be an IPv4 address.',
                    'ipv6NotAllowedMessage' => 'Must not be an IPv6 address.',
                    'wrongCidrMessage' => 'Contains wrong subnet mask.',
                    'noSubnetMessage' => 'Must be an IP address with specified subnet.',
                    'hasSubnetMessage' => 'Must not be a subnet.',
                    'notInRangeMessage' => 'Is not in the allowed range.',
                    'ranges' => [],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Ip(allowIpv4: false),
                [
                    'allowIpv4' => false,
                    'allowIpv6' => true,
                    'allowSubnet' => false,
                    'requireSubnet' => false,
                    'allowNegation' => false,
                    'message' => 'Must be a valid IP address.',
                    'ipv4NotAllowedMessage' => 'Must not be an IPv4 address.',
                    'ipv6NotAllowedMessage' => 'Must not be an IPv6 address.',
                    'wrongCidrMessage' => 'Contains wrong subnet mask.',
                    'noSubnetMessage' => 'Must be an IP address with specified subnet.',
                    'hasSubnetMessage' => 'Must not be a subnet.',
                    'notInRangeMessage' => 'Is not in the allowed range.',
                    'ranges' => [],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Ip(allowIpv6: false),
                [
                    'allowIpv4' => true,
                    'allowIpv6' => false,
                    'allowSubnet' => false,
                    'requireSubnet' => false,
                    'allowNegation' => false,
                    'message' => 'Must be a valid IP address.',
                    'ipv4NotAllowedMessage' => 'Must not be an IPv4 address.',
                    'ipv6NotAllowedMessage' => 'Must not be an IPv6 address.',
                    'wrongCidrMessage' => 'Contains wrong subnet mask.',
                    'noSubnetMessage' => 'Must be an IP address with specified subnet.',
                    'hasSubnetMessage' => 'Must not be a subnet.',
                    'notInRangeMessage' => 'Is not in the allowed range.',
                    'ranges' => [],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Ip(allowSubnet: true),
                [
                    'allowIpv4' => true,
                    'allowIpv6' => true,
                    'allowSubnet' => true,
                    'requireSubnet' => false,
                    'allowNegation' => false,
                    'message' => 'Must be a valid IP address.',
                    'ipv4NotAllowedMessage' => 'Must not be an IPv4 address.',
                    'ipv6NotAllowedMessage' => 'Must not be an IPv6 address.',
                    'wrongCidrMessage' => 'Contains wrong subnet mask.',
                    'noSubnetMessage' => 'Must be an IP address with specified subnet.',
                    'hasSubnetMessage' => 'Must not be a subnet.',
                    'notInRangeMessage' => 'Is not in the allowed range.',
                    'ranges' => [],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Ip(requireSubnet: true),
                [
                    'allowIpv4' => true,
                    'allowIpv6' => true,
                    'allowSubnet' => true,
                    'requireSubnet' => true,
                    'allowNegation' => false,
                    'message' => 'Must be a valid IP address.',
                    'ipv4NotAllowedMessage' => 'Must not be an IPv4 address.',
                    'ipv6NotAllowedMessage' => 'Must not be an IPv6 address.',
                    'wrongCidrMessage' => 'Contains wrong subnet mask.',
                    'noSubnetMessage' => 'Must be an IP address with specified subnet.',
                    'hasSubnetMessage' => 'Must not be a subnet.',
                    'notInRangeMessage' => 'Is not in the allowed range.',
                    'ranges' => [],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Ip(allowNegation: true),
                [
                    'allowIpv4' => true,
                    'allowIpv6' => true,
                    'allowSubnet' => false,
                    'requireSubnet' => false,
                    'allowNegation' => true,
                    'message' => 'Must be a valid IP address.',
                    'ipv4NotAllowedMessage' => 'Must not be an IPv4 address.',
                    'ipv6NotAllowedMessage' => 'Must not be an IPv6 address.',
                    'wrongCidrMessage' => 'Contains wrong subnet mask.',
                    'noSubnetMessage' => 'Must be an IP address with specified subnet.',
                    'hasSubnetMessage' => 'Must not be a subnet.',
                    'notInRangeMessage' => 'Is not in the allowed range.',
                    'ranges' => [],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Ip(ranges: ['private']),
                [
                    'allowIpv4' => true,
                    'allowIpv6' => true,
                    'allowSubnet' => false,
                    'requireSubnet' => false,
                    'allowNegation' => false,
                    'message' => 'Must be a valid IP address.',
                    'ipv4NotAllowedMessage' => 'Must not be an IPv4 address.',
                    'ipv6NotAllowedMessage' => 'Must not be an IPv6 address.',
                    'wrongCidrMessage' => 'Contains wrong subnet mask.',
                    'noSubnetMessage' => 'Must be an IP address with specified subnet.',
                    'hasSubnetMessage' => 'Must not be a subnet.',
                    'notInRangeMessage' => 'Is not in the allowed range.',
                    'ranges' => ['10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16', 'fd00::/8'],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider getOptionsProvider
     */
    public function testGetOptions(Ip $rule, array $expectedOptions): void
    {
        $this->assertEquals($expectedOptions, $rule->getOptions());
    }
}
