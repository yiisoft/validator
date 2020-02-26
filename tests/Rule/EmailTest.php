<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Email;

/**
 * @group validators
 */
class EmailTest extends TestCase
{
    public function testValidate(): void
    {
        $validator = new Email();

        $this->assertTrue($validator->validate('sam@rmcreative.ru')->isValid());
        $this->assertTrue($validator->validate('5011@gmail.com')->isValid());
        $this->assertTrue($validator->validate('Abc.123@example.com')->isValid());
        $this->assertTrue($validator->validate('user+mailbox/department=shipping@example.com')->isValid());
        $this->assertTrue($validator->validate('!#$%&\'*+-/=?^_`.{|}~@example.com')->isValid());
        $this->assertFalse($validator->validate('rmcreative.ru')->isValid());
        $this->assertFalse($validator->validate('Carsten Brandt <mail@cebe.cc>')->isValid());
        $this->assertFalse($validator->validate('"Carsten Brandt" <mail@cebe.cc>')->isValid());
        $this->assertFalse($validator->validate('<mail@cebe.cc>')->isValid());
        $this->assertFalse($validator->validate('info@örtliches.de')->isValid());
        $this->assertFalse($validator->validate('sam@рмкреатиф.ru')->isValid());
        $this->assertFalse($validator->validate('ex..ample@example.com')->isValid());
        $this->assertFalse($validator->validate(['developer@yiiframework.com'])->isValid());

        $validator->allowName(true);

        $this->assertTrue($validator->validate('sam@rmcreative.ru')->isValid());
        $this->assertTrue($validator->validate('5011@gmail.com')->isValid());
        $this->assertFalse($validator->validate('rmcreative.ru')->isValid());
        $this->assertTrue($validator->validate('Carsten Brandt <mail@cebe.cc>')->isValid());
        $this->assertTrue($validator->validate('"Carsten Brandt" <mail@cebe.cc>')->isValid());
        $this->assertTrue($validator->validate('<mail@cebe.cc>')->isValid());
        $this->assertFalse($validator->validate('info@örtliches.de')->isValid());
        $this->assertFalse($validator->validate('üñîçøðé@üñîçøðé.com')->isValid());
        $this->assertFalse($validator->validate('sam@рмкреатиф.ru')->isValid());
        $this->assertFalse($validator->validate('Informtation info@oertliches.de')->isValid());
        $this->assertTrue($validator->validate('test@example.com')->isValid());
        $this->assertTrue($validator->validate('John Smith <john.smith@example.com>')->isValid());
        $this->assertTrue(
            $validator->validate(
                '"This name is longer than 64 characters. Blah blah blah blah blah" <shortmail@example.com>'
            )->isValid()
        );
        $this->assertFalse($validator->validate('John Smith <example.com>')->isValid());
        $this->assertFalse(
            $validator->validate(
                'Short Name <localPartMoreThan64Characters-blah-blah-blah-blah-blah-blah-blah-blah@example.com>'
            )->isValid()
        );
        $this->assertFalse(
            $validator->validate(
                'Short Name <domainNameIsMoreThan254Characters@example-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah.com>'
            )->isValid()
        );
        $this->assertFalse($validator->validate(['developer@yiiframework.com'])->isValid());
    }

