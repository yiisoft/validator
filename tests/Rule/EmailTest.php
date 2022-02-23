<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\Email;

/**
 * @group validators
 */
class EmailTest extends TestCase
{
    public function testValidate(): void
    {
        $rule = new Email();

        $this->assertTrue($rule->validate('sam@rmcreative.ru')->isValid());
        $this->assertTrue($rule->validate('5011@gmail.com')->isValid());
        $this->assertTrue($rule->validate('Abc.123@example.com')->isValid());
        $this->assertTrue($rule->validate('user+mailbox/department=shipping@example.com')->isValid());
        $this->assertTrue($rule->validate('!#$%&\'*+-/=?^_`.{|}~@example.com')->isValid());
        $this->assertFalse($rule->validate('rmcreative.ru')->isValid());
        $this->assertFalse($rule->validate('Carsten Brandt <mail@cebe.cc>')->isValid());
        $this->assertFalse($rule->validate('"Carsten Brandt" <mail@cebe.cc>')->isValid());
        $this->assertFalse($rule->validate('<mail@cebe.cc>')->isValid());
        $this->assertFalse($rule->validate('info@örtliches.de')->isValid());
        $this->assertFalse($rule->validate('sam@рмкреатиф.ru')->isValid());
        $this->assertFalse($rule->validate('ex..ample@example.com')->isValid());
        $this->assertFalse($rule->validate(['developer@yiiframework.com'])->isValid());

        $rule = new Email(allowName: true);

        $this->assertTrue($rule->validate('sam@rmcreative.ru')->isValid());
        $this->assertTrue($rule->validate('5011@gmail.com')->isValid());
        $this->assertFalse($rule->validate('rmcreative.ru')->isValid());
        $this->assertTrue($rule->validate('Carsten Brandt <mail@cebe.cc>')->isValid());
        $this->assertTrue($rule->validate('"Carsten Brandt" <mail@cebe.cc>')->isValid());
        $this->assertTrue($rule->validate('<mail@cebe.cc>')->isValid());
        $this->assertFalse($rule->validate('info@örtliches.de')->isValid());
        $this->assertFalse($rule->validate('üñîçøðé@üñîçøðé.com')->isValid());
        $this->assertFalse($rule->validate('sam@рмкреатиф.ru')->isValid());
        $this->assertFalse($rule->validate('Informtation info@oertliches.de')->isValid());
        $this->assertTrue($rule->validate('test@example.com')->isValid());
        $this->assertTrue($rule->validate('John Smith <john.smith@example.com>')->isValid());
        $this->assertTrue(
            $rule->validate(
                '"This name is longer than 64 characters. Blah blah blah blah blah" <shortmail@example.com>'
            )->isValid()
        );
        $this->assertFalse($rule->validate('John Smith <example.com>')->isValid());
        $this->assertFalse(
            $rule->validate(
                'Short Name <localPartMoreThan64Characters-blah-blah-blah-blah-blah-blah-blah-blah@example.com>'
            )->isValid()
        );
        $this->assertFalse(
            $rule->validate(
                'Short Name <domainNameIsMoreThan254Characters@example-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah.com>'
            )->isValid()
        );
        $this->assertFalse($rule->validate(['developer@yiiframework.com'])->isValid());
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
