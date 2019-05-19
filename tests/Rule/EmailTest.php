<?php

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Email;

/**
 * @group validators
 */
class EmailTest extends TestCase
{
    public function testValidateValue()
    {
        $validator = new Email();

        $this->assertTrue($validator->validateValue('sam@rmcreative.ru')->isValid());
        $this->assertTrue($validator->validateValue('5011@gmail.com')->isValid());
        $this->assertTrue($validator->validateValue('Abc.123@example.com')->isValid());
        $this->assertTrue($validator->validateValue('user+mailbox/department=shipping@example.com')->isValid());
        $this->assertTrue($validator->validateValue('!#$%&\'*+-/=?^_`.{|}~@example.com')->isValid());
        $this->assertFalse($validator->validateValue('rmcreative.ru')->isValid());
        $this->assertFalse($validator->validateValue('Carsten Brandt <mail@cebe.cc>')->isValid());
        $this->assertFalse($validator->validateValue('"Carsten Brandt" <mail@cebe.cc>')->isValid());
        $this->assertFalse($validator->validateValue('<mail@cebe.cc>')->isValid());
        $this->assertFalse($validator->validateValue('info@örtliches.de')->isValid());
        $this->assertFalse($validator->validateValue('sam@рмкреатиф.ru')->isValid());
        $this->assertFalse($validator->validateValue('ex..ample@example.com')->isValid());
        $this->assertFalse($validator->validateValue(['developer@yiiframework.com'])->isValid());

        $validator->allowName(true);

        $this->assertTrue($validator->validateValue('sam@rmcreative.ru')->isValid());
        $this->assertTrue($validator->validateValue('5011@gmail.com')->isValid());
        $this->assertFalse($validator->validateValue('rmcreative.ru')->isValid());
        $this->assertTrue($validator->validateValue('Carsten Brandt <mail@cebe.cc>')->isValid());
        $this->assertTrue($validator->validateValue('"Carsten Brandt" <mail@cebe.cc>')->isValid());
        $this->assertTrue($validator->validateValue('<mail@cebe.cc>')->isValid());
        $this->assertFalse($validator->validateValue('info@örtliches.de')->isValid());
        $this->assertFalse($validator->validateValue('üñîçøðé@üñîçøðé.com')->isValid());
        $this->assertFalse($validator->validateValue('sam@рмкреатиф.ru')->isValid());
        $this->assertFalse($validator->validateValue('Informtation info@oertliches.de')->isValid());
        $this->assertTrue($validator->validateValue('test@example.com')->isValid());
        $this->assertTrue($validator->validateValue('John Smith <john.smith@example.com>')->isValid());
        $this->assertTrue($validator->validateValue('"This name is longer than 64 characters. Blah blah blah blah blah" <shortmail@example.com>')->isValid());
        $this->assertFalse($validator->validateValue('John Smith <example.com>')->isValid());
        $this->assertFalse($validator->validateValue('Short Name <localPartMoreThan64Characters-blah-blah-blah-blah-blah-blah-blah-blah@example.com>')->isValid());
        $this->assertFalse($validator->validateValue('Short Name <domainNameIsMoreThan254Characters@example-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah.com>')->isValid());
        $this->assertFalse($validator->validateValue(['developer@yiiframework.com'])->isValid());
    }

