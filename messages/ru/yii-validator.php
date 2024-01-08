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
    '{attribute} must be either "{true}" or "{false}".' => '{attribute} должно быть «{true}» или «{false}».',
    /** @see Count */
    '{attribute} must be an array or implement \Countable interface.' => '{attribute} должно быть массивом или объектом, реализующим интерфейс \Countable.',
    '{attribute} must contain at least {min, number} {min, plural, one{item} other{items}}.' => '{attribute} должно содержать как минимум {min, number} {min, plural, one{элемент} few{элемента} many{элементов} other{элементов}}.',
    '{attribute} must contain at most {max, number} {max, plural, one{item} other{items}}.' => '{attribute} должно содержать не более {max, number} {max, plural, one{элемента} few{элементов} many{элементов} other{элементов}}.',
    '{attribute} must contain exactly {exactly, number} {exactly, plural, one{item} other{items}}.' => '{attribute} должно содержать ровно {exactly, number} {exactly, plural, one{элемент} few{элемента} many{элементов} other{элементов}}.',
    /** @see Each */
    '{attribute} must be array or iterable.' => '{attribute} должно быть массивом или иметь псевдотип iterable.',
    'Every iterable key must have an integer or a string type.' => 'Ключ должен иметь тип integer или string.',
    /** @see Email */
    '{attribute} is not a valid email address.' => '{attribute} не является правильным адресом электронной почты.',
    /** @see In */
    '{attribute} is not in the list of acceptable values.' => '{attribute} не в списке допустимых значений.',
    /** @see Ip */
    'Must be a valid IP address.' => 'Должно быть правильным IP-адресом.',
    'Must not be an IPv4 address.' => 'Не должно быть IPv4-адресом.',
    'Must not be an IPv6 address.' => 'Не должно быть IPv6-адресом.',
    'Contains wrong subnet mask.' => 'Содержит неверную маску подсети.',
    'Must be an IP address with specified subnet.' => 'Должно быть IP адресом с подсетью.',
    'Must not be a subnet.' => 'Не должно быть подсетью.',
    'Is not in the allowed range.' => 'Не входит в список разрешенных диапазонов адресов.',
    /** @see Integer */
    '{attribute} must be an integer.' => '{attribute} должно быть целым числом.',
    /** @see Json */
    '{attribute} is not JSON.' => '{attribute} не является строкой JSON.',
    /** @see Length */
    '{attribute} must contain at least {min, number} {min, plural, one{character} other{characters}}.' => '{attribute} должно содержать как минимум {min, number} {min, plural, one{символ} few{символа} many{символов} other{символов}}.',
    '{attribute} must contain at most {max, number} {max, plural, one{character} other{characters}}.' => '{attribute} должно содержать не более {max, number} {max, plural, one{символа} few{символов} many{символов} other{символов}}.',
    '{attribute} must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.' => '{attribute} должно содержать ровно {exactly, number} {exactly, plural, one{символ} few{символа} many{символов} other{символов}}.',
    /** @see Nested */
    'Nested rule without rules can be used for objects only.' => 'Правило Nested без указания правил может использоваться только для объектов.',
    'An object data set data can only have an array type.' => 'Данные в объекте должны быть массивом.',
    'Property "{path}" is not found.' => 'Свойство «{path}» не найдено.',
    /** @see Number */
    '{attribute} must be a number.' => '{attribute} должно быть числом.',
    /** @see OneOf */
    'Exactly 1 attribute from this list must be filled: {attributes}.' => 'Ровно 1 атрибут из этого списка должен быть заполнен: {attributes}.',
    /** @see Regex */
    '{attribute} is invalid.' => 'Значение неверно.',
    /** @see Required */
    '{attribute} cannot be blank.' => '{attribute} не может быть пустым.',
    '{attribute} not passed.' => '{attribute} не передано.',
    /** @see Subset */
    '{attribute} must be iterable.' => '{attribute} должно быть итерируемым.',
    '{attribute} is not a subset of acceptable values.' => '{attribute} не является подмножеством допустимых значений.',
    /** @see TrueValue */
    '{attribute} must be "{true}".' => '{attribute} должно быть «{true}».',
    /** @see Url */
    '{attribute} is not a valid URL.' => '{attribute} не является правильным URL.',

    // Used in multiple rules

    /**
     * @see AtLeast
     * @see Nested
     * @see OneOf
     */
    '{attribute} must be an array or an object.' => '{attribute} должно быть массивом или объектом.',
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
    '{attribute} must be equal to "{targetValueOrAttribute}".' => '{attribute} должно быть равно «{targetValueOrAttribute}».',
    '{attribute} must be strictly equal to "{targetValueOrAttribute}".' => '{attribute} должно быть строго равно «{targetValueOrAttribute}».',
    '{attribute} must not be equal to "{targetValueOrAttribute}".' => '{attribute} не должно быть равно «{targetValueOrAttribute}».',
    '{attribute} must not be strictly equal to "{targetValueOrAttribute}".' => '{attribute} не должно быть строго равно «{targetValueOrAttribute}».',
    '{attribute} must be greater than "{targetValueOrAttribute}".' => '{attribute} должно быть больше, чем «{targetValueOrAttribute}».',
    '{attribute} must be greater than or equal to "{targetValueOrAttribute}".' => '{attribute} должно быть больше или равно «{targetValueOrAttribute}».',
    '{attribute} must be less than "{targetValueOrAttribute}".' => '{attribute} должно быть меньше, чем «{targetValueOrAttribute}».',
    '{attribute} must be less than or equal to "{targetValueOrAttribute}".' => '{attribute} должно быть меньше или равно «{targetValueOrAttribute}».',
    /**
     * @see Email
     * @see Ip
     * @see Json
     * @see Length
     * @see Regex
     * @see Url
     */
    '{attribute} must be a string.' => '{attribute} должно быть строкой.',
    /**
     * @see Number
     * @see Integer
     */
    'The allowed types are integer, float and string.' => 'Разрешённые типы: integer, float и string.',
    '{attribute} must be no less than {min}.' => '{attribute} должно быть не меньше {min}.',
    '{attribute} must be no greater than {max}.' => '{attribute} должно быть не больше {max}.',
];
