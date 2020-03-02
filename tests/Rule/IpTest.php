<?php

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
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
        (new Ip())->disallowIpv4()->disallowIpv6()->validate('');
    }

    public function provideRangesForSubstitution(): array
    {
        return [
            'ipv4' => [['10.0.0.1'], ['10.0.0.1']],
            'any' => [['192.168.0.32', 'fa::/32', 'any'], ['192.168.0.32', 'fa::/32', '0.0.0.0/0', '::/0']],
            'ipv4+!private' => [
                ['10.0.0.1', '!private'],
                ['10.0.0.1', '!10.0.0.0/8', '!172.16.0.0/12', '!192.168.0.0/16', '!fd00::/8']
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
                    '!2001:db8::/32'
                ]
            ],
        ];
    }

    /**
     * @dataProvider provideRangesForSubstitution
     */
    public function testRangesSubstitution(array $range, array $expectedRange): void
    {
        $validator = (new Ip())->ranges($range);
        $this->assertEquals($expectedRange, $validator->getRanges());
    }


    public function testValidateOrder(): void
    {
        $validator = (new Ip())->ranges(['10.0.0.1', '!10.0.0.0/8', '!babe::/8', 'any']);

        $this->assertTrue($validator->validate('10.0.0.1')->isValid());
        $this->assertFalse($validator->validate('10.0.0.2')->isValid());
        $this->assertTrue($validator->validate('192.168.5.101')->isValid());
        $this->assertTrue($validator->validate('cafe::babe')->isValid());
        $this->assertFalse($validator->validate('babe::cafe')->isValid());
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
        $validator = new Ip();
        $this->assertFalse($validator->validate($badIp)->isValid());
    }

    public function testValidateIPv4(): void
    {
        $validator = new Ip();

        $this->assertTrue($validator->validate('192.168.10.11')->isValid());
        $this->assertFalse($validator->validate('192.168.005.001')->isValid()); // leading zero not supported
        $this->assertFalse($validator->validate('192.168.5.321')->isValid());
        $this->assertFalse($validator->validate('!192.168.5.32')->isValid());
        $this->assertFalse($validator->validate('192.168.5.32/11')->isValid());

        $validator = $validator->disallowIpv4();
        $this->assertFalse($validator->validate('192.168.10.11')->isValid());

        $validator = $validator->allowIpv4()->allowSubnet();
        $this->assertTrue($validator->validate('192.168.5.32/11')->isValid());
        $this->assertTrue($validator->validate('192.168.5.32/32')->isValid());
        $this->assertTrue($validator->validate('0.0.0.0/0')->isValid());
        $this->assertFalse($validator->validate('192.168.5.32/33')->isValid());
        $this->assertFalse($validator->validate('192.168.5.32/33')->isValid());
        $this->assertFalse($validator->validate('192.168.5.32/af')->isValid());
        $this->assertFalse($validator->validate('192.168.5.32/11/12')->isValid());

        $validator = $validator->requireSubnet();
        $this->assertTrue($validator->validate('10.0.0.1/24')->isValid());
        $this->assertTrue($validator->validate('10.0.0.1/0')->isValid());
        $this->assertFalse($validator->validate('10.0.0.1')->isValid());

        $validator = $validator->allowNegation();
        $this->assertTrue($validator->validate('!192.168.5.32/32')->isValid());
        $this->assertFalse($validator->validate('!!192.168.5.32/32')->isValid());
    }


    public function testValidateIPv6(): void
    {
        $validator = new Ip();

        $this->assertTrue($validator->validate('2008:fa::1')->isValid());
        $this->assertTrue($validator->validate('2008:00fa::0001')->isValid());
        $this->assertFalse($validator->validate('2008:fz::0')->isValid());
        $this->assertFalse($validator->validate('2008:fa::0::1')->isValid());
        $this->assertFalse($validator->validate('!2008:fa::0::1')->isValid());
        $this->assertFalse($validator->validate('2008:fa::0:1/64')->isValid());

        $validator = $validator->disallowIpv4();
        $this->assertTrue($validator->validate('2008:fa::1')->isValid());

        $validator = $validator->allowIpv6()->allowSubnet();
        $this->assertTrue($validator->validate('2008:fa::0:1/64')->isValid());
        $this->assertTrue($validator->validate('2008:fa::0:1/128')->isValid());
        $this->assertTrue($validator->validate('2008:fa::0:1/0')->isValid());
        $this->assertFalse($validator->validate('!2008:fa::0:1/0')->isValid());
        $this->assertFalse($validator->validate('2008:fz::0/129')->isValid());

        $validator = $validator->requireSubnet();
        $this->assertTrue($validator->validate('2008:db0::1/64')->isValid());
        $this->assertFalse($validator->validate('2008:db0::1')->isValid());

        $validator = $validator->allowNegation();
        $this->assertTrue($validator->validate('!2008:fa::0:1/64')->isValid());
        $this->assertFalse($validator->validate('!!2008:fa::0:1/64')->isValid());
    }

    public function testValidateIPvBoth(): void
    {
        $validator = new Ip();

        $this->assertTrue($validator->validate('192.168.10.11')->isValid());
        $this->assertTrue($validator->validate('2008:fa::1')->isValid());
        $this->assertTrue($validator->validate('2008:00fa::0001')->isValid());
        $this->assertFalse($validator->validate('192.168.005.001')->isValid()); // leading zero not allowed
        $this->assertFalse($validator->validate('192.168.5.321')->isValid());
        $this->assertFalse($validator->validate('!192.168.5.32')->isValid());
        $this->assertFalse($validator->validate('192.168.5.32/11')->isValid());
        $this->assertFalse($validator->validate('2008:fz::0')->isValid());
        $this->assertFalse($validator->validate('2008:fa::0::1')->isValid());
        $this->assertFalse($validator->validate('!2008:fa::0::1')->isValid());
        $this->assertFalse($validator->validate('2008:fa::0:1/64')->isValid());

        $validator = $validator->disallowIpv4();
        $this->assertFalse($validator->validate('192.168.10.11')->isValid());
        $this->assertTrue($validator->validate('2008:fa::1')->isValid());

        $validator = $validator->disallowIpv6()->allowIpv4();
        $this->assertTrue($validator->validate('192.168.10.11')->isValid());
        $this->assertFalse($validator->validate('2008:fa::1')->isValid());

        $validator = $validator->allowIpv6()->requireSubnet();
        $this->assertTrue($validator->validate('192.168.5.32/11')->isValid());
        $this->assertTrue($validator->validate('192.168.5.32/32')->isValid());
        $this->assertTrue($validator->validate('0.0.0.0/0')->isValid());
        $this->assertTrue($validator->validate('2008:fa::0:1/64')->isValid());
        $this->assertTrue($validator->validate('2008:fa::0:1/128')->isValid());
        $this->assertTrue($validator->validate('2008:fa::0:1/0')->isValid());
        $this->assertFalse($validator->validate('!2008:fa::0:1/0')->isValid());
        $this->assertFalse($validator->validate('192.168.5.32/33')->isValid());
        $this->assertFalse($validator->validate('2008:fz::0/129')->isValid());
        $this->assertFalse($validator->validate('192.168.5.32/33')->isValid());
        $this->assertFalse($validator->validate('192.168.5.32/af')->isValid());
        $this->assertFalse($validator->validate('192.168.5.32/11/12')->isValid());

        $validator = $validator->requireSubnet();
        $this->assertTrue($validator->validate('10.0.0.1/24')->isValid());
        $this->assertTrue($validator->validate('10.0.0.1/0')->isValid());
        $this->assertTrue($validator->validate('2008:db0::1/64')->isValid());
        $this->assertFalse($validator->validate('2008:db0::1')->isValid());
        $this->assertFalse($validator->validate('10.0.0.1')->isValid());

        $validator = $validator->allowNegation();
        $this->assertTrue($validator->validate('!192.168.5.32/32')->isValid());
        $this->assertTrue($validator->validate('!2008:fa::0:1/64')->isValid());
        $this->assertFalse($validator->validate('!!192.168.5.32/32')->isValid());
        $this->assertFalse($validator->validate('!!2008:fa::0:1/64')->isValid());
    }

    public function testValidateRangeIPv4(): void
    {
        $validator = (new Ip())->ranges(['10.0.1.0/24']);
        $this->assertTrue($validator->validate('10.0.1.2')->isValid());
        $this->assertFalse($validator->validate('192.5.1.1')->isValid());

        $validator = $validator->ranges(['10.0.1.0/24']);
        $this->assertTrue($validator->validate('10.0.1.2')->isValid());
        $this->assertFalse($validator->validate('10.0.3.2')->isValid());

        $validator = $validator->ranges(['!10.0.1.0/24', '10.0.0.0/8', 'localhost']);
        $this->assertFalse($validator->validate('10.0.1.2')->isValid());
        $this->assertTrue($validator->validate('127.0.0.1')->isValid());

        $validator = $validator->allowSubnet()->ranges(['10.0.1.0/24', '!10.0.0.0/8', 'localhost']);
        $this->assertTrue($validator->validate('10.0.1.2')->isValid());
        $this->assertTrue($validator->validate('127.0.0.1')->isValid());
        $this->assertTrue($validator->validate('10.0.1.28/28')->isValid());
        $this->assertFalse($validator->validate('10.2.2.2')->isValid());
        $this->assertFalse($validator->validate('10.0.1.1/22')->isValid());
    }

    public function testValidateRangeIPv6(): void
    {
        $validator = (new Ip())->ranges(['2001:db0:1:1::/64']);
        $this->assertTrue($validator->validate('2001:db0:1:1::6')->isValid());
        $this->assertFalse($validator->validate('2001:db0:1:2::7')->isValid());

        $validator = $validator->ranges(['2001:db0:1:2::/64']);
        $this->assertTrue($validator->validate('2001:db0:1:2::7')->isValid());

        $validator = $validator->ranges(['!2001:db0::/32', '2001:db0:1:2::/64']);
        $this->assertFalse($validator->validate('2001:db0:1:2::7')->isValid());

        $validator = $validator->allowSubnet()->ranges(array_reverse($validator->getRanges()));
        $this->assertTrue($validator->validate('2001:db0:1:2::7')->isValid());
    }

    public function testValidateRangeIPvBoth(): void
    {
        $validator = (new Ip())->ranges(['10.0.1.0/24']);
        $this->assertTrue($validator->validate('10.0.1.2')->isValid());
        $this->assertFalse($validator->validate('192.5.1.1')->isValid());
        $this->assertFalse($validator->validate('2001:db0:1:2::7')->isValid());

        $validator = $validator->ranges(['10.0.1.0/24', '2001:db0:1:2::/64', '127.0.0.1']);
        $this->assertTrue($validator->validate('2001:db0:1:2::7')->isValid());
        $this->assertTrue($validator->validate('10.0.1.2')->isValid());
        $this->assertFalse($validator->validate('10.0.3.2')->isValid());

        $validator = $validator->ranges(['!system', 'any']);
        $this->assertFalse($validator->validate('127.0.0.1')->isValid());
        $this->assertFalse($validator->validate('fe80::face')->isValid());
        $this->assertTrue($validator->validate('8.8.8.8')->isValid());

        $validator = $validator->allowSubnet()->ranges(['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!any']);
        $this->assertTrue($validator->validate('10.0.1.2')->isValid());
        $this->assertTrue($validator->validate('2001:db0:1:2::7')->isValid());
        $this->assertTrue($validator->validate('127.0.0.1')->isValid());
        $this->assertTrue($validator->validate('10.0.1.28/28')->isValid());
        $this->assertFalse($validator->validate('10.2.2.2')->isValid());
        $this->assertFalse($validator->validate('10.0.1.1/22')->isValid());
    }

    public function testIpv4LeadingZero(): void
    {
        $this->assertFalse((new Ip())->validate('01.01.01.01')->isValid());
        $this->assertFalse((new Ip())->validate('010.010.010.010')->isValid());
        $this->assertFalse((new Ip())->validate('001.001.001.001')->isValid());
    }

    public function testNetworkAlias(): void
    {
        $validator = (new Ip())->network('myNetworkEu', ['1.2.3.4/10', '5.6.7.8'])
            ->ranges(['myNetworkEu']);
        $this->assertTrue($validator->validate('1.2.3.4')->isValid());
        $this->assertTrue($validator->validate('5.6.7.8')->isValid());
    }

    public function testNetworkAliasException(): void
    {
        $validator = (new Ip())->network('myNetworkEu', ['1.2.3.4/10', '5.6.7.8'])
            ->ranges(['myNetworkEu']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Network alias "myNetworkEu" already set');
        $validator->network('myNetworkEu', ['!1.2.3.4/10', '!5.6.7.8']);
    }
}
