<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use RuntimeException;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\EmailHandler;
use Yiisoft\Validator\RuleHandlerInterface;

use function extension_loaded;

final class EmailHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        if (!extension_loaded('intl')) {
            return [];
        }

        $rule = new Email();
        $ruleAllowedName = new Email(allowName: true);
        $ruleEnabledIDN = new Email(enableIDN: true);
        $ruleEnabledIDNandAllowedName = new Email(allowName: true, enableIDN: true);
        $errors = [new Error('This value is not a valid email address.')];

        return [
            [$rule, ...$this->createValueAndErrorsPair('rmcreative.ru', $errors)],
            [$rule, ...$this->createValueAndErrorsPair('Carsten Brandt <mail@cebe.cc>', $errors)],
            [$rule, ...$this->createValueAndErrorsPair('"Carsten Brandt" <mail@cebe.cc>', $errors)],
            [$rule, ...$this->createValueAndErrorsPair('<mail@cebe.cc>', $errors)],
            [$rule, ...$this->createValueAndErrorsPair('info@örtliches.de', $errors)],
            [$rule, ...$this->createValueAndErrorsPair('sam@рмкреатиф.ru', $errors)],
            [$rule, ...$this->createValueAndErrorsPair('ex..ample@example.com', $errors)],
            [$rule, ...$this->createValueAndErrorsPair(['developer@yiiframework.com'], $errors)],

            // Malicious email addresses that can be used to exploit SwiftMailer vulnerability CVE-2016-10074 while IDN
            // is disabled.
            // https://legalhackers.com/advisories/SwiftMailer-Exploit-Remote-Code-Exec-CVE-2016-10074-Vuln.html

            // This is the demo email used in the proof of concept of the exploit
            [
                $rule,
                ...$this->createValueAndErrorsPair(
                    '"attacker\" -oQ/tmp/ -X/var/www/cache/phpcode.php "@email.com',
                    $errors
                ),
            ],

            // Trying more addresses
            [$rule, ...$this->createValueAndErrorsPair('"Attacker -Param2 -Param3"@test.com', $errors)],
            [$rule, ...$this->createValueAndErrorsPair('\'Attacker -Param2 -Param3\'@test.com', $errors)],
            [$rule, ...$this->createValueAndErrorsPair('"Attacker \" -Param2 -Param3"@test.com', $errors)],
            [$rule, ...$this->createValueAndErrorsPair("'Attacker \\' -Param2 -Param3'@test.com", $errors)],
            [
                $rule,
                ...$this->createValueAndErrorsPair(
                    '"attacker\" -oQ/tmp/ -X/var/www/cache/phpcode.php "@email.com',
                    $errors
                ),
            ],

            // And even more variants
            [
                $rule,
                ...$this->createValueAndErrorsPair(
                    '"attacker\"\ -oQ/tmp/\ -X/var/www/cache/phpcode.php"@email.com',
                    $errors
                ),
            ],
            [
                $rule,
                ...$this->createValueAndErrorsPair(
                    "\"attacker\\\"\0-oQ/tmp/\0-X/var/www/cache/phpcode.php\"@email.com",
                    $errors
                ),
            ],
            [$rule, ...$this->createValueAndErrorsPair('"attacker@cebe.cc\"-Xbeep"@email.com', $errors)],
            [
                $rule,
                ...$this->createValueAndErrorsPair(
                    "'attacker\\' -oQ/tmp/ -X/var/www/cache/phpcode.php'@email.com",
                    $errors
                ),
            ],
            [
                $rule,
                ...$this->createValueAndErrorsPair(
                    "'attacker\\\\' -oQ/tmp/ -X/var/www/cache/phpcode.php'@email.com",
                    $errors
                ),
            ],
            [
                $rule,
                ...$this->createValueAndErrorsPair(
                    "'attacker\\\\'\\ -oQ/tmp/ -X/var/www/cache/phpcode.php'@email.com",
                    $errors
                ),
            ],
            [$rule, ...$this->createValueAndErrorsPair("'attacker\\';touch /tmp/hackme'@email.com", $errors)],
            [$rule, ...$this->createValueAndErrorsPair("'attacker\\\\';touch /tmp/hackme'@email.com", $errors)],
            [$rule, ...$this->createValueAndErrorsPair("'attacker\\';touch/tmp/hackme'@email.com", $errors)],
            [$rule, ...$this->createValueAndErrorsPair("'attacker\\\\';touch/tmp/hackme'@email.com", $errors)],
            [
                $rule,
                ...$this->createValueAndErrorsPair(
                    '"attacker\" -oQ/tmp/ -X/var/www/cache/phpcode.php "@email.com',
                    $errors
                ),
            ],

            [$ruleAllowedName, ...$this->createValueAndErrorsPair('rmcreative.ru', $errors)],
            [$ruleAllowedName, ...$this->createValueAndErrorsPair('info@örtliches.de', $errors)],
            [$ruleAllowedName, ...$this->createValueAndErrorsPair('üñîçøðé@üñîçøðé.com', $errors)],
            [$ruleAllowedName, ...$this->createValueAndErrorsPair('sam@рмкреатиф.ru', $errors)],
            [$ruleAllowedName, ...$this->createValueAndErrorsPair('Informtation info@oertliches.de', $errors)],
            [$ruleAllowedName, ...$this->createValueAndErrorsPair('John Smith <example.com>', $errors)],
            [
                $ruleAllowedName,
                ...$this->createValueAndErrorsPair(
                    'Short Name <localPartMoreThan64Characters-blah-blah-blah-blah-blah-blah-blah-blah@example.com>',
                    $errors
                ),
            ],
            [$ruleAllowedName, ...$this->createValueAndErrorsPair(['developer@yiiframework.com'], $errors)],
            [
                $ruleAllowedName,
                ...$this->createValueAndErrorsPair([
                    'Short Name <domainNameIsMoreThan254Characters@example-blah-blah-blah-blah-blah-blah-blah-blah-' .
                    'blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-' .
                    'blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-' .
                    'blah-blah-blah.com>',
                ],
                    $errors
                ),
            ],

            [$ruleEnabledIDN, ...$this->createValueAndErrorsPair('rmcreative.ru', $errors)],
            [$ruleEnabledIDN, ...$this->createValueAndErrorsPair('Carsten Brandt <mail@cebe.cc>', $errors)],
            [$ruleEnabledIDN, ...$this->createValueAndErrorsPair('"Carsten Brandt" <mail@cebe.cc>', $errors)],
            [$ruleEnabledIDN, ...$this->createValueAndErrorsPair('<mail@cebe.cc>', $errors)],

            [
                $ruleEnabledIDNandAllowedName,
                ...$this->createValueAndErrorsPair(
                    'Короткое имя <тест@это-доменное-имя.после-преобразования-в-idn.будет-содержать-больше-254-символов.' .
                    'бла-бла-бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-' .
                    'бла.com>',
                    $errors
                ),
            ],
            [
                $ruleEnabledIDNandAllowedName,
                ...$this->createValueAndErrorsPair('Information info@örtliches.de', $errors),
            ],
            [$ruleEnabledIDNandAllowedName, ...$this->createValueAndErrorsPair('rmcreative.ru', $errors)],
            [$ruleEnabledIDNandAllowedName, ...$this->createValueAndErrorsPair('John Smith <example.com>', $errors)],
            [
                $ruleEnabledIDNandAllowedName,
                ...$this->createValueAndErrorsPair(
                    'Короткое имя <после-преобразования-в-idn-тут-будет-больше-чем-64-символа@пример.com>',
                    $errors
                ),
            ],

            [
                new Email(checkDNS: true),
                ...$this->createValueAndErrorsPair('test@nonexistingsubdomain.example.com', $errors),
            ],
        ];
    }

    /**
     * @dataProvider failedValidationProvider
     */
    public function testValidationFailed(object $config, mixed $value, array $expectedErrors): void
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('The intl extension must be available for this test.');
        }

        parent::testValidationFailed($config, $value, $expectedErrors);
    }

    public function passedValidationProvider(): array
    {
        if (!extension_loaded('intl')) {
            return [];
        }

        $rule = new Email();
        $ruleAllowedName = new Email(allowName: true);
        $ruleEnabledIDN = new Email(enableIDN: true);
        $ruleEnabledIDNandAllowedName = new Email(allowName: true, enableIDN: true);

        return [
            [$rule, 'sam@rmcreative.ru'],
            [$rule, '5011@gmail.com'],
            [$rule, 'Abc.123@example.com'],
            [$rule, 'user+mailbox/department=shipping@example.com'],
            [$rule, '!#$%&\'*+-/=?^_`.{|}~@example.com'],
            [$rule, 'test@nonexistingsubdomain.example.com'], // checkDNS is disabled

            [$ruleAllowedName, 'sam@rmcreative.ru'],
            [$ruleAllowedName, '5011@gmail.com'],
            [$ruleAllowedName, 'Carsten Brandt <mail@cebe.cc>'],
            [$ruleAllowedName, '"Carsten Brandt" <mail@cebe.cc>'],
            [$ruleAllowedName, '<mail@cebe.cc>'],
            [$ruleAllowedName, 'test@example.com'],
            [$ruleAllowedName, 'John Smith <john.smith@example.com>'],
            [
                $ruleAllowedName,
                '"This name is longer than 64 characters. Blah blah blah blah blah" <shortmail@example.com>',
            ],

            [$ruleEnabledIDN, '5011@example.com'],
            [$ruleEnabledIDN, 'test-@dummy.com'],
            [$ruleEnabledIDN, 'example@äüößìà.de'],
            [$ruleEnabledIDN, 'example@xn--zcack7ayc9a.de'],
            [$ruleEnabledIDN, 'info@örtliches.de'],
            [$ruleEnabledIDN, 'sam@рмкреатиф.ru'],
            [$ruleEnabledIDN, 'sam@rmcreative.ru'],
            [$ruleEnabledIDN, '5011@gmail.com'],
            [$ruleEnabledIDN, 'üñîçøðé@üñîçøðé.com'],

            [$ruleEnabledIDNandAllowedName, 'info@örtliches.de'],
            [$ruleEnabledIDNandAllowedName, 'Information <info@örtliches.de>'],
            [$ruleEnabledIDNandAllowedName, 'sam@рмкреатиф.ru'],
            [$ruleEnabledIDNandAllowedName, 'sam@rmcreative.ru'],
            [$ruleEnabledIDNandAllowedName, '5011@gmail.com'],
            [$ruleEnabledIDNandAllowedName, 'Carsten Brandt <mail@cebe.cc>'],
            [$ruleEnabledIDNandAllowedName, '"Carsten Brandt" <mail@cebe.cc>'],
            [$ruleEnabledIDNandAllowedName, 'üñîçøðé 日本国 <üñîçøðé@üñîçøðé.com>'],
            [$ruleEnabledIDNandAllowedName, '<mail@cebe.cc>'],
            [$ruleEnabledIDNandAllowedName, 'test@example.com'],
            [$ruleEnabledIDNandAllowedName, 'John Smith <john.smith@example.com>'],
            [
                $ruleEnabledIDNandAllowedName,
                '"Такое имя достаточно длинное, но оно все равно может пройти валидацию" <shortmail@example.com>',
            ],

            [new Email(checkDNS: true), '5011@gmail.com'],

            [new Email(allowName: true, checkDNS: true), 'ipetrov@gmail.com'],
            [new Email(allowName: true, checkDNS: true), 'Ivan Petrov <ipetrov@gmail.com>'],
        ];
    }

    /**
     * @dataProvider passedValidationProvider
     */
    public function testValidationPassed(object $config, mixed $value): void
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('The intl extension must be available for this test.');
        }

        parent::testValidationPassed($config, $value);
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [
                new Email(checkDNS: true, message: 'Custom error'),
                ...$this->createValueAndErrorsPair(
                'test@nonexistingsubdomain.example.com',
                [new Error('Custom error')]
                ),
            ],
        ];
    }

    public function testEnableIdnWithMissingIntlExtension(): void
    {
        if (extension_loaded('intl')) {
            $this->markTestSkipped('The intl extension must be unavailable for this test.');
        }

        $this->expectException(RuntimeException::class);
        new Email(enableIDN: true);
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new EmailHandler();
    }
}
