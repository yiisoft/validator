<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Tests\Stub\FakeValidatorFactory;
use Yiisoft\Validator\Validator;

final class EmailTest extends TestCase
{
    public function testGetName(): void
    {
        $rule = new Email();
        $this->assertSame('email', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new Email(),
                [
                    'pattern' => '/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/',
                    'fullPattern' => '/^[^@]*<[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?>$/',
                    'idnEmailPattern' => '/^([a-zA-Z0-9._%+-]+)@((\[\d{1,3}\.\d{1,3}\.\d{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|\d{1,3})(\]?)$/',
                    'allowName' => false,
                    'checkDNS' => false,
                    'enableIDN' => false,
                    'message' => [
                        'message' => 'This value is not a valid email address.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Email(allowName: true),
                [
                    'pattern' => '/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/',
                    'fullPattern' => '/^[^@]*<[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?>$/',
                    'idnEmailPattern' => '/^([a-zA-Z0-9._%+-]+)@((\[\d{1,3}\.\d{1,3}\.\d{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|\d{1,3})(\]?)$/',
                    'allowName' => true,
                    'checkDNS' => false,
                    'enableIDN' => false,
                    'message' => [
                        'message' => 'This value is not a valid email address.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Email(allowName: true, checkDNS: true),
                [
                    'pattern' => '/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/',
                    'fullPattern' => '/^[^@]*<[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?>$/',
                    'idnEmailPattern' => '/^([a-zA-Z0-9._%+-]+)@((\[\d{1,3}\.\d{1,3}\.\d{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|\d{1,3})(\]?)$/',
                    'allowName' => true,
                    'checkDNS' => true,
                    'enableIDN' => false,
                    'message' => [
                        'message' => 'This value is not a valid email address.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Email(allowName: true, enableIDN: true),
                [
                    'pattern' => '/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/',
                    'fullPattern' => '/^[^@]*<[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?>$/',
                    'idnEmailPattern' => '/^([a-zA-Z0-9._%+-]+)@((\[\d{1,3}\.\d{1,3}\.\d{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|\d{1,3})(\]?)$/',
                    'allowName' => true,
                    'checkDNS' => false,
                    'enableIDN' => true,
                    'message' => [
                        'message' => 'This value is not a valid email address.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    /**
     * @requires extension intl
     * @dataProvider dataOptions
     */
    public function testOptions(Email $rule, array $expectedOptions): void
    {
        $options = $rule->getOptions();
        $this->assertSame($expectedOptions, $options);
    }

    public function dataValidationPassed(): array
    {
        if (!extension_loaded('intl')) {
            return [];
        }

        $rule = new Email();
        $ruleAllowedName = new Email(allowName: true);
        $ruleEnabledIDN = new Email(enableIDN: true);
        $ruleEnabledIDNandAllowedName = new Email(allowName: true, enableIDN: true);

        return [
            ['sam@rmcreative.ru', [$rule]],
            ['5011@gmail.com', [$rule]],
            ['Abc.123@example.com', [$rule]],
            ['user+mailbox/department=shipping@example.com', [$rule]],
            ['!#$%&\'*+-/=?^_`.{|}~@example.com', [$rule]],
            ['test@nonexistingsubdomain.example.com', [$rule]], // checkDNS is disabled

            ['sam@rmcreative.ru', [$ruleAllowedName]],
            ['5011@gmail.com', [$ruleAllowedName]],
            ['Carsten Brandt <mail@cebe.cc>', [$ruleAllowedName]],
            ['"Carsten Brandt" <mail@cebe.cc>', [$ruleAllowedName]],
            ['<mail@cebe.cc>', [$ruleAllowedName]],
            ['test@example.com', [$ruleAllowedName]],
            ['John Smith <john.smith@example.com>', [$ruleAllowedName]],
            [
                '"This name is longer than 64 characters. Blah blah blah blah blah" <shortmail@example.com>',
                [$ruleAllowedName],
            ],

            ['5011@example.com', [$ruleEnabledIDN]],
            ['test-@dummy.com', [$ruleEnabledIDN]],
            ['example@äüößìà.de', [$ruleEnabledIDN]],
            ['example@xn--zcack7ayc9a.de', [$ruleEnabledIDN]],
            ['info@örtliches.de', [$ruleEnabledIDN]],
            ['sam@рмкреатиф.ru', [$ruleEnabledIDN]],
            ['sam@rmcreative.ru', [$ruleEnabledIDN]],
            ['5011@gmail.com', [$ruleEnabledIDN]],
            ['üñîçøðé@üñîçøðé.com', [$ruleEnabledIDN]],

            ['info@örtliches.de', [$ruleEnabledIDNandAllowedName]],
            ['Information <info@örtliches.de>', [$ruleEnabledIDNandAllowedName]],
            ['sam@рмкреатиф.ru', [$ruleEnabledIDNandAllowedName]],
            ['sam@rmcreative.ru', [$ruleEnabledIDNandAllowedName]],
            ['5011@gmail.com', [$ruleEnabledIDNandAllowedName]],
            ['Carsten Brandt <mail@cebe.cc>', [$ruleEnabledIDNandAllowedName]],
            ['"Carsten Brandt" <mail@cebe.cc>', [$ruleEnabledIDNandAllowedName]],
            ['üñîçøðé 日本国 <üñîçøðé@üñîçøðé.com>', [$ruleEnabledIDNandAllowedName]],
            ['<mail@cebe.cc>', [$ruleEnabledIDNandAllowedName]],
            ['test@example.com', [$ruleEnabledIDNandAllowedName]],
            ['John Smith <john.smith@example.com>', [$ruleEnabledIDNandAllowedName]],
            [
                '"Такое имя достаточно длинное, но оно все равно может пройти валидацию" <shortmail@example.com>',
                [$ruleEnabledIDNandAllowedName],
            ],

            ['5011@gmail.com', [new Email(checkDNS: true)]],

            ['ipetrov@gmail.com', [new Email(allowName: true, checkDNS: true)]],
            ['Ivan Petrov <ipetrov@gmail.com>', [new Email(allowName: true, checkDNS: true)]],
        ];
    }

    /**
     * @dataProvider dataValidationPassed
     */
    public function testValidationPassed(mixed $data, array $rules): void
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('The intl extension must be available for this test.');
        }

        $result = $this->createValidator()->validate($data, $rules);

        $this->assertTrue($result->isValid());
    }

    public function dataValidationFailed(): array
    {
        if (!extension_loaded('intl')) {
            return [];
        }

        $rule = new Email();
        $ruleAllowedName = new Email(allowName: true);
        $ruleEnabledIDN = new Email(enableIDN: true);
        $ruleEnabledIDNandAllowedName = new Email(allowName: true, enableIDN: true);
        $errors = ['' => ['This value is not a valid email address.']];

        return [
            ['rmcreative.ru', [$rule], $errors],
            ['Carsten Brandt <mail@cebe.cc>', [$rule], $errors],
            ['"Carsten Brandt" <mail@cebe.cc>', [$rule], $errors],
            ['<mail@cebe.cc>', [$rule], $errors],
            ['info@örtliches.de', [$rule], $errors],
            ['sam@рмкреатиф.ru', [$rule], $errors],
            ['ex..ample@example.com', [$rule], $errors],
            [['developer@yiiframework.com'], [$rule], $errors],

            // Malicious email addresses that can be used to exploit SwiftMailer vulnerability CVE-2016-10074 while IDN
            // is disabled.
            // https://legalhackers.com/advisories/SwiftMailer-Exploit-Remote-Code-Exec-CVE-2016-10074-Vuln.html

            // This is the demo email used in the proof of concept of the exploit
            ['"attacker\" -oQ/tmp/ -X/var/www/cache/phpcode.php "@email.com', [$rule], $errors],

            // Trying more addresses
            ['"Attacker -Param2 -Param3"@test.com', [$rule], $errors],
            ['\'Attacker -Param2 -Param3\'@test.com', [$rule], $errors],
            ['"Attacker \" -Param2 -Param3"@test.com', [$rule], $errors],
            ["'Attacker \\' -Param2 -Param3'@test.com", [$rule], $errors],
            ['"attacker\" -oQ/tmp/ -X/var/www/cache/phpcode.php "@email.com', [$rule], $errors],

            // And even more variants
            ['"attacker\"\ -oQ/tmp/\ -X/var/www/cache/phpcode.php"@email.com', [$rule], $errors],
            ["\"attacker\\\"\0-oQ/tmp/\0-X/var/www/cache/phpcode.php\"@email.com", [$rule], $errors],
            ['"attacker@cebe.cc\"-Xbeep"@email.com', [$rule], $errors],
            ["'attacker\\' -oQ/tmp/ -X/var/www/cache/phpcode.php'@email.com", [$rule], $errors],
            ["'attacker\\\\' -oQ/tmp/ -X/var/www/cache/phpcode.php'@email.com", [$rule], $errors],
            ["'attacker\\\\'\\ -oQ/tmp/ -X/var/www/cache/phpcode.php'@email.com", [$rule], $errors],
            ["'attacker\\';touch /tmp/hackme'@email.com", [$rule], $errors],
            ["'attacker\\\\';touch /tmp/hackme'@email.com", [$rule], $errors],
            ["'attacker\\';touch/tmp/hackme'@email.com", [$rule], $errors],
            ["'attacker\\\\';touch/tmp/hackme'@email.com", [$rule], $errors],
            ['"attacker\" -oQ/tmp/ -X/var/www/cache/phpcode.php "@email.com', [$rule], $errors],

            ['rmcreative.ru', [$ruleAllowedName], $errors],
            ['info@örtliches.de', [$ruleAllowedName], $errors],
            ['üñîçøðé@üñîçøðé.com', [$ruleAllowedName], $errors],
            ['sam@рмкреатиф.ru', [$ruleAllowedName], $errors],
            ['Informtation info@oertliches.de', [$ruleAllowedName], $errors],
            ['John Smith <example.com>', [$ruleAllowedName], $errors],
            [
                'Short Name <localPartMoreThan64Characters-blah-blah-blah-blah-blah-blah-blah-blah@example.com>',
                [$ruleAllowedName],
                $errors,
            ],
            [['developer@yiiframework.com'], [$ruleAllowedName], $errors],
            [
                [
                    'Short Name <domainNameIsMoreThan254Characters@example-blah-blah-blah-blah-blah-blah-blah-blah-' .
                    'blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-' .
                    'blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-' .
                    'blah-blah-blah.com>',
                ],
                [$ruleAllowedName],
                $errors,
            ],

            ['rmcreative.ru', [$ruleEnabledIDN], $errors],
            ['Carsten Brandt <mail@cebe.cc>', [$ruleEnabledIDN], $errors],
            ['"Carsten Brandt" <mail@cebe.cc>', [$ruleEnabledIDN], $errors],
            ['<mail@cebe.cc>', [$ruleEnabledIDN], $errors],

            [
                'Короткое имя <тест@это-доменное-имя.после-преобразования-в-idn.будет-содержать-больше-254-символов.' .
                'бла-бла-бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-' .
                'бла.com>',
                [$ruleEnabledIDNandAllowedName],
                $errors,
            ],
            ['Information info@örtliches.de', [$ruleEnabledIDNandAllowedName], $errors],
            ['rmcreative.ru', [$ruleEnabledIDNandAllowedName], $errors],
            ['John Smith <example.com>', [$ruleEnabledIDNandAllowedName], $errors],
            [
                'Короткое имя <после-преобразования-в-idn-тут-будет-больше-чем-64-символа@пример.com>',
                [$ruleEnabledIDNandAllowedName],
                $errors,
            ],

            [
                'test@nonexistingsubdomain.example.com',
                [new Email(checkDNS: true)],
                $errors,
            ],
        ];
    }

    /**
     * @dataProvider dataValidationFailed
     */
    public function testValidationFailed(mixed $data, array $rules, array $errorMessagesIndexedByPath): void
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('The intl extension must be available for this test.');
        }

        $result = $this->createValidator()->validate($data, $rules);

        $this->assertFalse($result->isValid());
        $this->assertSame($errorMessagesIndexedByPath, $result->getErrorMessagesIndexedByPath());
    }

    public function testCustomErrorMessage(): void
    {
        $data = 'test@nonexistingsubdomain.example.com';
        $rules = [new Email(checkDNS: true, message: 'Custom error')];

        $result = $this->createValidator()->validate($data, $rules);

        $this->assertFalse($result->isValid());
        $this->assertSame(
            ['' => ['Custom error']],
            $result->getErrorMessagesIndexedByPath()
        );
    }

    public function testEnableIdnWithMissingIntlExtension(): void
    {
        if (extension_loaded('intl')) {
            $this->markTestSkipped('The intl extension must be unavailable for this test.');
        }

        $this->expectException(RuntimeException::class);
        new Email(enableIDN: true);
    }

    private function createValidator(): Validator
    {
        return FakeValidatorFactory::make();
    }
}
