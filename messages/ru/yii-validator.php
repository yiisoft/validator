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
use Yiisoft\Validator\Rule\Type\FloatType;
use Yiisoft\Validator\Rule\Type\IntegerType;
use Yiisoft\Validator\Rule\Type\StringType;
use Yiisoft\Validator\Rule\UniqueIterable;
use Yiisoft\Validator\Rule\Url;

return [
    // Used in single rule

    /** @see AtLeast */
    'At least {min, number} {min, plural, one{property} other{properties}} from this list must be filled' => 'Как ' .
        'минимум {min, number}' .
        '{min, plural, one{свойство} few{свойства} many{свойства} other{свойства}} из этого списка ' .
        '{min, plural, one{должен} few{должны} many{должны} other{должны}} быть ' .
        'заполнены: {properties}.',
    /** @see BooleanValue */
    '{Property} must be either "{true}" or "{false}".' => '{Property} должно быть «{true}» или «{false}».',
    /** @see Count */
    '{Property} must be an array or implement \Countable interface.' => '{Property} должно быть массивом или объектом, реализующим интерфейс \Countable.',
    '{Property} must contain at least {min, number} {min, plural, one{item} other{items}}.' => '{Property} должно содержать как минимум {min, number} {min, plural, one{элемент} few{элемента} many{элементов} other{элементов}}.',
    '{Property} must contain at most {max, number} {max, plural, one{item} other{items}}.' => '{Property} должно содержать не более {max, number} {max, plural, one{элемента} few{элементов} many{элементов} other{элементов}}.',
    '{Property} must contain exactly {exactly, number} {exactly, plural, one{item} other{items}}.' => '{Property} должно содержать ровно {exactly, number} {exactly, plural, one{элемент} few{элемента} many{элементов} other{элементов}}.',
    /** @see Each */
    '{Property} must be array or iterable.' => '{Property} должно быть массивом или иметь псевдотип iterable.',
    'Every iterable key must have an integer or a string type.' => 'Ключ должен иметь тип integer или string.',
    /** @see Email */
    '{Property} is not a valid email address.' => '{Property} не является правильным адресом электронной почты.',
    /** @see In */
    '{Property} is not in the list of acceptable values.' => '{Property} не в списке допустимых значений.',
    /** @see Ip */
    '{Property} must be a valid IP address.' => '{Property} должен быть правильным IP-адресом.',
    '{Property} must not be an IPv4 address.' => '{Property} не должен быть IPv4-адресом.',
    '{Property} must not be an IPv6 address.' => '{Property} не должен быть IPv6-адресом.',
    '{Property} contains wrong subnet mask.' => '{Property} содержит неверную маску подсети.',
    '{Property} must be an IP address with specified subnet.' => '{Property} должен быть IP адресом с подсетью.',
    '{Property} must not be a subnet.' => '{Property} не должно быть подсетью.',
    '{Property} is not in the allowed range.' => '{Property} не входит в список разрешенных диапазонов адресов.',
    /**
     * @see IntegerType
     * @see Integer
     */
    '{Property} must be an integer.' => '{Property} должно быть целым числом.',
    /** @see Json */
    '{Property} is not JSON.' => '{Property} не является строкой JSON.',
    /** @see Length */
    '{Property} must contain at least {min, number} {min, plural, one{character} other{characters}}.' => '{Property} должно содержать как минимум {min, number} {min, plural, one{символ} few{символа} many{символов} other{символов}}.',
    '{Property} must contain at most {max, number} {max, plural, one{character} other{characters}}.' => '{Property} должно содержать не более {max, number} {max, plural, one{символа} few{символов} many{символов} other{символов}}.',
    '{Property} must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.' => '{Property} должно содержать ровно {exactly, number} {exactly, plural, one{символ} few{символа} many{символов} other{символов}}.',
    /** @see Nested */
    'Nested rule without rules can be used for objects only.' => 'Правило Nested без указания правил может использоваться только для объектов.',
    'An object data set data can only have an array type.' => 'Данные в объекте должны быть массивом.',
    'Property "{path}" is not found.' => 'Свойство «{path}» не найдено.',
    /** @see Number */
    '{Property} must be a number.' => '{Property} должно быть числом.',
    /** @see OneOf */
    'Exactly 1 property from this list must be filled: {properties}.' => 'Ровно 1 свойство из этого списка должно быть заполнен: {properties}.',
    /** @see Regex */
    '{Property} is invalid.' => 'Значение неверно.',
    /** @see Required */
    '{Property} cannot be blank.' => '{Property} не может быть пустым.',
    '{Property} not passed.' => '{Property} не передано.',
    /** @see Subset */
    '{Property} must be iterable.' => '{Property} должно быть итерируемым.',
    '{Property} is not a subset of acceptable values.' => '{Property} не является подмножеством допустимых значений.',
    /** @see TrueValue */
    '{Property} must be "{true}".' => '{Property} должно быть «{true}».',
    /** @see Url */
    '{Property} is not a valid URL.' => '{Property} не является правильным URL.',

    // Used in multiple rules

    /**
     * @see AtLeast
     * @see Nested
     * @see OneOf
     */
    '{Property} must be an array or an object.' => '{Property} должно быть массивом или объектом.',
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
    'The property value returned from a custom data set must have one of the following types: integer, float, string, boolean, null or an object implementing \Stringable interface or \DateTimeInterface.' => 'Значение, получаемое из пользовательского набора данных, должно иметь один из следующих типов: integer, float, string, bool, null или объект, реализующий интерфейс \Stringable или \DateTimeInterface.',
    '{Property} must be equal to "{targetValueOrProperty}".' => '{Property} должно быть равно «{targetValueOrProperty}».',
    '{Property} must be strictly equal to "{targetValueOrProperty}".' => '{Property} должно быть строго равно «{targetValueOrProperty}».',
    '{Property} must not be equal to "{targetValueOrProperty}".' => '{Property} не должно быть равно «{targetValueOrProperty}».',
    '{Property} must not be strictly equal to "{targetValueOrProperty}".' => '{Property} не должно быть строго равно «{targetValueOrProperty}».',
    '{Property} must be greater than "{targetValueOrProperty}".' => '{Property} должно быть больше, чем «{targetValueOrProperty}».',
    '{Property} must be greater than or equal to "{targetValueOrProperty}".' => '{Property} должно быть больше или равно «{targetValueOrProperty}».',
    '{Property} must be less than "{targetValueOrProperty}".' => '{Property} должно быть меньше, чем «{targetValueOrProperty}».',
    '{Property} must be less than or equal to "{targetValueOrProperty}".' => '{Property} должно быть меньше или равно «{targetValueOrProperty}».',
    /**
     * @see Email
     * @see Ip
     * @see Json
     * @see Length
     * @see Regex
     * @see StringType
     * @see Url
     */
    '{Property} must be a string.' => '{Property} должно быть строкой.',
    /**
     * @see Number
     * @see Integer
     */
    'The allowed types are integer, float and string.' => 'Разрешённые типы: integer, float и string.',
    '{Property} must be no less than {min}.' => '{Property} должно быть не меньше {min}.',
    '{Property} must be no greater than {max}.' => '{Property} должно быть не больше {max}.',

    /**
     * @see \Yiisoft\Validator\Rule\Date\Date
     * @see \Yiisoft\Validator\Rule\Date\DateTime
     * @see \Yiisoft\Validator\Rule\Date\Time
     */
    '{Property} must be no early than {limit}.' => '{Property} должно быть не ранее {limit}.',
    '{Property} must be no late than {limit}.' => '{Property} должно быть не позднее {limit}.',

    /**
     * @see \Yiisoft\Validator\Rule\Date\Date
     * @see \Yiisoft\Validator\Rule\Date\DateTime
     */
    'Invalid date value.' => 'Некорректное значение даты.',

    /**
     * @see \Yiisoft\Validator\Rule\Date\Time
     */
    'Invalid time value.' => 'Некорректное значение времени.',

    /** @see UniqueIterable */
    'The allowed types for iterable\'s item values are integer, float, string, boolean, null and object implementing \Stringable or \DateTimeInterface.' => 'Разрешённые типы для значений элементов списка: integer, float, string, boolean, null и объект, реализующий интерфейс \Stringable или \DateTimeInterface.',
    'All iterable items must have the same type.' => 'Все элементы списка должны иметь одинаковый тип.',
    'Every iterable\'s item must be unique.' => 'Каждый элемент списка должен быть уникален.',

    /** @see BooleanType */
    'Value must be a boolean.' => 'Значение должно быть булевым.',
    /** @see FloatType */
    'Value must be a float.' => 'Значение должно быть вещественным числом.',
    /** @see AnyRule */
    'At least one of the inner rules must pass the validation.' => 'Как минимум одно из внутренних правил должно пройти валидацию',
];
