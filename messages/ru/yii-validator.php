<?php

declare(strict_types=1);

use Yiisoft\Validator\Rule\AtLeast;
use Yiisoft\Validator\Rule\BooleanValue;
use Yiisoft\Validator\Rule\Compare;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Equal;
use Yiisoft\Validator\Rule\GreaterThan;
use Yiisoft\Validator\Rule\GreaterThanOrEqual;
use Yiisoft\Validator\Rule\In;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Ip;
use Yiisoft\Validator\Rule\Json;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\LessThan;
use Yiisoft\Validator\Rule\LessThanOrEqual;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\NotEqual;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\OneOf;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Subset;
use Yiisoft\Validator\Rule\TrueValue;
use Yiisoft\Validator\Rule\Url;

return [
    // Used in single rule

    /** @see AtLeast */
    'At least {min, number} {min, plural, one{attribute} other{attributes}} from this list must be filled' => 'Как ' .
        'минимум {min, number}' .
        '{min, plural, one{атрибут} few{атрибута} many{атрибутов} other{атрибута}} из этого списка ' .
        '{min, plural, one{должен} few{должны} many{должны} other{должны}} быть ' .
        'заполнены: {attributes}.',
    /** @see BooleanValue */
    '{label} must be either "{true}" or "{false}".' => '{label} должно быть «{true}» или «{false}».',
    /** @see Count */
    '{label} must be an array or implement \Countable interface.' => '{label} должно быть массивом или объектом, реализующим интерфейс \Countable.',
    '{label} must contain at least {min, number} {min, plural, one{item} other{items}}.' => '{label} должно содержать как минимум {min, number} {min, plural, one{элемент} few{элемента} many{элементов} other{элементов}}.',
    '{label} must contain at most {max, number} {max, plural, one{item} other{items}}.' => '{label} должно содержать не более {max, number} {max, plural, one{элемента} few{элементов} many{элементов} other{элементов}}.',
    '{label} must contain exactly {exactly, number} {exactly, plural, one{item} other{items}}.' => '{label} должно содержать ровно {exactly, number} {exactly, plural, one{элемент} few{элемента} many{элементов} other{элементов}}.',
    /** @see Each */
    '{label} must be array or iterable.' => '{label} должно быть массивом или иметь псевдотип iterable.',
    'Every iterable key must have an integer or a string type.' => 'Ключ должен иметь тип integer или string.',
    /** @see Email */
    '{label} is not a valid email address.' => '{label} не является правильным адресом электронной почты.',
    /** @see In */
    '{label} is not in the list of acceptable values.' => '{label} не в списке допустимых значений.',
    /** @see Ip */
    'Must be a valid IP address.' => 'Должно быть правильным IP-адресом.',
    'Must not be an IPv4 address.' => 'Не должно быть IPv4-адресом.',
    'Must not be an IPv6 address.' => 'Не должно быть IPv6-адресом.',
    'Contains wrong subnet mask.' => 'Содержит неверную маску подсети.',
    'Must be an IP address with specified subnet.' => 'Должно быть IP адресом с подсетью.',
    'Must not be a subnet.' => 'Не должно быть подсетью.',
    'Is not in the allowed range.' => 'Не входит в список разрешенных диапазонов адресов.',
    /** @see Integer */
    '{label} must be an integer.' => '{label} должно быть целым числом.',
    /** @see Json */
    '{label} is not JSON.' => '{label} не является строкой JSON.',
    /** @see Length */
    '{label} must contain at least {min, number} {min, plural, one{character} other{characters}}.' => '{label} должно содержать как минимум {min, number} {min, plural, one{символ} few{символа} many{символов} other{символов}}.',
    '{label} must contain at most {max, number} {max, plural, one{character} other{characters}}.' => '{label} должно содержать не более {max, number} {max, plural, one{символа} few{символов} many{символов} other{символов}}.',
    '{label} must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.' => '{label} должно содержать ровно {exactly, number} {exactly, plural, one{символ} few{символа} many{символов} other{символов}}.',
    /** @see Nested */
    'Nested rule without rules can be used for objects only.' => 'Правило Nested без указания правил может использоваться только для объектов.',
    'An object data set data can only have an array type.' => 'Данные в объекте должны быть массивом.',
    'Property "{path}" is not found.' => 'Свойство «{path}» не найдено.',
    /** @see Number */
    '{label} must be a number.' => '{label} должно быть числом.',
    /** @see OneOf */
    'Exactly 1 attribute from this list must be filled: {attributes}.' => 'Ровно 1 атрибут из этого списка должен быть заполнен: {attributes}.',
    /** @see Regex */
    '{label} is invalid.' => 'Значение неверно.',
    /** @see Required */
    '{label} cannot be blank.' => '{label} не может быть пустым.',
    '{label} not passed.' => '{label} не передано.',
    /** @see Subset */
    '{label} must be iterable.' => '{label} должно быть итерируемым.',
    '{label} is not a subset of acceptable values.' => '{label} не является подмножеством допустимых значений.',
    /** @see TrueValue */
    '{label} must be "{true}".' => '{label} должно быть «{true}».',
    /** @see Url */
    '{label} is not a valid URL.' => '{label} не является правильным URL.',

    // Used in multiple rules

    /**
     * @see AtLeast
     * @see Nested
     * @see OneOf
     */
    '{label} must be an array or an object.' => '{label} должно быть массивом или объектом.',
    /**
     * @see BooleanValue
     * @see TrueValue
     */
    'The allowed types are integer, float, string, boolean. {type} given.' => 'Разрешённые типы: integer, float, string, boolean. Передан {type}.',
    /**
     * @see Compare
     * @see Equal
     * @see GreaterThan
     * @see GreaterThanOrEqual
     * @see LessThan
     * @see LessThanOrEqual
     * @see NotEqual
     */
    'The allowed types are integer, float, string, boolean, null and object implementing \Stringable or \DateTimeInterface.' => 'Разрешённые типы: integer, float, string, boolean, null и объект, реализующий интерфейс \Stringable или \DateTimeInterface.',
    'The attribute value returned from a custom data set must have one of the following types: integer, float, string, boolean, null or an object implementing \Stringable interface or \DateTimeInterface.' => 'Значение, получаемое из пользовательского набора данных, должно иметь один из следующих типов: integer, float, string, bool, null или объект, реализующий интерфейс \Stringable или \DateTimeInterface.',
    '{label} must be equal to "{targetValueOrAttribute}".' => '{label} должно быть равно «{targetValueOrAttribute}».',
    '{label} must be strictly equal to "{targetValueOrAttribute}".' => '{label} должно быть строго равно «{targetValueOrAttribute}».',
    '{label} must not be equal to "{targetValueOrAttribute}".' => '{label} не должно быть равно «{targetValueOrAttribute}».',
    '{label} must not be strictly equal to "{targetValueOrAttribute}".' => '{label} не должно быть строго равно «{targetValueOrAttribute}».',
    '{label} must be greater than "{targetValueOrAttribute}".' => '{label} должно быть больше, чем «{targetValueOrAttribute}».',
    '{label} must be greater than or equal to "{targetValueOrAttribute}".' => '{label} должно быть больше или равно «{targetValueOrAttribute}».',
    '{label} must be less than "{targetValueOrAttribute}".' => '{label} должно быть меньше, чем «{targetValueOrAttribute}».',
    '{label} must be less than or equal to "{targetValueOrAttribute}".' => '{label} должно быть меньше или равно «{targetValueOrAttribute}».',
    /**
     * @see Email
     * @see Ip
     * @see Json
     * @see Length
     * @see Regex
     * @see Url
     */
    '{label} must be a string.' => '{label} должно быть строкой.',
    /**
     * @see Number
     * @see Integer
     */
    'The allowed types are integer, float and string.' => 'Разрешённые типы: integer, float и string.',
    '{label} must be no less than {min}.' => '{label} должно быть не меньше {min}.',
    '{label} must be no greater than {max}.' => '{label} должно быть не больше {max}.',
];