    public function testValidateIdn(): void
    {
        if (!function_exists('idn_to_ascii')) {
            $this->markTestSkipped('Intl extension required');

            return;
        }
        $validator = new Email();
        $validator->enableIDN(true);

        $this->assertTrue($validator->validate('5011@example.com')->isValid());
        $this->assertTrue($validator->validate('example@äüößìà.de')->isValid());
        $this->assertTrue($validator->validate('example@xn--zcack7ayc9a.de')->isValid());
        $this->assertTrue($validator->validate('info@örtliches.de')->isValid());
        $this->assertTrue($validator->validate('sam@рмкреатиф.ru')->isValid());
        $this->assertTrue($validator->validate('sam@rmcreative.ru')->isValid());
        $this->assertTrue($validator->validate('5011@gmail.com')->isValid());
        $this->assertTrue($validator->validate('üñîçøðé@üñîçøðé.com')->isValid());
        $this->assertFalse($validator->validate('rmcreative.ru')->isValid());
        $this->assertFalse($validator->validate('Carsten Brandt <mail@cebe.cc>')->isValid());
        $this->assertFalse($validator->validate('"Carsten Brandt" <mail@cebe.cc>')->isValid());
        $this->assertFalse($validator->validate('<mail@cebe.cc>')->isValid());

        $validator->allowName(true);

        $this->assertTrue($validator->validate('info@örtliches.de')->isValid());
        $this->assertTrue($validator->validate('Informtation <info@örtliches.de>')->isValid());
        $this->assertFalse($validator->validate('Informtation info@örtliches.de')->isValid());
        $this->assertTrue($validator->validate('sam@рмкреатиф.ru')->isValid());
        $this->assertTrue($validator->validate('sam@rmcreative.ru')->isValid());
        $this->assertTrue($validator->validate('5011@gmail.com')->isValid());
        $this->assertFalse($validator->validate('rmcreative.ru')->isValid());
        $this->assertTrue($validator->validate('Carsten Brandt <mail@cebe.cc>')->isValid());
        $this->assertTrue($validator->validate('"Carsten Brandt" <mail@cebe.cc>')->isValid());
        $this->assertTrue($validator->validate('üñîçøðé 日本国 <üñîçøðé@üñîçøðé.com>')->isValid());
        $this->assertTrue($validator->validate('<mail@cebe.cc>')->isValid());
        $this->assertTrue($validator->validate('test@example.com')->isValid());
        $this->assertTrue($validator->validate('John Smith <john.smith@example.com>')->isValid());
        $this->assertTrue(
            $validator->validate(
                '"Такое имя достаточно длинное, но оно все равно может пройти валидацию" <shortmail@example.com>'
            )->isValid()
        );
        $this->assertFalse($validator->validate('John Smith <example.com>')->isValid());
        $this->assertFalse(
            $validator->validate(
                'Короткое имя <после-преобразования-в-idn-тут-будет-больше-чем-64-символа@пример.com>'
            )->isValid()
        );
        $this->assertFalse(
            $validator->validate(
                'Короткое имя <тест@это-доменное-имя.после-преобразования-в-idn.будет-содержать-больше-254-символов.бла-бла-бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-бла.com>'
            )->isValid()
        );
    }

    public function testValidateMx(): void
    {
        $this->markTestSkipped('Too slow :(');

        $validator = new Email();

        $validator->checkDNS(true);
        $this->assertTrue($validator->validate('5011@gmail.com')->isValid());

        $validator->checkDNS(false);
        $this->assertTrue($validator->validate('test@nonexistingsubdomain.example.com')->isValid());
        $validator->checkDNS(true);
        $this->assertFalse($validator->validate('test@nonexistingsubdomain.example.com')->isValid());

        $validator->checkDns(true);
        $validator->allowName(true);
        $emails = [
            'ipetrov@gmail.com',
            'Ivan Petrov <ipetrov@gmail.com>',
        ];
        foreach ($emails as $email) {
            $this->assertTrue(
                $validator->validate($email)->isValid(),
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
     * @see https://legalhackers.com/advisories/SwiftMailer-Exploit-Remote-Code-Exec-CVE-2016-10074-Vuln.html
     * @dataProvider malformedAddressesProvider
     *
     * @param string $value
     */
    public function testMalformedAddressesIdnDisabled($value): void
    {
        $validator = new Email();
        $validator->enableIDN(true);
        $this->assertFalse($validator->validate($value)->isValid());
    }

    /**
     * Test malicious email addresses that can be used to exploit SwiftMailer vulnerability CVE-2016-10074 while IDN is
     * enabled.
     * @see https://legalhackers.com/advisories/SwiftMailer-Exploit-Remote-Code-Exec-CVE-2016-10074-Vuln.html
     * @dataProvider malformedAddressesProvider
     *
     * @param string $value
     */
    public function testMalformedAddressesIdnEnabled($value): void
    {
        if (!function_exists('idn_to_ascii')) {
            $this->markTestSkipped('Intl extension required');

            return;
        }

        $val = new Email();
        $val->enableIDN(true);
        $this->assertFalse($val->validate($value)->isValid());
    }
}
