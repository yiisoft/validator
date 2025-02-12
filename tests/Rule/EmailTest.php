<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\EmailHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class EmailTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public static function dataInvalidConfiguration(): array
    {
        return [
            [['pattern' => ''], 'Pattern can\'t be empty.'],
            [['fullPattern' => ''], 'Full pattern can\'t be empty.'],
            [['idnEmailPattern' => ''], 'IDN e-mail pattern can\'t be empty.'],
        ];
    }

    #[DataProvider('dataInvalidConfiguration')]
    public function testinvalidConfiguration(array $arguments, string $expectedExceptionMessage): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        new Email(...$arguments);
    }

    public function testGetName(): void
    {
        $rule = new Email();
        $this->assertSame(Email::class, $rule->getName());
    }

    public static function dataOptions(): array
    {
        return [
            'default' => [
                new Email(),
                [
                    'pattern' => '/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/',
                    'fullPattern' => '/^[^@]*<[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?>$/',
                    'idnEmailPattern' => '/^([a-zA-Z0-9._%+-]+)@((\[\d{1,3}\.\d{1,3}\.\d{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|\d{1,3})(\]?)$/',
                    'allowName' => false,
                    'checkDns' => false,
                    'enableIdn' => false,
                    'incorrectInputMessage' => [
                        'template' => '{Property} must be a string. {type} given.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => '{Property} is not a valid email address.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'custom' => [
                new Email(
                    pattern: '/test1/',
                    fullPattern: '/test2/',
                    idnEmailPattern: '/test3/',
                    allowName: true,
                    checkDns: true,
                    enableIdn: true,
                    incorrectInputMessage: 'Custom message 1.',
                    message: 'Custom message 2.',
                    skipOnEmpty: true,
                    skipOnError: true,
                ),
                [
                    'pattern' => '/test1/',
                    'fullPattern' => '/test2/',
                    'idnEmailPattern' => '/test3/',
                    'allowName' => true,
                    'checkDns' => true,
                    'enableIdn' => true,
                    'incorrectInputMessage' => [
                        'template' => 'Custom message 1.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'Custom message 2.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => true,
                    'skipOnError' => true,
                ],
            ],
        ];
    }

    public static function dataValidationPassed(): array
    {
        $rule = new Email();
        $ruleAllowedName = new Email(allowName: true);
        $ruleEnabledIdn = new Email(enableIdn: true);
        $ruleEnabledIdnAndAllowedName = new Email(allowName: true, enableIdn: true);

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

            ['5011@example.com', [$ruleEnabledIdn]],
            ['test-@dummy.com', [$ruleEnabledIdn]],
            ['example@äüößìà.de', [$ruleEnabledIdn]],
            ['example@xn--zcack7ayc9a.de', [$ruleEnabledIdn]],
            ['info@örtliches.de', [$ruleEnabledIdn]],
            ['sam@рмкреатиф.ru', [$ruleEnabledIdn]],
            ['sam@rmcreative.ru', [$ruleEnabledIdn]],
            ['5011@gmail.com', [$ruleEnabledIdn]],
            ['üñîçøðé@üñîçøðé.com', [$ruleEnabledIdn]],

            ['info@örtliches.de', [$ruleEnabledIdnAndAllowedName]],
            ['Information <info@örtliches.de>', [$ruleEnabledIdnAndAllowedName]],
            ['sam@рмкреатиф.ru', [$ruleEnabledIdnAndAllowedName]],
            ['sam@rmcreative.ru', [$ruleEnabledIdnAndAllowedName]],
            ['5011@gmail.com', [$ruleEnabledIdnAndAllowedName]],
            ['Carsten Brandt <mail@cebe.cc>', [$ruleEnabledIdnAndAllowedName]],
            ['"Carsten Brandt" <mail@cebe.cc>', [$ruleEnabledIdnAndAllowedName]],
            ['üñîçøðé 日本国 <üñîçøðé@üñîçøðé.com>', [$ruleEnabledIdnAndAllowedName]],
            ['<mail@cebe.cc>', [$ruleEnabledIdnAndAllowedName]],
            ['test@example.com', [$ruleEnabledIdnAndAllowedName]],
            ['John Smith <john.smith@example.com>', [$ruleEnabledIdnAndAllowedName]],
            [
                '"Такое имя достаточно длинное, но оно все равно может пройти валидацию" <shortmail@example.com>',
                [$ruleEnabledIdnAndAllowedName],
            ],

            ['5011@gmail.com', [new Email(checkDns: true)]],

            ['ipetrov@gmail.com', [new Email(allowName: true, checkDns: true)]],
            ['Ivan Petrov <ipetrov@gmail.com>', [new Email(allowName: true, checkDns: true)]],
            ['name@ñandu.cl', [new Email(checkDns: true, enableIdn: true)]],
        ];
    }

    public static function dataValidationFailed(): array
    {
        $rule = new Email();
        $ruleAllowedName = new Email(allowName: true);
        $ruleEnabledIdn = new Email(enableIdn: true);
        $ruleEnabledIdnAndAllowedName = new Email(allowName: true, enableIdn: true);
        $errors = ['' => ['Value is not a valid email address.']];

        return [
            'incorrect input, integer' => [1, [$rule], ['' => ['Value must be a string. int given.']]],
            'incorrect input, array containing string element' => [
                ['developer@yiiframework.com'],
                [$ruleAllowedName],
                ['' => ['Value must be a string. array given.']],
            ],
            'custom incorrect input message' => [
                1,
                [new Email(incorrectInputMessage: 'Custom incorrect input message.')],
                ['' => ['Custom incorrect input message.']],
            ],
            'custom incorrect input message with parameters' => [
                1,
                [new Email(incorrectInputMessage: 'Property - {property}, type - {type}.')],
                ['' => ['Property - value, type - int.']],
            ],
            'custom incorrect input message with parameters, property set' => [
                ['data' => 1],
                ['data' => [new Email(incorrectInputMessage: 'Property - {property}, type - {type}.')]],
                ['data' => ['Property - data, type - int.']],
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

            ['rmcreative.ru', [$ruleEnabledIdn], $errors],
            ['Carsten Brandt <mail@cebe.cc>', [$ruleEnabledIdn], $errors],
            ['"Carsten Brandt" <mail@cebe.cc>', [$ruleEnabledIdn], $errors],
            ['<mail@cebe.cc>', [$ruleEnabledIdn], $errors],

            [
                'Короткое имя <тест@это-доменное-имя.после-преобразования-в-idn.будет-содержать-больше-254-символов.' .
                'бла-бла-бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-бла.бла-бла-бла-бла-бла-' .
                'бла.com>',
                [$ruleEnabledIdnAndAllowedName],
                $errors,
            ],
            ['Information info@örtliches.de', [$ruleEnabledIdnAndAllowedName], $errors],
            ['rmcreative.ru', [$ruleEnabledIdnAndAllowedName], $errors],
            ['John Smith <example.com>', [$ruleEnabledIdnAndAllowedName], $errors],
            [
                'Короткое имя <после-преобразования-в-idn-тут-будет-больше-чем-64-символа@пример.com>',
                [$ruleEnabledIdnAndAllowedName],
                $errors,
            ],

            ['name@ñandu.cl', [new Email(checkDns: true)], $errors],
            ['gmail.con', [new Email(checkDns: true)], $errors],
            [
                'test@nonexistingsubdomain.example.com',
                [new Email(checkDns: true)],
                $errors,
            ],

            'custom message' => [
                'test@nonexistingsubdomain.example.com',
                [new Email(checkDns: true, message: 'Custom message.')],
                ['' => ['Custom message.']],
            ],
            'custom message with parameters' => [
                'test@nonexistingsubdomain.example.com',
                [new Email(checkDns: true, message: 'Property - {Property}, value - {value}.')],
                ['' => ['Property - Value, value - test@nonexistingsubdomain.example.com.']],
            ],
            'custom message with parameters, property set' => [
                ['data' => 'test@nonexistingsubdomain.example.com'],
                ['data' => new Email(checkDns: true, message: 'Property - {property}, value - {value}.')],
                ['data' => ['Property - data, value - test@nonexistingsubdomain.example.com.']],
            ],
            'edge-case-1' => [
                'test@-example.com',
                new Email(enableIdn: true, checkDns: true, pattern: '/.*/'),
                ['' => ['Value is not a valid email address.']],
            ]
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new Email(), new Email(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new Email(), new Email(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Email::class, EmailHandler::class];
    }
}
