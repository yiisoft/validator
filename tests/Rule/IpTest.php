<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yiisoft\Validator\Rule\Ip;
use Yiisoft\Validator\Rule\IpHandler;
use Yiisoft\Validator\Tests\Support\ValidatorFactory;
use Yiisoft\Validator\Tests\Support\Rule\RuleWithCustomHandler;

final class IpTest extends TestCase
{
    public function testGetName(): void
    {
        $rule = new Ip();
        $this->assertSame('ip', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new Ip(),
                [
                    'networks' => [
                        '*' => ['any'],
                        'any' => ['0.0.0.0/0', '::/0'],
                        'private' => ['10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16', 'fd00::/8'],
                        'multicast' => ['224.0.0.0/4', 'ff00::/8'],
                        'linklocal' => ['169.254.0.0/16', 'fe80::/10'],
                        'localhost' => ['127.0.0.0/8', '::1'],
                        'documentation' => ['192.0.2.0/24', '198.51.100.0/24', '203.0.113.0/24', '2001:db8::/32'],
                        'system' => ['multicast', 'linklocal', 'localhost', 'documentation'],
                    ],
                    'allowIpv4' => true,
                    'allowIpv6' => true,
                    'allowSubnet' => false,
                    'requireSubnet' => false,
                    'allowNegation' => false,
                    'message' => [
                        'message' => 'Must be a valid IP address.',
                    ],
                    'ipv4NotAllowedMessage' => [
                        'message' => 'Must not be an IPv4 address.',
                    ],
                    'ipv6NotAllowedMessage' => [
                        'message' => 'Must not be an IPv6 address.',
                    ],
                    'wrongCidrMessage' => [
                        'message' => 'Contains wrong subnet mask.',
                    ],
                    'noSubnetMessage' => [
                        'message' => 'Must be an IP address with specified subnet.',
                    ],
                    'hasSubnetMessage' => [
                        'message' => 'Must not be a subnet.',
                    ],
                    'notInRangeMessage' => [
                        'message' => 'Is not in the allowed range.',
                    ],
                    'ranges' => [],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Ip(allowIpv4: false),
                [
                    'networks' => [
                        '*' => ['any'],
                        'any' => ['0.0.0.0/0', '::/0'],
                        'private' => ['10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16', 'fd00::/8'],
                        'multicast' => ['224.0.0.0/4', 'ff00::/8'],
                        'linklocal' => ['169.254.0.0/16', 'fe80::/10'],
                        'localhost' => ['127.0.0.0/8', '::1'],
                        'documentation' => ['192.0.2.0/24', '198.51.100.0/24', '203.0.113.0/24', '2001:db8::/32'],
                        'system' => ['multicast', 'linklocal', 'localhost', 'documentation'],
                    ],
                    'allowIpv4' => false,
                    'allowIpv6' => true,
                    'allowSubnet' => false,
                    'requireSubnet' => false,
                    'allowNegation' => false,
                    'message' => [
                        'message' => 'Must be a valid IP address.',
                    ],
                    'ipv4NotAllowedMessage' => [
                        'message' => 'Must not be an IPv4 address.',
                    ],
                    'ipv6NotAllowedMessage' => [
                        'message' => 'Must not be an IPv6 address.',
                    ],
                    'wrongCidrMessage' => [
                        'message' => 'Contains wrong subnet mask.',
                    ],
                    'noSubnetMessage' => [
                        'message' => 'Must be an IP address with specified subnet.',
                    ],
                    'hasSubnetMessage' => [
                        'message' => 'Must not be a subnet.',
                    ],
                    'notInRangeMessage' => [
                        'message' => 'Is not in the allowed range.',
                    ],
                    'ranges' => [],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Ip(allowIpv6: false),
                [
                    'networks' => [
                        '*' => ['any'],
                        'any' => ['0.0.0.0/0', '::/0'],
                        'private' => ['10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16', 'fd00::/8'],
                        'multicast' => ['224.0.0.0/4', 'ff00::/8'],
                        'linklocal' => ['169.254.0.0/16', 'fe80::/10'],
                        'localhost' => ['127.0.0.0/8', '::1'],
                        'documentation' => ['192.0.2.0/24', '198.51.100.0/24', '203.0.113.0/24', '2001:db8::/32'],
                        'system' => ['multicast', 'linklocal', 'localhost', 'documentation'],
                    ],
                    'allowIpv4' => true,
                    'allowIpv6' => false,
                    'allowSubnet' => false,
                    'requireSubnet' => false,
                    'allowNegation' => false,
                    'message' => [
                        'message' => 'Must be a valid IP address.',
                    ],
                    'ipv4NotAllowedMessage' => [
                        'message' => 'Must not be an IPv4 address.',
                    ],
                    'ipv6NotAllowedMessage' => [
                        'message' => 'Must not be an IPv6 address.',
                    ],
                    'wrongCidrMessage' => [
                        'message' => 'Contains wrong subnet mask.',
                    ],
                    'noSubnetMessage' => [
                        'message' => 'Must be an IP address with specified subnet.',
                    ],
                    'hasSubnetMessage' => [
                        'message' => 'Must not be a subnet.',
                    ],
                    'notInRangeMessage' => [
                        'message' => 'Is not in the allowed range.',
                    ],
                    'ranges' => [],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Ip(allowSubnet: true),
                [
                    'networks' => [
                        '*' => ['any'],
                        'any' => ['0.0.0.0/0', '::/0'],
                        'private' => ['10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16', 'fd00::/8'],
                        'multicast' => ['224.0.0.0/4', 'ff00::/8'],
                        'linklocal' => ['169.254.0.0/16', 'fe80::/10'],
                        'localhost' => ['127.0.0.0/8', '::1'],
                        'documentation' => ['192.0.2.0/24', '198.51.100.0/24', '203.0.113.0/24', '2001:db8::/32'],
                        'system' => ['multicast', 'linklocal', 'localhost', 'documentation'],
                    ],
                    'allowIpv4' => true,
                    'allowIpv6' => true,
                    'allowSubnet' => true,
                    'requireSubnet' => false,
                    'allowNegation' => false,
                    'message' => [
                        'message' => 'Must be a valid IP address.',
                    ],
                    'ipv4NotAllowedMessage' => [
                        'message' => 'Must not be an IPv4 address.',
                    ],
                    'ipv6NotAllowedMessage' => [
                        'message' => 'Must not be an IPv6 address.',
                    ],
                    'wrongCidrMessage' => [
                        'message' => 'Contains wrong subnet mask.',
                    ],
                    'noSubnetMessage' => [
                        'message' => 'Must be an IP address with specified subnet.',
                    ],
                    'hasSubnetMessage' => [
                        'message' => 'Must not be a subnet.',
                    ],
                    'notInRangeMessage' => [
                        'message' => 'Is not in the allowed range.',
                    ],
                    'ranges' => [],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Ip(requireSubnet: true),
                [
                    'networks' => [
                        '*' => ['any'],
                        'any' => ['0.0.0.0/0', '::/0'],
                        'private' => ['10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16', 'fd00::/8'],
                        'multicast' => ['224.0.0.0/4', 'ff00::/8'],
                        'linklocal' => ['169.254.0.0/16', 'fe80::/10'],
                        'localhost' => ['127.0.0.0/8', '::1'],
                        'documentation' => ['192.0.2.0/24', '198.51.100.0/24', '203.0.113.0/24', '2001:db8::/32'],
                        'system' => ['multicast', 'linklocal', 'localhost', 'documentation'],
                    ],
                    'allowIpv4' => true,
                    'allowIpv6' => true,
                    'allowSubnet' => true,
                    'requireSubnet' => true,
                    'allowNegation' => false,
                    'message' => [
                        'message' => 'Must be a valid IP address.',
                    ],
                    'ipv4NotAllowedMessage' => [
                        'message' => 'Must not be an IPv4 address.',
                    ],
                    'ipv6NotAllowedMessage' => [
                        'message' => 'Must not be an IPv6 address.',
                    ],
                    'wrongCidrMessage' => [
                        'message' => 'Contains wrong subnet mask.',
                    ],
                    'noSubnetMessage' => [
                        'message' => 'Must be an IP address with specified subnet.',
                    ],
                    'hasSubnetMessage' => [
                        'message' => 'Must not be a subnet.',
                    ],
                    'notInRangeMessage' => [
                        'message' => 'Is not in the allowed range.',
                    ],
                    'ranges' => [],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Ip(allowNegation: true),
                [
                    'networks' => [
                        '*' => ['any'],
                        'any' => ['0.0.0.0/0', '::/0'],
                        'private' => ['10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16', 'fd00::/8'],
                        'multicast' => ['224.0.0.0/4', 'ff00::/8'],
                        'linklocal' => ['169.254.0.0/16', 'fe80::/10'],
                        'localhost' => ['127.0.0.0/8', '::1'],
                        'documentation' => ['192.0.2.0/24', '198.51.100.0/24', '203.0.113.0/24', '2001:db8::/32'],
                        'system' => ['multicast', 'linklocal', 'localhost', 'documentation'],
                    ],
                    'allowIpv4' => true,
                    'allowIpv6' => true,
                    'allowSubnet' => false,
                    'requireSubnet' => false,
                    'allowNegation' => true,
                    'message' => [
                        'message' => 'Must be a valid IP address.',
                    ],
                    'ipv4NotAllowedMessage' => [
                        'message' => 'Must not be an IPv4 address.',
                    ],
                    'ipv6NotAllowedMessage' => [
                        'message' => 'Must not be an IPv6 address.',
                    ],
                    'wrongCidrMessage' => [
                        'message' => 'Contains wrong subnet mask.',
                    ],
                    'noSubnetMessage' => [
                        'message' => 'Must be an IP address with specified subnet.',
                    ],
                    'hasSubnetMessage' => [
                        'message' => 'Must not be a subnet.',
                    ],
                    'notInRangeMessage' => [
                        'message' => 'Is not in the allowed range.',
                    ],
                    'ranges' => [],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Ip(ranges: ['private']),
                [
                    'networks' => [
                        '*' => ['any'],
                        'any' => ['0.0.0.0/0', '::/0'],
                        'private' => ['10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16', 'fd00::/8'],
                        'multicast' => ['224.0.0.0/4', 'ff00::/8'],
                        'linklocal' => ['169.254.0.0/16', 'fe80::/10'],
                        'localhost' => ['127.0.0.0/8', '::1'],
                        'documentation' => ['192.0.2.0/24', '198.51.100.0/24', '203.0.113.0/24', '2001:db8::/32'],
                        'system' => ['multicast', 'linklocal', 'localhost', 'documentation'],
                    ],
                    'allowIpv4' => true,
                    'allowIpv6' => true,
                    'allowSubnet' => false,
                    'requireSubnet' => false,
                    'allowNegation' => false,
                    'message' => [
                        'message' => 'Must be a valid IP address.',
                    ],
                    'ipv4NotAllowedMessage' => [
                        'message' => 'Must not be an IPv4 address.',
                    ],
                    'ipv6NotAllowedMessage' => [
                        'message' => 'Must not be an IPv6 address.',
                    ],
                    'wrongCidrMessage' => [
                        'message' => 'Contains wrong subnet mask.',
                    ],
                    'noSubnetMessage' => [
                        'message' => 'Must be an IP address with specified subnet.',
                    ],
                    'hasSubnetMessage' => [
                        'message' => 'Must not be a subnet.',
                    ],
                    'notInRangeMessage' => [
                        'message' => 'Is not in the allowed range.',
                    ],
                    'ranges' => ['10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16', 'fd00::/8'],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataOptions
     */
    public function testOptions(Ip $rule, array $expectedOptions): void
    {
        $options = $rule->getOptions();
        $this->assertSame($expectedOptions, $options);
    }

    public function dataValidationPassed(): array
    {
        return [
            ['192.168.10.11', [new Ip()]],

            ['10.0.0.1', [new Ip(ranges: ['10.0.0.1', '!10.0.0.0/8', '!babe::/8', 'any'])]],
            ['192.168.5.101', [new Ip(ranges: ['10.0.0.1', '!10.0.0.0/8', '!babe::/8', 'any'])]],
            ['cafe::babe', [new Ip(ranges: ['10.0.0.1', '!10.0.0.0/8', '!babe::/8', 'any'])]],

            ['192.168.5.32/11', [new Ip(allowSubnet: true)]],
            ['192.168.5.32/32', [new Ip(allowSubnet: true)]],
            ['0.0.0.0/0', [new Ip(allowSubnet: true)]],

            ['10.0.0.1/24', [new Ip(requireSubnet: true)]],
            ['10.0.0.1/0', [new Ip(requireSubnet: true)]],
            ['!192.168.5.32/32', [new Ip(requireSubnet: true, allowNegation: true)]],

            ['2008:fa::1', [new Ip()]],
            ['2008:00fa::0001', [new Ip()]],
            ['2008:fa::1', [new Ip(allowIpv4: false)]],
            ['2008:fa::0:1/64', [new Ip(allowIpv4: false, allowSubnet: true)]],
            ['2008:fa::0:1/128', [new Ip(allowIpv4: false, allowSubnet: true)]],
            ['2008:fa::0:1/0', [new Ip(allowIpv4: false, allowSubnet: true)]],
            ['2008:db0::1/64', [new Ip(allowIpv4: false, requireSubnet: true)]],
            ['!2008:fa::0:1/64', [new Ip(allowIpv4: false, requireSubnet: true, allowNegation: true)]],

            ['192.168.10.11', [new Ip()]],
            ['2008:fa::1', [new Ip()]],
            ['2008:00fa::0001', [new Ip()]],

            ['2008:fa::1', [new Ip(allowIpv4: false)]],
            ['192.168.10.11', [new Ip(allowIpv6: false)]],

            ['192.168.5.32/11', [new Ip(requireSubnet: true)]],
            ['192.168.5.32/32', [new Ip(requireSubnet: true)]],
            ['0.0.0.0/0', [new Ip(requireSubnet: true)]],
            ['2008:fa::0:1/64', [new Ip(requireSubnet: true)]],
            ['2008:fa::0:1/128', [new Ip(requireSubnet: true)]],
            ['2008:fa::0:1/0', [new Ip(requireSubnet: true)]],
            ['10.0.0.1/24', [new Ip(requireSubnet: true)]],
            ['10.0.0.1/0', [new Ip(requireSubnet: true)]],
            ['2008:db0::1/64', [new Ip(requireSubnet: true)]],

            ['!192.168.5.32/32', [new Ip(requireSubnet: true, allowNegation: true)]],
            ['!2008:fa::0:1/64', [new Ip(requireSubnet: true, allowNegation: true)]],

            ['10.0.1.2', [new Ip(ranges: ['10.0.1.0/24'])]],
            ['10.0.1.2', [new Ip(ranges: ['10.0.1.0/24'])]],
            ['127.0.0.1', [new Ip(ranges: ['!10.0.1.0/24', '10.0.0.0/8', 'localhost'])]],
            ['10.0.1.2', [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '!10.0.0.0/8', 'localhost'])]],
            ['127.0.0.1', [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '!10.0.0.0/8', 'localhost'])]],
            ['10.0.1.28/28', [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '!10.0.0.0/8', 'localhost'])]],

            ['2001:db0:1:1::6', [new Ip(ranges: ['2001:db0:1:1::/64'])]],
            ['2001:db0:1:2::7', [new Ip(ranges: ['2001:db0:1:2::/64'])]],
            ['2001:db0:1:2::7', [new Ip(allowSubnet: true, ranges: ['2001:db0:1:2::/64', '!2001:db0::/32'])]],

            ['10.0.1.2', [new Ip(ranges: ['10.0.1.0/24'])]],
            ['2001:db0:1:2::7', [new Ip(ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', '127.0.0.1'])]],
            ['10.0.1.2', [new Ip(ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', '127.0.0.1'])]],
            ['8.8.8.8', [new Ip(ranges: ['!system', 'any'])]],
            [
                '10.0.1.2',
                [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!any'])],
            ],
            [
                '2001:db0:1:2::7',
                [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!any'])],
            ],
            [
                '127.0.0.1',
                [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!any'])],
            ],
            [
                '10.0.1.28/28',
                [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!any'])],
            ],

            ['1.2.3.4', [new Ip(networks: ['myNetworkEu' => ['1.2.3.4/10', '5.6.7.8']], ranges: ['myNetworkEu'])]],
            ['5.6.7.8', [new Ip(networks: ['myNetworkEu' => ['1.2.3.4/10', '5.6.7.8']], ranges: ['myNetworkEu'])]],
        ];
    }

    /**
     * @dataProvider dataValidationPassed
     */
    public function testValidationPassed(mixed $data, array $rules): void
    {
        $result = ValidatorFactory::make()->validate($data, $rules);

        $this->assertTrue($result->isValid());
    }

    public function dataValidationFailed(): array
    {
        $message = 'Must be a valid IP address.';
        $hasSubnetMessage = 'Must not be a subnet.';
        $notInRangeMessage = 'Is not in the allowed range.';
        $ipv4NotAllowedMessage = 'Must not be an IPv4 address.';
        $wrongCidrMessage = 'Contains wrong subnet mask.';
        $noSubnetMessage = 'Must be an IP address with specified subnet.';
        $ipv6NotAllowedMessage = 'Must not be an IPv6 address.';

        return [
            ['not.an.ip', [new Ip()], ['' => [$message]]],
            ['bad:forSure', [new Ip()], ['' => [$message]]],
            [['what an array', '??'], [new Ip()], ['' => [$message]]],
            [123456, [new Ip()], ['' => [$message]]],
            [true, [new Ip()], ['' => [$message]]],
            [false, [new Ip()], ['' => [$message]]],

            ['2008:fz::0', [new Ip()], ['' => [$message]]],
            ['2008:fa::0::1', [new Ip()], ['' => [$message]]],
            ['!2008:fa::0::1', [new Ip()], ['' => [$message]]],
            ['2008:fa::0:1/64', [new Ip()], ['' => [$hasSubnetMessage]]],

            [
                'babe::cafe',
                [new Ip(ranges: ['10.0.0.1', '!10.0.0.0/8', '!babe::/8', 'any'])],
                ['' => [$notInRangeMessage]],
            ],
            [
                '10.0.0.2',
                [new Ip(ranges: ['10.0.0.1', '!10.0.0.0/8', '!babe::/8', 'any'])],
                ['' => [$notInRangeMessage]],
            ],

            ['192.168.005.001', [new Ip()], ['' => [$message]]], // Leading zeroes are not supported
            ['192.168.5.321', [new Ip()], ['' => [$message]]],
            ['!192.168.5.32', [new Ip()], ['' => [$message]]],
            ['192.168.5.32/11', [new Ip()], ['' => [$hasSubnetMessage]]],
            ['192.168.10.11', [new Ip(allowIpv4: false)], ['' => [$ipv4NotAllowedMessage]]],
            ['192.168.5.32/33', [new Ip(allowSubnet: true)], ['' => [$wrongCidrMessage]]],
            ['192.168.5.32/af', [new Ip(allowSubnet: true)], ['' => [$message]]],
            ['192.168.5.32/11/12', [new Ip(allowSubnet: true)], ['' => [$message]]],
            ['10.0.0.1', [new Ip(requireSubnet: true)], ['' => [$noSubnetMessage]]],
            ['!!192.168.5.32/32', [new Ip(requireSubnet: true, allowNegation: true)], ['' => [$message]]],

            ['!2008:fa::0:1/0', [new Ip(allowIpv4: false, allowSubnet: true)], ['' => [$message]]],
            ['2008:fz::0/129', [new Ip(allowIpv4: false, allowSubnet: true)], ['' => [$message]]],
            ['2008:db0::1', [new Ip(allowIpv4: false, requireSubnet: true)], ['' => [$noSubnetMessage]]],
            [
                '!!2008:fa::0:1/64',
                [new Ip(allowIpv4: false, requireSubnet: true, allowNegation: true)],
                ['' => [$message]],
            ],

            ['192.168.005.001', [new Ip()], ['' => [$message]]], // Leading zeroes are not allowed
            ['192.168.5.321', [new Ip()], ['' => [$message]]],
            ['!192.168.5.32', [new Ip()], ['' => [$message]]],
            ['192.168.5.32/11', [new Ip()], ['' => [$hasSubnetMessage]]],
            ['2008:fz::0', [new Ip()], ['' => [$message]]],
            ['2008:fa::0::1', [new Ip()], ['' => [$message]]],
            ['!2008:fa::0::1', [new Ip()], ['' => [$message]]],
            ['2008:fa::0:1/64', [new Ip()], ['' => [$hasSubnetMessage]]],
            ['192.168.10.11', [new Ip(allowIpv4: false)], ['' => [$ipv4NotAllowedMessage]]],
            ['2008:fa::1', [new Ip(allowIpv6: false)], ['' => [$ipv6NotAllowedMessage]]],
            ['!2008:fa::0:1/0', [new Ip(requireSubnet: true)], ['' => [$message]]],
            ['2008:fz::0/129', [new Ip(requireSubnet: true)], ['' => [$message]]],
            ['192.168.5.32/33', [new Ip(requireSubnet: true)], ['' => [$wrongCidrMessage]]],
            ['192.168.5.32/af', [new Ip(requireSubnet: true)], ['' => [$message]]],
            ['192.168.5.32/11/12', [new Ip(requireSubnet: true)], ['' => [$message]]],
            ['2008:db0::1', [new Ip(requireSubnet: true)], ['' => [$noSubnetMessage]]],
            ['10.0.0.1', [new Ip(requireSubnet: true)], ['' => [$noSubnetMessage]]],
            ['!!192.168.5.32/32', [new Ip(requireSubnet: true, allowNegation: true)], ['' => [$message]]],
            ['!!2008:fa::0:1/64', [new Ip(requireSubnet: true, allowNegation: true)], ['' => [$message]]],

            ['192.5.1.1', [new Ip(ranges: ['10.0.1.0/24'])], ['' => [$notInRangeMessage]]],
            ['10.0.3.2', [new Ip(ranges: ['10.0.1.0/24'])], ['' => [$notInRangeMessage]]],
            ['10.0.1.2', [new Ip(ranges: ['!10.0.1.0/24', '10.0.0.0/8', 'localhost'])], ['' => [$notInRangeMessage]]],
            [
                '10.2.2.2',
                [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '!10.0.0.0/8', 'localhost'])],
                ['' => [$notInRangeMessage]],
            ],
            [
                '10.0.1.1/22',
                [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '!10.0.0.0/8', 'localhost'])],
                ['' => [$notInRangeMessage]],
            ],
            ['2001:db0:1:2::7', [new Ip(ranges: ['2001:db0:1:1::/64'])], ['' => [$notInRangeMessage]]],
            [
                '2001:db0:1:2::7',
                [new Ip(ranges: ['!2001:db0::/32', '2001:db0:1:2::/64'])],
                ['' => [$notInRangeMessage]],
            ],

            ['192.5.1.1', [new Ip(ranges: ['10.0.1.0/24'])], ['' => [$notInRangeMessage]]],
            ['2001:db0:1:2::7', [new Ip(ranges: ['10.0.1.0/24'])], ['' => [$notInRangeMessage]]],
            [
                '10.0.3.2',
                [new Ip(ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', '127.0.0.1'])],
                ['' => [$notInRangeMessage]],
            ],
            ['127.0.0.1', [new Ip(ranges: ['!system', 'any'])], ['' => [$notInRangeMessage]]],
            ['fe80::face', [new Ip(ranges: ['!system', 'any'])], ['' => [$notInRangeMessage]]],

            [
                '10.2.2.2',
                [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!any'])],
                ['' => [$notInRangeMessage]],
            ],
            [
                '10.0.1.1/22',
                [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!any'])],
                ['' => [$notInRangeMessage]],
            ],

            ['01.01.01.01', [new Ip()], ['' => [$message]]],
            ['010.010.010.010', [new Ip()], ['' => [$message]]],
            ['001.001.001.001', [new Ip()], ['' => [$message]]],
        ];
    }

    /**
     * @dataProvider dataValidationFailed
     */
    public function testValidationFailed(mixed $data, array $rules, array $errorMessagesIndexedByPath): void
    {
        $result = ValidatorFactory::make()->validate($data, $rules);

        $this->assertFalse($result->isValid());
        $this->assertSame($errorMessagesIndexedByPath, $result->getErrorMessagesIndexedByPath());
    }

    public function testCustomErrorMessage(): void
    {
        $data = '192.168.5.32/af';
        $rules = [new Ip(allowSubnet: true, message: 'Custom error')];

        $result = ValidatorFactory::make()->validate($data, $rules);

        $this->assertFalse($result->isValid());
        $this->assertSame(
            ['' => ['Custom error']],
            $result->getErrorMessagesIndexedByPath()
        );
    }

    public function testNetworkAliasException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Network alias "*" already set as default');
        new Ip(networks: ['*' => ['wrong']], ranges: ['*']);
    }

    public function dataRangesForSubstitution(): array
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
     * @dataProvider dataRangesForSubstitution
     */
    public function testRangesForSubstitution(array $ranges, array $expectedRanges): void
    {
        $rule = new Ip(ranges: $ranges);
        $this->assertSame($expectedRanges, $rule->getRanges());
    }

    public function testInitException(): void
    {
        $rule = new Ip(allowIpv4: false, allowIpv6: false);
        $validator = ValidatorFactory::make();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Both IPv4 and IPv6 checks can not be disabled at the same time');
        $validator->validate('', [$rule]);
    }

    public function testDifferentRuleInHandler(): void
    {
        $rule = new RuleWithCustomHandler(IpHandler::class);
        $validator = ValidatorFactory::make();

        $this->expectExceptionMessageMatches(
            '/.*' . preg_quote(Ip::class) . '.*' . preg_quote(RuleWithCustomHandler::class) . '.*/'
        );
        $validator->validate([], [$rule]);
    }
}
