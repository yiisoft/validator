<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\Ip;

/**
 * @group validators
 */
class IpTest extends TestCase
{
    public function testInitException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Both IPv4 and IPv6 checks can not be disabled at the same time');

        (new Ip(allowIpv4: false, allowIpv6: false))->validate('');
    }

    public function provideRangesForSubstitution(): array
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
     * @dataProvider provideRangesForSubstitution
     */
    public function testRangesSubstitution(array $ranges, array $expectedRanges): void
    {
        $rule = new Ip(ranges: $ranges);
        $this->assertEquals($expectedRanges, $rule->getRanges());
    }

    public function testValidateOrder(): void
    {
        $rule = new Ip(ranges: ['10.0.0.1', '!10.0.0.0/8', '!babe::/8', 'any']);

        $this->assertTrue($rule->validate('10.0.0.1')->isValid());
        $this->assertFalse($rule->validate('10.0.0.2')->isValid());
        $this->assertTrue($rule->validate('192.168.5.101')->isValid());
        $this->assertTrue($rule->validate('cafe::babe')->isValid());
        $this->assertFalse($rule->validate('babe::cafe')->isValid());
    }

    public function provideBadIps(): array
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
     * @dataProvider provideBadIps
     */
    public function testValidateNotAnIP($badIp): void
    {
        $this->assertFalse((new Ip())->validate($badIp)->isValid());
    }

    public function testValidateIPv4(): void
    {
        $rule = new Ip();
        $this->assertTrue($rule->validate('192.168.10.11')->isValid());
        $this->assertFalse($rule->validate('192.168.005.001')->isValid()); // leading zero not supported
        $this->assertFalse($rule->validate('192.168.5.321')->isValid());
        $this->assertFalse($rule->validate('!192.168.5.32')->isValid());
        $this->assertFalse($rule->validate('192.168.5.32/11')->isValid());

        $rule = new Ip(allowIpv4: false);
        $this->assertFalse($rule->validate('192.168.10.11')->isValid());

        $rule = new Ip(allowSubnet: true);
        $this->assertTrue($rule->validate('192.168.5.32/11')->isValid());
        $this->assertTrue($rule->validate('192.168.5.32/32')->isValid());
        $this->assertTrue($rule->validate('0.0.0.0/0')->isValid());
        $this->assertFalse($rule->validate('192.168.5.32/33')->isValid());
        $this->assertFalse($rule->validate('192.168.5.32/33')->isValid());
        $this->assertFalse($rule->validate('192.168.5.32/af')->isValid());
        $this->assertFalse($rule->validate('192.168.5.32/11/12')->isValid());

        $rule = new Ip(requireSubnet: true);
        $this->assertTrue($rule->validate('10.0.0.1/24')->isValid());
        $this->assertTrue($rule->validate('10.0.0.1/0')->isValid());
        $this->assertFalse($rule->validate('10.0.0.1')->isValid());

        $rule = new Ip(requireSubnet: true, allowNegation: true);
        $this->assertTrue($rule->validate('!192.168.5.32/32')->isValid());
        $this->assertFalse($rule->validate('!!192.168.5.32/32')->isValid());
    }

    public function testValidateIPv6(): void
    {
        $rule = new Ip();
        $this->assertTrue($rule->validate('2008:fa::1')->isValid());
        $this->assertTrue($rule->validate('2008:00fa::0001')->isValid());
        $this->assertFalse($rule->validate('2008:fz::0')->isValid());
        $this->assertFalse($rule->validate('2008:fa::0::1')->isValid());
        $this->assertFalse($rule->validate('!2008:fa::0::1')->isValid());
        $this->assertFalse($rule->validate('2008:fa::0:1/64')->isValid());

        $rule = new Ip(allowIpv4: false);
        $this->assertTrue($rule->validate('2008:fa::1')->isValid());

        $rule = new Ip(allowIpv4: false, allowSubnet: true);
        $this->assertTrue($rule->validate('2008:fa::0:1/64')->isValid());
        $this->assertTrue($rule->validate('2008:fa::0:1/128')->isValid());
        $this->assertTrue($rule->validate('2008:fa::0:1/0')->isValid());
        $this->assertFalse($rule->validate('!2008:fa::0:1/0')->isValid());
        $this->assertFalse($rule->validate('2008:fz::0/129')->isValid());

        $rule = new Ip(allowIpv4: false, requireSubnet: true);
        $this->assertTrue($rule->validate('2008:db0::1/64')->isValid());
        $this->assertFalse($rule->validate('2008:db0::1')->isValid());

        $rule = new Ip(allowIpv4: false, requireSubnet: true, allowNegation: true);
        $this->assertTrue($rule->validate('!2008:fa::0:1/64')->isValid());
        $this->assertFalse($rule->validate('!!2008:fa::0:1/64')->isValid());
    }

    public function testValidateIPvBoth(): void
    {
        $rule = new Ip();
        $this->assertTrue($rule->validate('192.168.10.11')->isValid());
        $this->assertTrue($rule->validate('2008:fa::1')->isValid());
        $this->assertTrue($rule->validate('2008:00fa::0001')->isValid());
        $this->assertFalse($rule->validate('192.168.005.001')->isValid()); // leading zero not allowed
        $this->assertFalse($rule->validate('192.168.5.321')->isValid());
        $this->assertFalse($rule->validate('!192.168.5.32')->isValid());
        $this->assertFalse($rule->validate('192.168.5.32/11')->isValid());
        $this->assertFalse($rule->validate('2008:fz::0')->isValid());
        $this->assertFalse($rule->validate('2008:fa::0::1')->isValid());
        $this->assertFalse($rule->validate('!2008:fa::0::1')->isValid());
        $this->assertFalse($rule->validate('2008:fa::0:1/64')->isValid());

        $rule = new Ip(allowIpv4: false);
        $this->assertFalse($rule->validate('192.168.10.11')->isValid());
        $this->assertTrue($rule->validate('2008:fa::1')->isValid());

        $rule = new Ip(allowIpv6: false);
        $this->assertTrue($rule->validate('192.168.10.11')->isValid());
        $this->assertFalse($rule->validate('2008:fa::1')->isValid());

        $rule = new Ip(requireSubnet: true);
        $this->assertTrue($rule->validate('192.168.5.32/11')->isValid());
        $this->assertTrue($rule->validate('192.168.5.32/32')->isValid());
        $this->assertTrue($rule->validate('0.0.0.0/0')->isValid());
        $this->assertTrue($rule->validate('2008:fa::0:1/64')->isValid());
        $this->assertTrue($rule->validate('2008:fa::0:1/128')->isValid());
        $this->assertTrue($rule->validate('2008:fa::0:1/0')->isValid());
        $this->assertFalse($rule->validate('!2008:fa::0:1/0')->isValid());
        $this->assertFalse($rule->validate('192.168.5.32/33')->isValid());
        $this->assertFalse($rule->validate('2008:fz::0/129')->isValid());
        $this->assertFalse($rule->validate('192.168.5.32/33')->isValid());
        $this->assertFalse($rule->validate('192.168.5.32/af')->isValid());
        $this->assertFalse($rule->validate('192.168.5.32/11/12')->isValid());
        $this->assertTrue($rule->validate('10.0.0.1/24')->isValid());
        $this->assertTrue($rule->validate('10.0.0.1/0')->isValid());
        $this->assertTrue($rule->validate('2008:db0::1/64')->isValid());
        $this->assertFalse($rule->validate('2008:db0::1')->isValid());
        $this->assertFalse($rule->validate('10.0.0.1')->isValid());

        $rule = new Ip(requireSubnet: true, allowNegation: true);
        $this->assertTrue($rule->validate('!192.168.5.32/32')->isValid());
        $this->assertTrue($rule->validate('!2008:fa::0:1/64')->isValid());
        $this->assertFalse($rule->validate('!!192.168.5.32/32')->isValid());
        $this->assertFalse($rule->validate('!!2008:fa::0:1/64')->isValid());
    }

    public function testValidateRangeIPv4(): void
    {
        $rule = new Ip(ranges: ['10.0.1.0/24']);
        $this->assertTrue($rule->validate('10.0.1.2')->isValid());
        $this->assertFalse($rule->validate('192.5.1.1')->isValid());

        $rule = new Ip(ranges: ['10.0.1.0/24']);
        $this->assertTrue($rule->validate('10.0.1.2')->isValid());
        $this->assertFalse($rule->validate('10.0.3.2')->isValid());

        $rule = new Ip(ranges: ['!10.0.1.0/24', '10.0.0.0/8', 'localhost']);
        $this->assertFalse($rule->validate('10.0.1.2')->isValid());
        $this->assertTrue($rule->validate('127.0.0.1')->isValid());

        $rule = new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '!10.0.0.0/8', 'localhost']);
        $this->assertTrue($rule->validate('10.0.1.2')->isValid());
        $this->assertTrue($rule->validate('127.0.0.1')->isValid());
        $this->assertTrue($rule->validate('10.0.1.28/28')->isValid());
        $this->assertFalse($rule->validate('10.2.2.2')->isValid());
        $this->assertFalse($rule->validate('10.0.1.1/22')->isValid());
    }

    public function testValidateRangeIPv6(): void
    {
        $rule = new Ip(ranges: ['2001:db0:1:1::/64']);
        $this->assertTrue($rule->validate('2001:db0:1:1::6')->isValid());
        $this->assertFalse($rule->validate('2001:db0:1:2::7')->isValid());

        $rule = new Ip(ranges: ['2001:db0:1:2::/64']);
        $this->assertTrue($rule->validate('2001:db0:1:2::7')->isValid());

        $rule = new Ip(ranges: ['!2001:db0::/32', '2001:db0:1:2::/64']);
        $this->assertFalse($rule->validate('2001:db0:1:2::7')->isValid());

        $rule = new Ip(allowSubnet: true, ranges: array_reverse($rule->getRanges()));
        $this->assertTrue($rule->validate('2001:db0:1:2::7')->isValid());
    }

    public function testValidateRangeIPvBoth(): void
    {
        $rule = new Ip(ranges: ['10.0.1.0/24']);
        $this->assertTrue($rule->validate('10.0.1.2')->isValid());
        $this->assertFalse($rule->validate('192.5.1.1')->isValid());
        $this->assertFalse($rule->validate('2001:db0:1:2::7')->isValid());

        $rule = new Ip(ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', '127.0.0.1']);
        $this->assertTrue($rule->validate('2001:db0:1:2::7')->isValid());
        $this->assertTrue($rule->validate('10.0.1.2')->isValid());
        $this->assertFalse($rule->validate('10.0.3.2')->isValid());

        $rule = new Ip(ranges: ['!system', 'any']);
        $this->assertFalse($rule->validate('127.0.0.1')->isValid());
        $this->assertFalse($rule->validate('fe80::face')->isValid());
        $this->assertTrue($rule->validate('8.8.8.8')->isValid());

        $rule = new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!any']);
        $this->assertTrue($rule->validate('10.0.1.2')->isValid());
        $this->assertTrue($rule->validate('2001:db0:1:2::7')->isValid());
        $this->assertTrue($rule->validate('127.0.0.1')->isValid());
        $this->assertTrue($rule->validate('10.0.1.28/28')->isValid());
        $this->assertFalse($rule->validate('10.2.2.2')->isValid());
        $this->assertFalse($rule->validate('10.0.1.1/22')->isValid());
    }

    public function testIpv4LeadingZero(): void
    {
        $this->assertFalse((new Ip())->validate('01.01.01.01')->isValid());
        $this->assertFalse((new Ip())->validate('010.010.010.010')->isValid());
        $this->assertFalse((new Ip())->validate('001.001.001.001')->isValid());
    }

    public function testNetworkAlias(): void
    {
        $rule = new Ip(networks: ['myNetworkEu' => ['1.2.3.4/10', '5.6.7.8']], ranges: ['myNetworkEu']);

        $this->assertTrue($rule->validate('1.2.3.4')->isValid());
        $this->assertTrue($rule->validate('5.6.7.8')->isValid());
    }

    public function testNetworkAliasException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Network alias "*" already set as default');

        new Ip(networks: ['*' => ['wrong']], ranges: ['*']);
    }

    public function testName(): void
    {
        $this->assertEquals('ip', (new Ip())->getName());
    }

    public function optionsProvider(): array
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
     * @dataProvider optionsProvider
     *
     * @param Rule $rule
     * @param array $expected
     */
    public function testOptions(Rule $rule, array $expected): void
    {
        $this->assertEquals($expected, $rule->getOptions());
    }
}