    public function testValidateValueIdn()
    {
        if (!function_exists('idn_to_ascii')) {
            $this->markTestSkipped('Intl extension required');

            return;
        }
        $validator = new Email();
        $validator->enableIDN(true);

        $this->assertTrue($validator->validateValue('5011@example.com')->isValid());
        $this->assertTrue($validator->validateValue('example@äüößìà.de')->isValid());
        $this->assertTrue($validator->validateValue('example@xn--zcack7ayc9a.de')->isValid());
        $this->assertTrue($validator->validateValue('info@örtliches.de')->isValid());
        $this->assertTrue($validator->validateValue('sam@рмкреатиф.ru')->isValid());
        $this->assertTrue($validator->validateValue('sam@rmcreative.ru')->isValid());
        $this->assertTrue($validator->validateValue('5011@gmail.com')->isValid());
        $this->assertTrue($validator->validateValue('üñîçøðé@üñîçøðé.com')->isValid());
        $this->assertFalse($validator->validateValue('rmcreative.ru')->isValid());
        $this->assertFalse($validator->validateValue('Carsten Brandt <mail@cebe.cc>')->isValid());
        $this->assertFalse($validator->validateValue('"Carsten Brandt" <mail@cebe.cc>')->isValid());
        $this->assertFalse($validator->validateValue('<mail@cebe.cc>')->isValid());

        $validator->allowName(true);

        $this->assertTrue($validator->validateValue('info@örtliches.de')->isValid());
        $this->assertTrue($validator->validateValue('Informtation <info@örtliches.de>')->isValid());
        $this->assertFalse($validator->validateValue('Informtation info@örtliches.de')->isValid());
        $this->assertTrue($validator->validateValue('sam@рмкреатиф.ru')->isValid());
        $this->assertTrue($validator->validateValue('sam@rmcreative.ru')->isValid());
        $this->assertTrue($validator->validateValue('5011@gmail.com')->isValid());
        $this->assertFalse($validator->validateValue('rmcreative.ru')->isValid());
        $this->assertTrue($validator->validateValue('Carsten Brandt <mail@cebe.cc>')->isValid());
        $this->assertTrue($validator->validateValue('"Carsten Brandt" <mail@cebe.cc>')->isValid());
        $this->assertTrue($validator->validateValue('üñîçøðé 日本国 <üñîçøðé@üñîçøðé.com>')->isValid());
        $this->assertTrue($validator->validateValue('<mail@cebe.cc>')->isValid());
        $this->assertTrue($validator->validateValue('test@example.com')->isValid());
        $this->assertTrue($validator->validateValue('John Smith <john.smith@example.com>')->isValid());
        $this->assertTrue($validator->validateValue('"Такое имя достаточно длинное, но оно все равно может пройти валидацию" <shortmail@example.com>')->isValid());
        $this->assertFalse($validator->validateValue('John Smith <example.com>')->isValid());
        $this->assertFalse($validator->validateValue('Короткое имя <после-преобразования-в-idn-тут-будет-больше-чем-64-символа@пример.com>')->isValid());
        $this->assertFalse($validator->validateValue('Короткое имя <тест@это-доменное-имя.после-преобразования-в-idn.будет-содержать-больше-254-символов.бла-бла-бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-бла.com>')->isValid());
    }

    public function testValidateValueMx()
    {
        $validator = new Email();

        $validator->checkDNS(true);
        $this->assertTrue($validator->validateValue('5011@gmail.com')->isValid());

        $validator->checkDNS(false);
        $this->assertTrue($validator->validateValue('test@nonexistingsubdomain.example.com')->isValid());
        $validator->checkDNS(true);
        $this->assertFalse($validator->validateValue('test@nonexistingsubdomain.example.com')->isValid());

        $validator->checkDns(true);
        $validator->allowName(true);
        $emails = [
            'ipetrov@gmail.com',
            'Ivan Petrov <ipetrov@gmail.com>',
        ];
        foreach ($emails as $email) {
            $this->assertTrue($validator->validateValue($email)->isValid(),
                "Email: '$email' failed to validateValue(checkDNS=true, allowName=true)");
        }
    }

    public function testValidateAttribute()
    {
        $validator = new Email();
        $model = new FakedValidationModel();
        $model->attr_email = '5011@gmail.com';
        $validator->validateAttribute($model, 'attr_email');
        $this->assertFalse($model->hasErrors('attr_email'));
    }

    public function malformedAddressesProvider()
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
    public function testMalformedAddressesIdnDisabled($value)
    {
        $validator = new Email();
        $validator->enableIDN(true);
        $this->assertFalse($validator->validateValue($value)->isValid());
    }

    /**
     * Test malicious email addresses that can be used to exploit SwiftMailer vulnerability CVE-2016-10074 while IDN is
     * enabled.
     * @see https://legalhackers.com/advisories/SwiftMailer-Exploit-Remote-Code-Exec-CVE-2016-10074-Vuln.html
     * @dataProvider malformedAddressesProvider
     *
     * @param string $value
     */
    public function testMalformedAddressesIdnEnabled($value)
    {
        if (!function_exists('idn_to_ascii')) {
            $this->markTestSkipped('Intl extension required');

            return;
        }

        $val = new Email();
        $val->enableIDN(true);
        $this->assertFalse($val->validateValue($value)->isValid());
    }
}
