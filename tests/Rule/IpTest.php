<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use RuntimeException;
use Yiisoft\Validator\Rule\Ip;
use Yiisoft\Validator\ParametrizedRuleInterface;

final class IpTest extends AbstractRuleTest
{
    public function optionsDataProvider(): array
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

    public function testNetworkAliasException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Network alias "*" already set as default');

        new Ip(networks: ['*' => ['wrong']], ranges: ['*']);
    }

    protected function getRule(): ParametrizedRuleInterface
    {
        return new Ip([]);
    }
}
