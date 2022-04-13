<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Email;
use function function_exists;

class EmailTest extends TestCase
{
    public function validateWithDefaultArgumentsProvider(): array
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

            ['test@nonexistingsubdomain.example.com', true], // checkDNS is disabled

            // Malicious email addresses that can be used to exploit SwiftMailer vulnerability CVE-2016-10074 while IDN
            // is disabled.
            // https://legalhackers.com/advisories/SwiftMailer-Exploit-Remote-Code-Exec-CVE-2016-10074-Vuln.html

            // This is the demo email used in the proof of concept of the exploit
            ['"attacker\" -oQ/tmp/ -X/var/www/cache/phpcode.php "@email.com', false],
            // Trying more addresses
            ['"Attacker -Param2 -Param3"@test.com', false],
            ['\'Attacker -Param2 -Param3\'@test.com', false],
            ['"Attacker \" -Param2 -Param3"@test.com', false],
            ["'Attacker \\' -Param2 -Param3'@test.com", false],
            ['"attacker\" -oQ/tmp/ -X/var/www/cache/phpcode.php "@email.com', false],
            // And even more variants
            ['"attacker\"\ -oQ/tmp/\ -X/var/www/cache/phpcode.php"@email.com', false],
            ["\"attacker\\\"\0-oQ/tmp/\0-X/var/www/cache/phpcode.php\"@email.com", false],
            ['"attacker@cebe.cc\"-Xbeep"@email.com', false],

