<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use RuntimeException;
use Xepozz\InternalMocker\MockerState;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\EmailHandler;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\Tests\MockerExtension;

final class EmailHandlerTest extends AbstractRuleValidatorTest
{
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        defined('INTL_IDNA_VARIANT_UTS46') || define('INTL_IDNA_VARIANT_UTS46', 1);
        MockerExtension::load();
        parent::__construct($name, $data, $dataName);
    }

    public function failedValidationProvider(): array
    {
        $rule = new Email();
        $ruleAllowedName = new Email(allowName: true);
        $ruleEnabledIDN = new Email(enableIDN: true);
        $ruleEnabledIDNandAllowedName = new Email(allowName: true, enableIDN: true);
        $errors = [new Error('This value is not a valid email address.')];

        return [
            [$rule, 'rmcreative.ru', $errors],
            [$rule, 'Carsten Brandt <mail@cebe.cc>', $errors],
            [$rule, '"Carsten Brandt" <mail@cebe.cc>', $errors],
            [$rule, '<mail@cebe.cc>', $errors],
            [$rule, 'info@örtliches.de', $errors],
            [$rule, 'sam@рмкреатиф.ru', $errors],
            [$rule, 'ex..ample@example.com', $errors],
            [$rule, ['developer@yiiframework.com'], $errors],

            // Malicious email addresses that can be used to exploit SwiftMailer vulnerability CVE-2016-10074 while IDN
            // is disabled.
            // https://legalhackers.com/advisories/SwiftMailer-Exploit-Remote-Code-Exec-CVE-2016-10074-Vuln.html

            // This is the demo email used in the proof of concept of the exploit
            [$rule, '"attacker\" -oQ/tmp/ -X/var/www/cache/phpcode.php "@email.com', $errors],

            // Trying more addresses
            [$rule, '"Attacker -Param2 -Param3"@test.com', $errors],
            [$rule, '\'Attacker -Param2 -Param3\'@test.com', $errors],
            [$rule, '"Attacker \" -Param2 -Param3"@test.com', $errors],
            [$rule, "'Attacker \\' -Param2 -Param3'@test.com", $errors],
            [$rule, '"attacker\" -oQ/tmp/ -X/var/www/cache/phpcode.php "@email.com', $errors],

            // And even more variants
            [$rule, '"attacker\"\ -oQ/tmp/\ -X/var/www/cache/phpcode.php"@email.com', $errors],
            [$rule, "\"attacker\\\"\0-oQ/tmp/\0-X/var/www/cache/phpcode.php\"@email.com", $errors],
            [$rule, '"attacker@cebe.cc\"-Xbeep"@email.com', $errors],
            [$rule, "'attacker\\' -oQ/tmp/ -X/var/www/cache/phpcode.php'@email.com", $errors],
            [$rule, "'attacker\\\\' -oQ/tmp/ -X/var/www/cache/phpcode.php'@email.com", $errors],
            [$rule, "'attacker\\\\'\\ -oQ/tmp/ -X/var/www/cache/phpcode.php'@email.com", $errors],
            [$rule, "'attacker\\';touch /tmp/hackme'@email.com", $errors],
            [$rule, "'attacker\\\\';touch /tmp/hackme'@email.com", $errors],
            [$rule, "'attacker\\';touch/tmp/hackme'@email.com", $errors],
            [$rule, "'attacker\\\\';touch/tmp/hackme'@email.com", $errors],
            [$rule, '"attacker\" -oQ/tmp/ -X/var/www/cache/phpcode.php "@email.com', $errors],

            [$ruleAllowedName, 'rmcreative.ru', $errors],
            [$ruleAllowedName, 'info@örtliches.de', $errors],
            [$ruleAllowedName, 'üñîçøðé@üñîçøðé.com', $errors],
            [$ruleAllowedName, 'sam@рмкреатиф.ru', $errors],
            [$ruleAllowedName, 'Informtation info@oertliches.de', $errors],
            [$ruleAllowedName, 'John Smith <example.com>', $errors],
            [
                $ruleAllowedName,
                'Short Name <localPartMoreThan64Characters-blah-blah-blah-blah-blah-blah-blah-blah@example.com>',
                $errors,
            ],
            [$ruleAllowedName, ['developer@yiiframework.com'], $errors],
            [
                $ruleAllowedName,
                [
                    'Short Name <domainNameIsMoreThan254Characters@example-blah-blah-blah-blah-blah-blah-blah-blah-' .
                    'blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-' .
                    'blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-' .
                    'blah-blah-blah.com>',
                ],
                $errors,
            ],

            [$ruleEnabledIDN, 'rmcreative.ru', $errors],
            [$ruleEnabledIDN, 'Carsten Brandt <mail@cebe.cc>', $errors],
            [$ruleEnabledIDN, '"Carsten Brandt" <mail@cebe.cc>', $errors],
            [$ruleEnabledIDN, '<mail@cebe.cc>', $errors],

            [
                $ruleEnabledIDNandAllowedName,
                'Короткое имя <тест@это-доменное-имя.после-преобразования-в-idn.будет-содержать-больше-254-символов.' .
                'бла-бла-бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-' .
                'бла.com>',
                $errors,
            ],
            [$ruleEnabledIDNandAllowedName, 'Information info@örtliches.de', $errors],
            [$ruleEnabledIDNandAllowedName, 'rmcreative.ru', $errors],
            [$ruleEnabledIDNandAllowedName, 'John Smith <example.com>', $errors],
            [
                $ruleEnabledIDNandAllowedName,
                'Короткое имя <после-преобразования-в-idn-тут-будет-больше-чем-64-символа@пример.com>',
                $errors,
            ],

            [new Email(checkDNS: true), 'test@nonexistingsubdomain.example.com', $errors],
        ];
    }

    /**
     * @dataProvider failedValidationProvider
     */
    public function testValidationFailed(object $config, mixed $value, array $expectedErrors): void
    {
        /**
         * @var $config Email
         */
        if ($config->isCheckDNS()) {
            MockerState::addCondition(
                'Yiisoft\\Validator\\Rule',
                'checkdnsrr',
                [],
                false,
                true,
            );

            MockerState::addCondition(
                'Yiisoft\\Validator\\Rule',
                'idn_to_ascii',
                ['mail', 0, 1],
                'mail',
            );
            MockerState::addCondition(
                'Yiisoft\\Validator\\Rule',
                'idn_to_ascii',
                ['info', 0, 1],
                'info',
            );
            MockerState::addCondition(
                'Yiisoft\\Validator\\Rule',
                'idn_to_ascii',
                ['örtliches.de', 0, 1],
                'xn--rtliches-m4a.de',
            );
            MockerState::addCondition(
                'Yiisoft\\Validator\\Rule',
                'idn_to_ascii',
                ['после-преобразования-в-idn-тут-будет-больше-чем-64-символа', 0, 1],
                false,
            );
            MockerState::addCondition(
                'Yiisoft\\Validator\\Rule',
                'idn_to_ascii',
                ['пример.com', 0, 1],
                'xn--e1afmkfd.com',
            );
            MockerState::addCondition(
                'Yiisoft\\Validator\\Rule',
                'idn_to_ascii',
                ['cebe.cc', 0, 1],
                'cebe.cc',
            );
            MockerState::addCondition(
                'Yiisoft\\Validator\\Rule',
                'idn_to_ascii',
                ['тест', 0, 1],
                'xn--e1aybc',
            );
            MockerState::addCondition(
                'Yiisoft\\Validator\\Rule',
                'idn_to_ascii',
                ['это-доменное-имя.после-преобразования-в-idn.будет-содержать-больше-254-символов.бла-бла-бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-бла.com', 0, 1],
                false,
            );
        }

        parent::testValidationFailed($config, $value, $expectedErrors);
    }

    public function passedValidationProvider(): array
    {
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
        /**
         * @var $config Email
         */
        if ($config->isCheckDNS()) {
            MockerState::addCondition(
                'Yiisoft\\Validator\\Rule',
                'checkdnsrr',
                [],
                true,
                true,
            );
        }

        parent::testValidationPassed($config, $value);
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [
                new Email(checkDNS: true, message: 'Custom error'),
                'test@nonexistingsubdomain.example.com',
                [new Error('Custom error')],
            ],
        ];
    }

    /**
     * @dataProvider customErrorMessagesProvider
     */
    public function testCustomErrorMessages(object $config, mixed $value, array $expectedErrorMessages): void
    {
        /**
         * @var $config Email
         */
        if ($config->isCheckDNS()) {
            MockerState::addCondition(
                'Yiisoft\\Validator\\Rule',
                'checkdnsrr',
                [],
                false,
                true,
            );
        }
        parent::testCustomErrorMessages($config, $value, $expectedErrorMessages);
    }

    public function testEnableIdnWithMissingIntlExtension(): void
    {
        MockerState::addCondition(
            'Yiisoft\\Validator\\Rule',
            'function_exists',
            ['idn_to_ascii'],
            false,
        );

        $this->expectException(RuntimeException::class);
        new Email(enableIDN: true);
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new EmailHandler($this->getTranslator());
    }
}
