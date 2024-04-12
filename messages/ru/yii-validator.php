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
use Yiisoft\Validator\Rule\Unique;
use Yiisoft\Validator\Rule\Url;

return [
    // Used in single rule

    /** @see AtLeast */
    'The data must have at least "{min}" filled attributes.' => 'Данные должны содержать минимум {min, number} {min, plural, one{заполненный атрибут} few{заполненных атрибута} many{заполненных атрибутов} other{заполненных атрибута}}.',
    /** @see BooleanValue */
    'Value must be either "{true}" or "{false}".' => 'Значение должно быть «{true}» или «{false}».',
    /** @see Count */
    'This value must be an array or implement \Countable interface.' => 'Значение должно быть массивом или объектом, реализующим интерфейс \Countable.',
    'This value must contain at least {min, number} {min, plural, one{item} other{items}}.' => 'Значение должно содержать как минимум {min, number} {min, plural, one{элемент} few{элемента} many{элементов} other{элементов}}.',
    'This value must contain at most {max, number} {max, plural, one{item} other{items}}.' => 'Значение должно содержать не более {max, number} {max, plural, one{элемента} few{элементов} many{элементов} other{элементов}}.',
    'This value must contain exactly {exactly, number} {exactly, plural, one{item} other{items}}.' => 'Значение должно содержать ровно {exactly, number} {exactly, plural, one{элемент} few{элемента} many{элементов} other{элементов}}.',
    /**
     * @see Each
     * @see Unique
     */
    'Value must be array or iterable.' => 'Значение должно быть массивом или иметь псевдотип iterable.',
    'Every iterable key must have an integer or a string type.' => 'Ключ должен иметь тип integer или string.',
    /** @see Email */
    'This value is not a valid email address.' => 'Значение не является правильным адресом электронной почты.',
    /** @see In */
    'This value is not in the list of acceptable values.' => 'Это значение не в списке допустимых значений.',
    /** @see Ip */
    'Must be a valid IP address.' => 'Должно быть правильным IP-адресом.',
    'Must not be an IPv4 address.' => 'Не должно быть IPv4-адресом.',
    'Must not be an IPv6 address.' => 'Не должно быть IPv6-адресом.',
    'Contains wrong subnet mask.' => 'Содержит неверную маску подсети.',
    'Must be an IP address with specified subnet.' => 'Должно быть IP адресом с подсетью.',
    'Must not be a subnet.' => 'Не должно быть подсетью.',
    'Is not in the allowed range.' => 'Не входит в список разрешенных диапазонов адресов.',
    /**
     * @see IntegerType
     * @see Integer
     */
    'Value must be an integer.' => 'Значение должно быть целым числом.',
    /** @see Json */
    'The value is not JSON.' => 'Значение не является строкой JSON.',
    /** @see Length */
    'This value must contain at least {min, number} {min, plural, one{character} other{characters}}.' => 'Значение должно содержать как минимум {min, number} {min, plural, one{символ} few{символа} many{символов} other{символов}}.',
    'This value must contain at most {max, number} {max, plural, one{character} other{characters}}.' => 'Значение должно содержать не более {max, number} {max, plural, one{символа} few{символов} many{символов} other{символов}}.',
    'This value must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.' => 'Значение должно содержать ровно {exactly, number} {exactly, plural, one{символ} few{символа} many{символов} other{символов}}.',
    /** @see Nested */
    'Nested rule without rules can be used for objects only.' => 'Правило Nested без указания правил может использоваться только для объектов.',
    'An object data set data can only have an array type.' => 'Данные в объекте должны быть массивом.',
    'Property "{path}" is not found.' => 'Свойство «{path}» не найдено.',
    /** @see Number */
    'Value must be a number.' => 'Значение должно быть числом.',
    /** @see OneOf */
    'The data must have at least 1 filled attribute.' => 'Данные должны содержать минимум 1 заполненный атрибут.',
    /** @see Regex */
    'Value is invalid.' => 'Значение неверно.',
    /** @see Required */
    'Value cannot be blank.' => 'Значение не может быть пустым.',
    'Value not passed.' => 'Значение не передано.',
    /** @see Subset */
    'Value must be iterable.' => 'Значение должно быть итерируемым.',
    'This value is not a subset of acceptable values.' => 'Это значение не является подмножеством допустимых значений.',
    /** @see TrueValue */
    'The value must be "{true}".' => 'Значение должно быть «{true}».',
    /** @see Url */
    'This value is not a valid URL.' => 'Значение не является правильным URL.',

    // Used in multiple rules

    /**
     * @see AtLeast
     * @see Nested
     * @see OneOf
     */
    'The value must be an array or an object.' => 'Значение должно быть массивом или объектом.',
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
    'Value must be equal to "{targetValueOrAttribute}".' => 'Значение должно быть равно «{targetValueOrAttribute}».',
    'Value must be strictly equal to "{targetValueOrAttribute}".' => 'Значение должно быть строго равно «{targetValueOrAttribute}».',
    'Value must not be equal to "{targetValueOrAttribute}".' => 'Значение не должно быть равно «{targetValueOrAttribute}».',
    'Value must not be strictly equal to "{targetValueOrAttribute}".' => 'Значение не должно быть строго равно «{targetValueOrAttribute}».',
    'Value must be greater than "{targetValueOrAttribute}".' => 'Значение должно быть больше, чем «{targetValueOrAttribute}».',
    'Value must be greater than or equal to "{targetValueOrAttribute}".' => 'Значение должно быть больше или равно «{targetValueOrAttribute}».',
    'Value must be less than "{targetValueOrAttribute}".' => 'Значение должно быть меньше, чем «{targetValueOrAttribute}».',
    'Value must be less than or equal to "{targetValueOrAttribute}".' => 'Значение должно быть меньше или равно «{targetValueOrAttribute}».',
    /**
     * @see Email
     * @see Ip
     * @see Json
     * @see Length
     * @see Regex
     * @see StringType
     * @see Url
     */
    'The value must be a string.' => 'Значение должно быть строкой.',
    /**
     * @see Number
     * @see Integer
     */
    'The allowed types are integer, float and string.' => 'Разрешённые типы: integer, float и string.',
    'Value must be no less than {min}.' => 'Значение должно быть не меньше {min}.',
    'Value must be no greater than {max}.' => 'Значение должно быть не больше {max}.',

    /**
     * @see \Yiisoft\Validator\Rule\Date\Date
     * @see \Yiisoft\Validator\Rule\Date\DateTime
     * @see \Yiisoft\Validator\Rule\Date\Time
     */
    'The value must be no early than {limit}.' => 'Значение должно быть не ранее {limit}.',
    'The value must be no late than {limit}.' => 'Значение должно быть не позднее {limit}.',

    /**
     * @see \Yiisoft\Validator\Rule\Date\Date
     * @see \Yiisoft\Validator\Rule\Date\DateTime
     */
    'Invalid date value.' => 'Некорректное значение даты.',

    /**
     * @see \Yiisoft\Validator\Rule\Date\Time
     */
    'Invalid time value.' => 'Некорректное значение времени.',

    /** @see Unique */
    'The allowed types for iterable\'s item values are integer, float, string, boolean, null and object implementing \Stringable or \DateTimeInterface.' => 'Разрешённые типы для значений элементов списка: integer, float, string, boolean, null и объект, реализующий интерфейс \Stringable или \DateTimeInterface.',
    'Every iterable\'s item must be unique.' => 'Каждый элемент списка должен быть уникален.',

    /** @see BooleanType */
    'Value must be a boolean.' => 'Значение должно быть булевым.',
    /** @see FloatType */
    'Value must be a float.' => 'Значение должно быть вещественным числом.',
    /** @see AnyRule */
    'At least one of the inner rules must pass the validation.' => 'Как минимум одно из внутренних правил должно пройти валидацию',
];