            ["'attacker\\' -oQ/tmp/ -X/var/www/cache/phpcode.php'@email.com", false],
            ["'attacker\\\\' -oQ/tmp/ -X/var/www/cache/phpcode.php'@email.com", false],
            ["'attacker\\\\'\\ -oQ/tmp/ -X/var/www/cache/phpcode.php'@email.com", false],
            ["'attacker\\';touch /tmp/hackme'@email.com", false],
            ["'attacker\\\\';touch /tmp/hackme'@email.com", false],
            ["'attacker\\';touch/tmp/hackme'@email.com", false],
            ["'attacker\\\\';touch/tmp/hackme'@email.com", false],
            ['"attacker\" -oQ/tmp/ -X/var/www/cache/phpcode.php "@email.com', false],
        ];
    }

    /**
     * @dataProvider validateWithDefaultArgumentsProvider
     */
    public function testValidateWithDefaultArguments(mixed $value, bool $expectedIsValid): void
    {
        $rule = new Email();
        $result = $rule->validate($value);

        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function validateWithAllowNameProvider(): array
    {
        return [
            ['sam@rmcreative.ru', true],
            ['5011@gmail.com', true],
            ['rmcreative.ru', false],
            ['Carsten Brandt <mail@cebe.cc>', true],
            ['"Carsten Brandt" <mail@cebe.cc>', true],
            ['<mail@cebe.cc>', true],
            ['info@örtliches.de', false],
            ['üñîçøðé@üñîçøðé.com', false],
            ['sam@рмкреатиф.ru', false],
            ['Informtation info@oertliches.de', false],
            ['test@example.com', true],
            ['John Smith <john.smith@example.com>', true],
            ['"This name is longer than 64 characters. Blah blah blah blah blah" <shortmail@example.com>', true],
            ['John Smith <example.com>', false],
            ['Short Name <localPartMoreThan64Characters-blah-blah-blah-blah-blah-blah-blah-blah@example.com>', false],
            [
                'Short Name <domainNameIsMoreThan254Characters@example-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah.com>',
                false,
            ],
            [['developer@yiiframework.com'], false],
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

    public function validateWithEnableIdnProvider(): array
    {
        return [
            ['5011@example.com', true],
            ['test-@dummy.com', true],
            ['example@äüößìà.de', true],
            ['example@xn--zcack7ayc9a.de', true],
            ['info@örtliches.de', true],
            ['sam@рмкреатиф.ru', true],
            ['sam@rmcreative.ru', true],
            ['5011@gmail.com', true],
            ['üñîçøðé@üñîçøðé.com', true],
            ['rmcreative.ru', false],
            ['Carsten Brandt <mail@cebe.cc>', false],
            ['"Carsten Brandt" <mail@cebe.cc>', false],
            ['<mail@cebe.cc>', false],
        ];
    }

    /**
     * @dataProvider validateWithEnableIdnProvider
     */
    public function testValidateWithEnableIdn(string $value, bool $expectedIsValid): void
    {
        if (!function_exists('idn_to_ascii')) {
            $this->markTestSkipped('Intl extension required');
        }

        $rule = new Email(enableIDN: true);
        $result = $rule->validate($value);

        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function validateWithEnableIdnAndAllowNameProvider(): array
    {
        return [
            ['info@örtliches.de', true],
            ['Information <info@örtliches.de>', true],
            ['Information info@örtliches.de', false],
            ['sam@рмкреатиф.ru', true],
            ['sam@rmcreative.ru', true],
            ['5011@gmail.com', true],
            ['rmcreative.ru', false],
            ['Carsten Brandt <mail@cebe.cc>', true],
            ['"Carsten Brandt" <mail@cebe.cc>', true],
            ['üñîçøðé 日本国 <üñîçøðé@üñîçøðé.com>', true],
            ['<mail@cebe.cc>', true],
            ['test@example.com', true],
            ['John Smith <john.smith@example.com>', true],
            ['"Такое имя достаточно длинное, но оно все равно может пройти валидацию" <shortmail@example.com>', true],
            ['John Smith <example.com>', false],
            ['Короткое имя <после-преобразования-в-idn-тут-будет-больше-чем-64-символа@пример.com>', false],
            [
                'Короткое имя <тест@это-доменное-имя.после-преобразования-в-idn.будет-содержать-больше-254-символов.бла-бла-бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-бла.com>',
                false,
            ],
        ];
    }

    /**
     * @dataProvider validateWithEnableIdnAndAllowNameProvider
     */
    public function testValidateWithEnableIdnAndAllowName(string $value, bool $expectedIsValid): void
    {
        $rule = new Email(allowName: true, enableIDN: true);

        $result = $rule->validate($value);
        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function validateWithCheckDNSProvider(): array
    {
        return [
            ['5011@gmail.com', true],
            ['test@nonexistingsubdomain.example.com', false],
        ];
    }

    /**
     * @dataProvider validateWithCheckDNSProvider
     */
    public function testValidateWithCheckDNS(string $value, bool $expectedIsValid): void
    {
        $rule = new Email(checkDNS: true);
        $result = $rule->validate($value);

        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function validateWithCheckDNSAndAllowNameProvider(): array
    {
        return [
            ['ipetrov@gmail.com', true],
            ['Ivan Petrov <ipetrov@gmail.com>', true],
        ];
    }

    /**
     * @dataProvider validateWithCheckDNSAndAllowNameProvider
     */
    public function testValidateWithCheckDNSAndAllowName(string $value, bool $expectedIsValid): void
    {
        $rule = new Email(allowName: true, checkDNS: true);
        $result = $rule->validate($value);

        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function testGetName(): void
    {
        $this->assertEquals('email', (new Email())->getName());
    }

    public function getOptionsProvider(): array
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
                    'pattern' => "/^[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/",
                    'fullPattern' => "/^[^@]*<[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?>$/",
                    'idnEmailPattern' => '/^([a-zA-Z0-9._%+-]+)@((\[\d{1,3}\.\d{1,3}\.\d{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|\d{1,3})(\]?)$/',
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
                    'pattern' => "/^[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/",
                    'fullPattern' => "/^[^@]*<[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?>$/",
                    'idnEmailPattern' => '/^([a-zA-Z0-9._%+-]+)@((\[\d{1,3}\.\d{1,3}\.\d{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|\d{1,3})(\]?)$/',
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
                    'pattern' => "/^[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/",
                    'fullPattern' => "/^[^@]*<[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?>$/",
                    'idnEmailPattern' => '/^([a-zA-Z0-9._%+-]+)@((\[\d{1,3}\.\d{1,3}\.\d{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|\d{1,3})(\]?)$/',
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
                    'pattern' => "/^[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/",
                    'fullPattern' => "/^[^@]*<[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?>$/",
                    'idnEmailPattern' => '/^([a-zA-Z0-9._%+-]+)@((\[\d{1,3}\.\d{1,3}\.\d{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|\d{1,3})(\]?)$/',
                ],
            ],
        ];
    }

    /**
     * @dataProvider getOptionsProvider
     */
    public function testGetOptions(Email $rule, array $expectedOptions): void
    {
        $this->assertEquals($expectedOptions, $rule->getOptions());
    }
}
