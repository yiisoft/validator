<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\TestEnvironments\WithIntl\Rule;

use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;

final class EmailTest extends RuleTestCase
{
    use RuleWithOptionsTestTrait;

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
                    'incorrectInputMessage' => [
                        'template' => 'The value must have a string type.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'This value is not a valid email address.',
                        'parameters' => [],
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
                    'incorrectInputMessage' => [
                        'template' => 'The value must have a string type.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'This value is not a valid email address.',
                        'parameters' => [],
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
                    'incorrectInputMessage' => [
                        'template' => 'The value must have a string type.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'This value is not a valid email address.',
                        'parameters' => [],
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
                    'incorrectInputMessage' => [
                        'template' => 'The value must have a string type.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'This value is not a valid email address.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    public function dataValidationPassed(): array
    {
        $rule = new Email();
        $ruleAllowedName = new Email(allowName: true);
        $ruleEnabledIDN = new Email(enableIDN: true);
        $ruleEnabledIDNandAllowedName = new Email(allowName: true, enableIDN: true);

        return [
            ['developer@yiiframework.com', [$rule]],
            ['sam@rmcreative.ru', [$rule]],
            ['5011@gmail.com', [$rule]],
            ['Abc.123@example.com', [$rule]],
            ['user+mailbox/department=shipping@example.com', [$rule]],
            ['!#$%&\'*+-/=?^_`.{|}~@example.com', [$rule]],
            ['test@nonexistingsubdomain.example.com', [$rule]], // checkDNS is disabled
            ['name@gmail.con', [$rule]],
            [str_repeat('a', 64) . '@gmail.com', [$rule]],
            ['name@' . str_repeat('a', 245) . '.com', [$rule]],
            ['SAM@RMCREATIVE.RU', [$rule]],

            ['developer@yiiframework.com', [$ruleAllowedName]],
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
            ['name@ñandu.cl', [new Email(checkDNS: true, enableIDN: true)]],
        ];
    }

    public function dataValidationFailed(): array
    {
        $rule = new Email();
        $ruleAllowedName = new Email(allowName: true);
        $ruleEnabledIDN = new Email(enableIDN: true);
        $ruleEnabledIDNandAllowedName = new Email(allowName: true, enableIDN: true);
        $errors = ['' => ['This value is not a valid email address.']];
        $incorrectInputErrors = ['' => ['The value must have a string type.']];

        return [
            'incorrect input, integer' => [1, [$rule], $incorrectInputErrors],
            'incorrect input, array containing string element' => [
                ['developer@yiiframework.com'],
                [$ruleAllowedName],
                $incorrectInputErrors,
            ],
            'custom incorrect input message' => [
                1,
                [new Email(incorrectInputMessage: 'Custom incorrect input message.')],
                ['' => ['Custom incorrect input message.']],
            ],
            'custom incorrect input message with parameters' => [
                1,
                [new Email(incorrectInputMessage: 'Attribute - {attribute}, type - {type}.')],
                ['' => ['Attribute - , type - int.']],
            ],
            'custom incorrect input message with parameters, attribute set' => [
                ['data' => 1],
                ['data' => [new Email(incorrectInputMessage: 'Attribute - {attribute}, type - {type}.')]],
                ['data' => ['Attribute - data, type - int.']],
            ],

            ['rmcreative.ru', [$rule], $errors],
            ['Carsten Brandt <mail@cebe.cc>', [$rule], $errors],
            ['"Carsten Brandt" <mail@cebe.cc>', [$rule], $errors],
            ['<mail@cebe.cc>', [$rule], $errors],
            ['info@örtliches.de', [$rule], $errors],
            ['sam@рмкреатиф.ru', [$rule], $errors],
            ['ex..ample@example.com', [$rule], $errors],
            [str_repeat('a', 65) . '@gmail.com', [$rule], $errors],
            ['name@' . str_repeat('a', 246) . '.com', [$rule], $errors],

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
            [
                'Short Name <domainNameIsMoreThan254Characters@example-blah-blah-blah-blah-blah-blah-blah-blah-blah-' .
                'blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-' .
                'blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah-blah' .
                '.com>',
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

            ['name@ñandu.cl', [new Email(checkDNS: true)], $errors],
            ['gmail.con', [new Email(checkDNS: true)], $errors],
            [
                'test@nonexistingsubdomain.example.com',
                [new Email(checkDNS: true)],
                $errors,
            ],

            'custom message' => [
                'test@nonexistingsubdomain.example.com',
                [new Email(checkDNS: true, message: 'Custom message.')],
                ['' => ['Custom message.']],
            ],
            'custom message with parameters' => [
                'test@nonexistingsubdomain.example.com',
                [new Email(checkDNS: true, message: 'Attribute - {attribute}, value - {value}.')],
                ['' => ['Attribute - , value - test@nonexistingsubdomain.example.com.']],
            ],
            'custom message with parameters, attribute set' => [
                ['data' => 'test@nonexistingsubdomain.example.com'],
                ['data' => new Email(checkDNS: true, message: 'Attribute - {attribute}, value - {value}.')],
                ['data' => ['Attribute - data, value - test@nonexistingsubdomain.example.com.']],
            ],
        ];
    }
}
