<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\Email;
use function function_exists;

/**
 * @group validators
 */
class EmailTest extends TestCase
{
    public function validateWithDefaultsParametersProvider(): array
    {
        return [
            ['sam@rmcreative.ru', true],
            ['5011@gmail.com', true],
            ['Abc.123@example.com', true],
            ['user+mailbox/department=shipping@example.com', true],
            ['!#$%&\'*+-/=?^_`.{|}~@example.com', true],

            ['rmcreative.ru', false],
            ['Carsten Brandt <mail@cebe.cc>', false],
            ['"Carsten Brandt" <mail@cebe.cc>', false],
            ['<mail@cebe.cc>', false],
            ['info@örtliches.de', false],
            ['sam@рмкреатиф.ru', false],
            ['ex..ample@example.com', false],
            [['developer@yiiframework.com'], false],
        ];
    }

    /**
     * @dataProvider validateWithDefaultsParametersProvider
     */
    public function testValidateWithDefaultParameters(mixed $value, bool $expectedIsValid): void
    {
        $rule = new Email();
        $result = $rule->validate($value);

        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function validateWithAllowNameProvider(): array
    {
        return [
            [new Email(allowName: true), 'sam@rmcreative.ru', true],
            [new Email(allowName: true), '5011@gmail.com', true],
            [new Email(allowName: true), 'rmcreative.ru', false],
            [new Email(allowName: true), 'Carsten Brandt <mail@cebe.cc>', true],
            [new Email(allowName: true), '"Carsten Brandt" <mail@cebe.cc>', true],
            [new Email(allowName: true), '<mail@cebe.cc>', true],
            [new Email(allowName: true), 'info@örtliches.de', false],
            [new Email(allowName: true), 'üñîçøðé@üñîçøðé.com', false],
            [new Email(allowName: true), 'sam@рмкреатиф.ru', false],
            [new Email(allowName: true), 'Informtation info@oertliches.de', false],
            [new Email(allowName: true), 'test@example.com', true],
            [new Email(allowName: true), 'John Smith <john.smith@example.com>', true],
            [
                new Email(allowName: true),
                '"This name is longer than 64 characters. Blah blah blah blah blah" <shortmail@example.com>',
                true,
            ],
            [new Email(allowName: true), 'John Smith <example.com>', false],
            [
                new Email(allowName: true),
                'Short Name <localPartMoreThan64Characters-blah-blah-blah-blah-blah-blah-blah-blah@example.com>',
                false,
            ],
            [
                new Email(allowName: true),
                'Short Name <domainNameIsMoreThan254Characters@example-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah.com>',
                false,
            ],
            [new Email(allowName: true), ['developer@yiiframework.com'], false],
        ];
    }

    /**
     * @dataProvider validateWithAllowNameProvider
     */
    public function testValidateWithAllowName(mixed $value, bool $expectedIsValid): void
    {
        $rule = new Email(allowName: true);
        $result = $rule->validate($value);

        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function testValidateIdn(): void
    {
        if (!function_exists('idn_to_ascii')) {
            $this->markTestSkipped('Intl extension required');
        }

        $rule = new Email(enableIDN: true);

        $this->assertTrue($rule->validate('5011@example.com')->isValid());
        $this->assertTrue($rule->validate('test-@dummy.com')->isValid());
        $this->assertTrue($rule->validate('example@äüößìà.de')->isValid());
        $this->assertTrue($rule->validate('example@xn--zcack7ayc9a.de')->isValid());
        $this->assertTrue($rule->validate('info@örtliches.de')->isValid());
        $this->assertTrue($rule->validate('sam@рмкреатиф.ru')->isValid());
        $this->assertTrue($rule->validate('sam@rmcreative.ru')->isValid());
        $this->assertTrue($rule->validate('5011@gmail.com')->isValid());
        $this->assertTrue($rule->validate('üñîçøðé@üñîçøðé.com')->isValid());
        $this->assertFalse($rule->validate('rmcreative.ru')->isValid());
        $this->assertFalse($rule->validate('Carsten Brandt <mail@cebe.cc>')->isValid());
        $this->assertFalse($rule->validate('"Carsten Brandt" <mail@cebe.cc>')->isValid());
        $this->assertFalse($rule->validate('<mail@cebe.cc>')->isValid());

        $rule = new Email(allowName: true, enableIDN: true);

        $this->assertTrue($rule->validate('info@örtliches.de')->isValid());
        $this->assertTrue($rule->validate('Informtation <info@örtliches.de>')->isValid());
        $this->assertFalse($rule->validate('Informtation info@örtliches.de')->isValid());
        $this->assertTrue($rule->validate('sam@рмкреатиф.ru')->isValid());
        $this->assertTrue($rule->validate('sam@rmcreative.ru')->isValid());
        $this->assertTrue($rule->validate('5011@gmail.com')->isValid());
        $this->assertFalse($rule->validate('rmcreative.ru')->isValid());
        $this->assertTrue($rule->validate('Carsten Brandt <mail@cebe.cc>')->isValid());
        $this->assertTrue($rule->validate('"Carsten Brandt" <mail@cebe.cc>')->isValid());
        $this->assertTrue($rule->validate('üñîçøðé 日本国 <üñîçøðé@üñîçøðé.com>')->isValid());
        $this->assertTrue($rule->validate('<mail@cebe.cc>')->isValid());
        $this->assertTrue($rule->validate('test@example.com')->isValid());
        $this->assertTrue($rule->validate('John Smith <john.smith@example.com>')->isValid());
        $this->assertTrue(
            $rule->validate(
                '"Такое имя достаточно длинное, но оно все равно может пройти валидацию" <shortmail@example.com>'
            )->isValid()
        );
        $this->assertFalse($rule->validate('John Smith <example.com>')->isValid());
        $this->assertFalse(
            $rule->validate(
                'Короткое имя <после-преобразования-в-idn-тут-будет-больше-чем-64-символа@пример.com>'
            )->isValid()
        );
        $this->assertFalse(
            $rule->validate(
                'Короткое имя <тест@это-доменное-имя.после-преобразования-в-idn.будет-содержать-больше-254-символов.бла-бла-бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-бла.com>'
            )->isValid()
        );
    }

    public function testValidateMx(): void
    {
        $rule = new Email(checkDNS: true);
        $this->assertTrue($rule->validate('5011@gmail.com')->isValid());

        $rule = new Email();
        $this->assertTrue($rule->validate('test@nonexistingsubdomain.example.com')->isValid());

        $rule = new Email(checkDNS: true);
        $this->assertFalse($rule->validate('test@nonexistingsubdomain.example.com')->isValid());

        $rule = new Email(allowName: true, checkDNS: true);
        $emails = ['ipetrov@gmail.com', 'Ivan Petrov <ipetrov@gmail.com>'];
        foreach ($emails as $email) {
            $this->assertTrue(
                $rule->validate($email)->isValid(),
                "Email: '$email' failed to validate(checkDNS=true, allowName=true)"
            );
        }
    }

    public function malformedAddressesProvider(): array
    {
        return [
            // this is the demo email used in the proof of concept of the exploit
            ['"attacker\" -oQ/tmp/ -X/var/www/cache/phpcode.php "@email.com'],
            // trying more adresses
            ['"Attacker -Param2 -Param3"@test.com'],
            ['\'Attacker -Param2 -Param3\'@test.com'],
            ['"Attacker \" -Param2 -Param3"@test.com'],
            ["'Attacker \\' -Param2 -Param3'@test.com"],
            ['"attacker\" -oQ/tmp/ -X/var/www/cache/phpcode.php "@email.com'],
            // and even more variants
            ['"attacker\"\ -oQ/tmp/\ -X/var/www/cache/phpcode.php"@email.com'],
            ["\"attacker\\\"\0-oQ/tmp/\0-X/var/www/cache/phpcode.php\"@email.com"],
            ['"attacker@cebe.cc\"-Xbeep"@email.com'],

            ["'attacker\\' -oQ/tmp/ -X/var/www/cache/phpcode.php'@email.com"],
            ["'attacker\\\\' -oQ/tmp/ -X/var/www/cache/phpcode.php'@email.com"],
            ["'attacker\\\\'\\ -oQ/tmp/ -X/var/www/cache/phpcode.php'@email.com"],
            ["'attacker\\';touch /tmp/hackme'@email.com"],
            ["'attacker\\\\';touch /tmp/hackme'@email.com"],
            ["'attacker\\';touch/tmp/hackme'@email.com"],
            ["'attacker\\\\';touch/tmp/hackme'@email.com"],
            ['"attacker\" -oQ/tmp/ -X/var/www/cache/phpcode.php "@email.com'],
        ];
    }

    /**
     * Test malicious email addresses that can be used to exploit SwiftMailer vulnerability CVE-2016-10074 while IDN is
     * disabled.
     *
     * @see https://legalhackers.com/advisories/SwiftMailer-Exploit-Remote-Code-Exec-CVE-2016-10074-Vuln.html
     * @dataProvider malformedAddressesProvider
     *
     * @param string $value
     */
    public function testMalformedAddressesIdnDisabled($value): void
    {
        $rule = new Email(enableIDN: true);
        $this->assertFalse($rule->validate($value)->isValid());
    }

    /**
     * Test malicious email addresses that can be used to exploit SwiftMailer vulnerability CVE-2016-10074 while IDN is
     * enabled.
     *
     * @see https://legalhackers.com/advisories/SwiftMailer-Exploit-Remote-Code-Exec-CVE-2016-10074-Vuln.html
     * @dataProvider malformedAddressesProvider
     *
     * @param string $value
     */
    public function testMalformedAddressesIdnEnabled($value): void
    {
        if (!function_exists('idn_to_ascii')) {
            $this->markTestSkipped('Intl extension required');
        }

        $rule = new Email(enableIDN: true);
        $this->assertFalse($rule->validate($value)->isValid());
    }

    public function testName(): void
    {
        $this->assertEquals('email', (new Email())->getName());
    }

    public function optionsProvider(): array
    {
        return [
            [
                new Email(),
                [
                    'allowName' => false,
                    'checkDNS' => false,
                    'enableIDN' => false,
                    'message' => 'This value is not a valid email address.',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Email(allowName: true),
                [
                    'allowName' => true,
                    'checkDNS' => false,
                    'enableIDN' => false,
                    'message' => 'This value is not a valid email address.',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Email(allowName: true, checkDNS: true),
                [
                    'allowName' => true,
                    'checkDNS' => true,
                    'enableIDN' => false,
                    'message' => 'This value is not a valid email address.',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Email(allowName: true, enableIDN: true),
                [
                    'allowName' => true,
                    'checkDNS' => false,
                    'enableIDN' => true,
                    'message' => 'This value is not a valid email address.',
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
