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
    '{Attribute} must be either "{true}" or "{false}".' => '{Attribute} должно быть «{true}» или «{false}».',
    /** @see Count */
    '{Attribute} must be an array or implement \Countable interface.' => '{Attribute} должно быть массивом или объектом, реализующим интерфейс \Countable.',
    '{Attribute} must contain at least {min, number} {min, plural, one{item} other{items}}.' => '{Attribute} должно содержать как минимум {min, number} {min, plural, one{элемент} few{элемента} many{элементов} other{элементов}}.',
    '{Attribute} must contain at most {max, number} {max, plural, one{item} other{items}}.' => '{Attribute} должно содержать не более {max, number} {max, plural, one{элемента} few{элементов} many{элементов} other{элементов}}.',
    '{Attribute} must contain exactly {exactly, number} {exactly, plural, one{item} other{items}}.' => '{Attribute} должно содержать ровно {exactly, number} {exactly, plural, one{элемент} few{элемента} many{элементов} other{элементов}}.',
    /** @see Each */
    '{Attribute} must be array or iterable.' => '{Attribute} должно быть массивом или иметь псевдотип iterable.',
    'Every iterable key must have an integer or a string type.' => 'Ключ должен иметь тип integer или string.',
    /** @see Email */
    '{Attribute} is not a valid email address.' => '{Attribute} не является правильным адресом электронной почты.',
    /** @see In */
    '{Attribute} is not in the list of acceptable values.' => '{Attribute} не в списке допустимых значений.',
    /** @see Ip */
    '{Attribute} must be a valid IP address.' => '{Attribute} должен быть правильным IP-адресом.',
    '{Attribute} must not be an IPv4 address.' => '{Attribute} не должен быть IPv4-адресом.',
    '{Attribute} must not be an IPv6 address.' => '{Attribute} не должен быть IPv6-адресом.',
    '{Attribute} contains wrong subnet mask.' => '{Attribute} содержит неверную маску подсети.',
    '{Attribute} must be an IP address with specified subnet.' => '{Attribute} должен быть IP адресом с подсетью.',
    '{Attribute} must not be a subnet.' => '{Attribute} не должно быть подсетью.',
    '{Attribute} is not in the allowed range.' => '{Attribute} не входит в список разрешенных диапазонов адресов.',
    /** @see Integer */
    '{Attribute} must be an integer.' => '{Attribute} должно быть целым числом.',
    /** @see Json */
    '{Attribute} is not JSON.' => '{Attribute} не является строкой JSON.',
    /** @see Length */
    '{Attribute} must contain at least {min, number} {min, plural, one{character} other{characters}}.' => '{Attribute} должно содержать как минимум {min, number} {min, plural, one{символ} few{символа} many{символов} other{символов}}.',
    '{Attribute} must contain at most {max, number} {max, plural, one{character} other{characters}}.' => '{Attribute} должно содержать не более {max, number} {max, plural, one{символа} few{символов} many{символов} other{символов}}.',
    '{Attribute} must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.' => '{Attribute} должно содержать ровно {exactly, number} {exactly, plural, one{символ} few{символа} many{символов} other{символов}}.',
    /** @see Nested */
    'Nested rule without rules can be used for objects only.' => 'Правило Nested без указания правил может использоваться только для объектов.',
    'An object data set data can only have an array type.' => 'Данные в объекте должны быть массивом.',
    'Property "{path}" is not found.' => 'Свойство «{path}» не найдено.',
    /** @see Number */
    '{Attribute} must be a number.' => '{Attribute} должно быть числом.',
    /** @see OneOf */
    'Exactly 1 attribute from this list must be filled: {attributes}.' => 'Ровно 1 атрибут из этого списка должен быть заполнен: {attributes}.',
    /** @see Regex */
    '{Attribute} is invalid.' => 'Значение неверно.',
    /** @see Required */
    '{Attribute} cannot be blank.' => '{Attribute} не может быть пустым.',
    '{Attribute} not passed.' => '{Attribute} не передано.',
    /** @see Subset */
    '{Attribute} must be iterable.' => '{Attribute} должно быть итерируемым.',
    '{Attribute} is not a subset of acceptable values.' => '{Attribute} не является подмножеством допустимых значений.',
    /** @see TrueValue */
    '{Attribute} must be "{true}".' => '{Attribute} должно быть «{true}».',
    /** @see Url */
    '{Attribute} is not a valid URL.' => '{Attribute} не является правильным URL.',

    // Used in multiple rules

    /**
     * @see AtLeast
     * @see Nested
     * @see OneOf
     */
    '{Attribute} must be an array or an object.' => '{Attribute} должно быть массивом или объектом.',
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
    '{Attribute} must be equal to "{targetValueOrAttribute}".' => '{Attribute} должно быть равно «{targetValueOrAttribute}».',
    '{Attribute} must be strictly equal to "{targetValueOrAttribute}".' => '{Attribute} должно быть строго равно «{targetValueOrAttribute}».',
    '{Attribute} must not be equal to "{targetValueOrAttribute}".' => '{Attribute} не должно быть равно «{targetValueOrAttribute}».',
    '{Attribute} must not be strictly equal to "{targetValueOrAttribute}".' => '{Attribute} не должно быть строго равно «{targetValueOrAttribute}».',
    '{Attribute} must be greater than "{targetValueOrAttribute}".' => '{Attribute} должно быть больше, чем «{targetValueOrAttribute}».',
    '{Attribute} must be greater than or equal to "{targetValueOrAttribute}".' => '{Attribute} должно быть больше или равно «{targetValueOrAttribute}».',
    '{Attribute} must be less than "{targetValueOrAttribute}".' => '{Attribute} должно быть меньше, чем «{targetValueOrAttribute}».',
    '{Attribute} must be less than or equal to "{targetValueOrAttribute}".' => '{Attribute} должно быть меньше или равно «{targetValueOrAttribute}».',
    /**
     * @see Email
     * @see Ip
     * @see Json
     * @see Length
     * @see Regex
     * @see Url
     */
    '{Attribute} must be a string.' => '{Attribute} должно быть строкой.',
    /**
     * @see Number
     * @see Integer
     */
    'The allowed types are integer, float and string.' => 'Разрешённые типы: integer, float и string.',
    '{Attribute} must be no less than {min}.' => '{Attribute} должно быть не меньше {min}.',
    '{Attribute} must be no greater than {max}.' => '{Attribute} должно быть не больше {max}.',

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
];
