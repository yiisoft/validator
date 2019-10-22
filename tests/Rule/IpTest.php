<?php
namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Ip;
use Yiisoft\Validator\Tests\data\FakedValidationModel;

/**
 * @group validators
 */
class IpTest extends TestCase
{
    public function testInitException()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Both IPv4 and IPv6 checks can not be disabled at the same time');
        (new Ip())->ipv4(false)->ipv6(false)->validateValue('any');
    }

    public function provideRangesForSubstitution()
    {
        return [
            ['10.0.0.1', ['10.0.0.1']],
            [['192.168.0.32', 'fa::/32', 'any'], ['192.168.0.32', 'fa::/32', '0.0.0.0/0', '::/0']],
            [['10.0.0.1', '!private'], ['10.0.0.1', '!10.0.0.0/8', '!172.16.0.0/12', '!192.168.0.0/16', '!fd00::/8']],
            [['private', '!system'], ['10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16', 'fd00::/8', '!224.0.0.0/4', '!ff00::/8', '!169.254.0.0/16', '!fe80::/10', '!127.0.0.0/8', '!::1', '!192.0.2.0/24', '!198.51.100.0/24', '!203.0.113.0/24', '!2001:db8::/32']],
        ];
    }

    /**
     * @dataProvider provideRangesForSubstitution
     * @param array $range
     * @param array $expectedRange
     */
    public function testRangesSubstitution($range, $expectedRange)
    {
        $validator = (new Ip())->ranges($range);

        $reflection = new \ReflectionObject($validator);
        $prop = $reflection->getProperty('ranges');
        $prop->setAccessible(true);

        $ranges = ($prop->getValue($validator));

        $this->assertEquals($expectedRange, $ranges);
    }


    public function testValidateOrder()
    {
        $validator = (new Ip())->ranges(['10.0.0.1', '!10.0.0.0/8', '!babe::/8', 'any']);

        $this->assertTrue($validator->validate('10.0.0.1')->isValid());
        $this->assertFalse($validator->validate('10.0.0.2')->isValid());
        $this->assertTrue($validator->validate('192.168.5.101')->isValid());
        $this->assertTrue($validator->validate('cafe::babe')->isValid());
        $this->assertFalse($validator->validate('babe::cafe')->isValid());
    }

    public function provideBadIps()
    {
        return [['not.an.ip'], [['what an array', '??']], [123456], [true], [false], ['bad:forSure']];
    }

    /**
     * @dataProvider provideBadIps
     * @param mixed $badIp
     */
    public function testvalidateNotAnIP($badIp)
    {
        $validator = new Ip();
        try {
            $this->assertFalse($validator->validate($badIp)->isValid());
        } catch (\Error $e) {
            $this->assertInstanceOf('\TypeError', $e);
        }
    }

    /**
     * @dataProvider provideBadIps
     * @param mixed $badIp
     */
    public function testValidateModelAttributeNotAnIP($badIp)
    {
        $validator = new Ip();
        $model = new FakedValidationModel();

        $model->attr_ip = $badIp;
        try {
            $result = $validator->validateAttribute($model, 'attr_ip');
            $this->assertTrue(in_array('{attribute} must be a valid IP address.', $result->getErrors()));
        } catch (\Error $e) {
            $this->assertInstanceOf('\TypeError', $e);
        }

        $validator->ipv4(false);

        $model->attr_ip = $badIp;
        try {
            $result = $validator->validateAttribute($model, 'attr_ip');
            $this->assertTrue(in_array('{attribute} must be a valid IP address.', $result->getErrors()));
        } catch (\Error $e) {
            $this->assertInstanceOf('\TypeError', $e);
        }


        $validator->ipv4(true);
        $validator->ipv6(false);

        $model->attr_ip = $badIp;
        try {
            $result = $validator->validateAttribute($model, 'attr_ip');
            $this->assertTrue(in_array('{attribute} must be a valid IP address.', $result->getErrors()));
        } catch (\Error $e) {
            $this->assertInstanceOf('\TypeError', $e);
        }
    }

    public function testvalidateIPv4()
    {
        $validator = new Ip();

        $this->assertTrue($validator->validate('192.168.10.11')->isValid());
        $this->assertTrue($validator->validate('192.168.005.001')->isValid());
        $this->assertFalse($validator->validate('192.168.5.321')->isValid());
        $this->assertFalse($validator->validate('!192.168.5.32')->isValid());
        $this->assertFalse($validator->validate('192.168.5.32/11')->isValid());

        $validator->ipv4(false);
        $this->assertFalse($validator->validate('192.168.10.11')->isValid());

        $validator->ipv4(true);
        $validator->subnet(null);

        $this->assertTrue($validator->validate('192.168.5.32/11')->isValid());
        $this->assertTrue($validator->validate('192.168.5.32/32')->isValid());
        $this->assertTrue($validator->validate('0.0.0.0/0')->isValid());
        $this->assertFalse($validator->validate('192.168.5.32/33')->isValid());
        $this->assertFalse($validator->validate('192.168.5.32/33')->isValid());
        $this->assertFalse($validator->validate('192.168.5.32/af')->isValid());
        $this->assertFalse($validator->validate('192.168.5.32/11/12')->isValid());

        $validator->subnet(true);
        $this->assertTrue($validator->validate('10.0.0.1/24')->isValid());
        $this->assertTrue($validator->validate('10.0.0.1/0')->isValid());
        $this->assertFalse($validator->validate('10.0.0.1')->isValid());

        $validator->negation(true);
        $this->assertTrue($validator->validate('!192.168.5.32/32')->isValid());
        $this->assertFalse($validator->validate('!!192.168.5.32/32')->isValid());
    }


    public function testvalidateIPv6()
    {
        $validator = new Ip();

        $this->assertTrue($validator->validate('2008:fa::1')->isValid());
        $this->assertTrue($validator->validate('2008:00fa::0001')->isValid());
        $this->assertFalse($validator->validate('2008:fz::0')->isValid());
        $this->assertFalse($validator->validate('2008:fa::0::1')->isValid());
        $this->assertFalse($validator->validate('!2008:fa::0::1')->isValid());
        $this->assertFalse($validator->validate('2008:fa::0:1/64')->isValid());

        $validator->ipv4(false);
        $this->assertTrue($validator->validate('2008:fa::1')->isValid());

        $validator->ipv4(true);
        $validator->ipv6(false);
        $this->assertFalse($validator->validate('2008:fa::1')->isValid());

        $validator->ipv4(false);
        $validator->ipv6(true);
        $validator->subnet(null);

        $this->assertTrue($validator->validate('2008:fa::0:1/64')->isValid());
        $this->assertTrue($validator->validate('2008:fa::0:1/128')->isValid());
        $this->assertTrue($validator->validate('2008:fa::0:1/0')->isValid());
        $this->assertFalse($validator->validate('!2008:fa::0:1/0')->isValid());
        $this->assertFalse($validator->validate('2008:fz::0/129')->isValid());

        $validator->subnet(true);
        $this->assertTrue($validator->validate('2008:db0::1/64')->isValid());
        $this->assertFalse($validator->validate('2008:db0::1')->isValid());

        $validator->negation(true);
        $this->assertTrue($validator->validate('!2008:fa::0:1/64')->isValid());
        $this->assertFalse($validator->validate('!!2008:fa::0:1/64')->isValid());
    }

    public function testvalidateIPvBoth()
    {
        $validator = new Ip();

        $this->assertTrue($validator->validate('192.168.10.11')->isValid());
        $this->assertTrue($validator->validate('2008:fa::1')->isValid());
        $this->assertTrue($validator->validate('2008:00fa::0001')->isValid());
        $this->assertTrue($validator->validate('192.168.005.001')->isValid());
        $this->assertFalse($validator->validate('192.168.5.321')->isValid());
        $this->assertFalse($validator->validate('!192.168.5.32')->isValid());
        $this->assertFalse($validator->validate('192.168.5.32/11')->isValid());
        $this->assertFalse($validator->validate('2008:fz::0')->isValid());
        $this->assertFalse($validator->validate('2008:fa::0::1')->isValid());
        $this->assertFalse($validator->validate('!2008:fa::0::1')->isValid());
        $this->assertFalse($validator->validate('2008:fa::0:1/64')->isValid());

        $validator->ipv4(false);
        $this->assertFalse($validator->validate('192.168.10.11')->isValid());
        $this->assertTrue($validator->validate('2008:fa::1')->isValid());

        $validator->ipv6(false);
        $validator->ipv4(true);
        $this->assertTrue($validator->validate('192.168.10.11')->isValid());
        $this->assertFalse($validator->validate('2008:fa::1')->isValid());

        $validator->ipv6(true);
        $validator->subnet(null);

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

        $validator->subnet(true);
        $this->assertTrue($validator->validate('10.0.0.1/24')->isValid());
        $this->assertTrue($validator->validate('10.0.0.1/0')->isValid());
        $this->assertTrue($validator->validate('2008:db0::1/64')->isValid());
        $this->assertFalse($validator->validate('2008:db0::1')->isValid());
        $this->assertFalse($validator->validate('10.0.0.1')->isValid());

        $validator->negation(true);

        $this->assertTrue($validator->validate('!192.168.5.32/32')->isValid());
        $this->assertTrue($validator->validate('!2008:fa::0:1/64')->isValid());
        $this->assertFalse($validator->validate('!!192.168.5.32/32')->isValid());
        $this->assertFalse($validator->validate('!!2008:fa::0:1/64')->isValid());
    }

    public function testValidateRangeIPv4()
    {
        $validator = (new Ip())->ranges(['10.0.1.0/24']);
        $this->assertTrue($validator->validate('10.0.1.2')->isValid());
        $this->assertFalse($validator->validate('192.5.1.1')->isValid());

        $validator->ranges(['10.0.1.0/24']);
        $this->assertTrue($validator->validate('10.0.1.2')->isValid());
        $this->assertFalse($validator->validate('10.0.3.2')->isValid());

        $validator->ranges(['!10.0.1.0/24', '10.0.0.0/8', 'localhost']);
        $this->assertFalse($validator->validate('10.0.1.2')->isValid());
        $this->assertTrue($validator->validate('127.0.0.1')->isValid());

        $validator->subnet(null);
        $validator->ranges(['10.0.1.0/24', '!10.0.0.0/8', 'localhost']);
        $this->assertTrue($validator->validate('10.0.1.2')->isValid());
        $this->assertTrue($validator->validate('127.0.0.1')->isValid());
        $this->assertTrue($validator->validate('10.0.1.28/28')->isValid());
        $this->assertFalse($validator->validate('10.2.2.2')->isValid());
        $this->assertFalse($validator->validate('10.0.1.1/22')->isValid());
    }

    public function testValidateRangeIPv6()
    {
        $validator = (new Ip())->ranges('2001:db0:1:1::/64');
        $this->assertTrue($validator->validate('2001:db0:1:1::6')->isValid());
        $this->assertFalse($validator->validate('2001:db0:1:2::7')->isValid());

        $validator->ranges(['2001:db0:1:2::/64']);
        $this->assertTrue($validator->validate('2001:db0:1:2::7')->isValid());

        $validator->ranges(['!2001:db0::/32', '2001:db0:1:2::/64']);
        $this->assertFalse($validator->validate('2001:db0:1:2::7')->isValid());

        $validator->subnet(null);
        $validator->ranges(array_reverse($validator->getRanges()));
        $this->assertTrue($validator->validate('2001:db0:1:2::7')->isValid());
    }

    public function testValidateRangeIPvBoth()
    {
        $validator = (new Ip())->ranges('10.0.1.0/24');
        $this->assertTrue($validator->validate('10.0.1.2')->isValid());
        $this->assertFalse($validator->validate('192.5.1.1')->isValid());
        $this->assertFalse($validator->validate('2001:db0:1:2::7')->isValid());

        $validator->ranges(['10.0.1.0/24', '2001:db0:1:2::/64', '127.0.0.1']);
        $this->assertTrue($validator->validate('2001:db0:1:2::7')->isValid());
        $this->assertTrue($validator->validate('10.0.1.2')->isValid());
        $this->assertFalse($validator->validate('10.0.3.2')->isValid());

        $validator->ranges(['!system', 'any']);
        $this->assertFalse($validator->validate('127.0.0.1')->isValid());
        $this->assertFalse($validator->validate('fe80::face')->isValid());
        $this->assertTrue($validator->validate('8.8.8.8')->isValid());

        $validator->subnet(null);
        $validator->ranges(['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!all']);
        $this->assertTrue($validator->validate('10.0.1.2')->isValid());
        $this->assertTrue($validator->validate('2001:db0:1:2::7')->isValid());
        $this->assertTrue($validator->validate('127.0.0.1')->isValid());
        $this->assertTrue($validator->validate('10.0.1.28/28')->isValid());
        $this->assertFalse($validator->validate('10.2.2.2')->isValid());
        $this->assertFalse($validator->validate('10.0.1.1/22')->isValid());
    }

    public function testValidateAttributeIPv4()
    {
        $validator = new Ip();
        $model = new FakedValidationModel();

        $validator->subnet(null);

        $model->attr_ip = '8.8.8.8';
        $result = $validator->validateAttribute($model, 'attr_ip');
        $this->assertTrue($result->isValid());
        $this->assertEquals('8.8.8.8', $model->attr_ip);

        $validator->subnet(false);

        $model->attr_ip = '8.8.8.8';
        $result = $validator->validateAttribute($model, 'attr_ip');
        $this->assertTrue($result->isValid());
        $this->assertEquals('8.8.8.8', $model->attr_ip);

        $model->attr_ip = '8.8.8.8/24';
        $result = $validator->validateAttribute($model, 'attr_ip');
        $this->assertFalse($result->isValid());
        $this->assertTrue(in_array('{attribute} must not be a subnet.', $result->getErrors()));

        $validator->subnet(null);
        $validator->normalize(true);

        $model->attr_ip = '8.8.8.8';
        $result = $validator->validateAttribute($model, 'attr_ip');
        $this->assertTrue($result->isValid());
        $this->assertEquals('8.8.8.8/32', $model->attr_ip);
    }


    public function testValidateAttributeIPv6()
    {
        $validator = new Ip();
        $model = new FakedValidationModel();

        $validator->subnet(null);

        $model->attr_ip = '2001:db0:1:2::1';
        $result = $validator->validateAttribute($model, 'attr_ip');
        $this->assertTrue($result->isValid());
        $this->assertEquals('2001:db0:1:2::1', $model->attr_ip);

        $validator->subnet(false);

        $model->attr_ip = '2001:db0:1:2::7';
        $result = $validator->validateAttribute($model, 'attr_ip');
        $this->assertTrue($result->isValid());
        $this->assertEquals('2001:db0:1:2::7', $model->attr_ip);

        $model->attr_ip = '2001:db0:1:2::7/64';
        $result = $validator->validateAttribute($model, 'attr_ip');
        $this->assertFalse($result->isValid());
        $this->assertTrue(in_array('{attribute} must not be a subnet.', $result->getErrors()));

        $validator->subnet(null);
        $validator->normalize(true);

        $model->attr_ip = 'fa01::1';
        $result = $validator->validateAttribute($model, 'attr_ip');
        $this->assertTrue($result->isValid());
        $this->assertEquals('fa01::1/128', $model->attr_ip);

        $model->attr_ip = 'fa01::1/64';
        $result = $validator->validateAttribute($model, 'attr_ip');
        $this->assertTrue($result->isValid());
        $this->assertEquals('fa01::1/64', $model->attr_ip);

        $validator->expandIPv6(true);

        $model->attr_ip = 'fa01::1/64';
        $result = $validator->validateAttribute($model, 'attr_ip');
        $this->assertTrue($result->isValid());
        $this->assertEquals('fa01:0000:0000:0000:0000:0000:0000:0001/64', $model->attr_ip);

        $model->attr_ip = 'fa01::2/614';
        $result = $validator->validateAttribute($model, 'attr_ip');
        $this->assertFalse($result->isValid());
        $this->assertEquals('fa01::2/614', $model->attr_ip);
        $this->assertTrue(in_array('{attribute} contains wrong subnet mask.', $result->getErrors()));
    }
}
