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
use Yiisoft\Validator\Rule\Image\Image;
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
    'At least {min, number} {min, plural, one{property} other{properties}} from this list must be filled for {property}: {properties}' =>
        'Как минимум {min, number} {min, plural, one{свойство} few{свойства} many{свойства} other{свойства}} из этого списка {min, plural, one{должен} few{должны} many{должны} other{должны}} быть заполнены для {property}: {properties}.',
    /** @see BooleanValue */
    '{Property} must be either "{true}" or "{false}".' => '{Property} должно быть «{true}» или «{false}».',
    /** @see Count */
    '{Property} must be an array or implement \Countable interface. {type} given.' =>
        '{Property} должно быть массивом или объектом, реализующим интерфейс \Countable. Передан {type}.',
    '{Property} must contain at least {min, number} {min, plural, one{item} other{items}}.' => '{Property} должно содержать как минимум {min, number} {min, plural, one{элемент} few{элемента} many{элементов} other{элементов}}.',
    '{Property} must contain at most {max, number} {max, plural, one{item} other{items}}.' => '{Property} должно содержать не более {max, number} {max, plural, one{элемента} few{элементов} many{элементов} other{элементов}}.',
    '{Property} must contain exactly {exactly, number} {exactly, plural, one{item} other{items}}.' => '{Property} должно содержать ровно {exactly, number} {exactly, plural, one{элемент} few{элемента} many{элементов} other{элементов}}.',
    /** @see Each */
    '{Property} must be array or iterable. {type} given.' =>
        '{Property} должно быть массивом или иметь псевдотип iterable. Передан {type}',
    'Every iterable key of {property} must have an integer or a string type. {type} given.' =>
        'Ключ {property} должен иметь тип integer или string. Передан {type}.',
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
    '{Property} is not a valid JSON.' => '{Property} не является валидной строкой JSON.',
    /** @see Length */
    '{Property} must contain at least {min, number} {min, plural, one{character} other{characters}}.' => '{Property} должно содержать как минимум {min, number} {min, plural, one{символ} few{символа} many{символов} other{символов}}.',
    '{Property} must contain at most {max, number} {max, plural, one{character} other{characters}}.' => '{Property} должно содержать не более {max, number} {max, plural, one{символа} few{символов} many{символов} other{символов}}.',
    '{Property} must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.' => '{Property} должно содержать ровно {exactly, number} {exactly, plural, one{символ} few{символа} many{символов} other{символов}}.',
    /** @see Nested */
    'Nested rule without rules requires {property} to be an object. {type} given.' =>
        'При правиле Nested без указания правил {property} должно быть объектом, {type} given.',
    'An object data set data for {property} can only have an array type. {type} given.' =>
        'Данные в объекте для {property} должны быть массивом. Передан {type}.',
    'Property "{path}" is not found in {property}.' => 'Свойство «{path}» не найдено в {property}.',
    /** @see Number */
    '{Property} must be a number.' => '{Property} должно быть числом.',
    /** @see OneOf */
    'Exactly 1 property from this list must be filled for {property}: {properties}.' =>
        'Ровно 1 свойство из этого списка должно быть заполнено в {property}: {properties}.',
    /** @see Regex */
    '{Property} is invalid.' => 'Значение неверно.',
    /** @see Required */
    '{Property} cannot be blank.' => '{Property} не может быть пустым.',
    '{Property} not passed.' => '{Property} не передано.',
    /** @see Subset */
    '{Property} must be iterable. {type} given.' => '{Property} должно быть итерируемым. Передан {type}.',
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
    '{Property} must be an array or an object. {type} given.' => '{Property} должно быть массивом или объектом. ' .
        'Передан {type}.',
    /**
     * @see BooleanValue
     * @see TrueValue
     */
    'The allowed types for {property} are integer, float, string, boolean. {type} given.' =>
        'Разрешённые типы для {property}: integer, float, string, boolean. Передан {type}.',
    /**
     * @see Compare
     * @see Equal
     * @see GreaterThan
     * @see GreaterThanOrEqual
     * @see LessThan
     * @see LessThanOrEqual
     * @see NotEqual
     */
    'The allowed types for {property} are integer, float, string, boolean, null and object implementing \Stringable or \DateTimeInterface. {type} given.' =>
        'Разрешённые типы для {property}: integer, float, string, boolean, null и объект, реализующий интерфейс \Stringable или \DateTimeInterface. Передан {type}.',
    '{Property} returned from a custom data set must have one of the following types: integer, float, string, boolean, null or an object implementing \Stringable interface or \DateTimeInterface. {type} given.' =>
        '{Property}, получаемое из пользовательского набора данных, должно иметь один из следующих типов: integer, float, string, bool, null или объект, реализующий интерфейс \Stringable или \DateTimeInterface. Передан {type}.',
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
    '{Property} must be a string. {type} given.' => '{Property} должно быть строкой. Передан {type}.',
    /**
     * @see Number
     * @see Integer
     */
    'The allowed types for {property} are integer, float and string. {type} given.' => 'Разрешённые типы: integer, float и string. Передан {type}.',
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
    '{Property} must be a date.' => '{Property} должно быть датой.',

    /**
     * @see \Yiisoft\Validator\Rule\Date\Time
     */
    '{Property} must be a time.' => '{Property} должно быть временем.',

    /** @see UniqueIterable */
    'The allowed types for iterable\'s item values of {property} are integer, float, string, boolean, null and object implementing \Stringable or \DateTimeInterface. {type} given.' =>
        'Разрешённые типы для значений элементов списка {property}: integer, float, string, boolean, null и объект, реализующий интерфейс \Stringable или \DateTimeInterface. Передан {type}.',
    'All iterable items of {property} must have the same type.' =>
        'Все элементы списка {property} должны иметь одинаковый тип.',
    'Every iterable\'s item must be unique.' => 'Каждый элемент списка {property} должен быть уникален.',

    /** @see BooleanType */
    '{Property} must be a boolean.' => 'Значение должно быть булевым.',
    /** @see FloatType */
    '{Property} must be a float.' => 'Значение должно быть вещественным числом.',
    /** @see AnyRule */
    'At least one of the inner rules must pass the validation.' => 'Как минимум одно из внутренних правил должно пройти валидацию',

    /** @see Image */
    '{Property} must be an image.' => '{Property} должно быть изображением.',
    'The width of {property} must be exactly {exactly, number} {exactly, plural, one{pixel} other{pixels}}.' =>
        'Ширина {propery} должна быть в точности {exactly, number} {exactly, plural, one{пиксель} other{пикселей}}',
    'The height of {property} must be exactly {exactly, number} {exactly, plural, one{pixel} other{pixels}}.' =>
        'Высота {propery} должна быть в точности {exactly, number} {exactly, plural, one{пиксель} other{пикселей}}',
    'The width of {property} cannot be smaller than {limit, number} {limit, plural, one{pixel} other{pixels}}.' =>
        'Ширина {property} не может быть меньше {limit, number} {limit, plural, one{пикселя} other{пикселей}}.',
    'The height of {property} cannot be smaller than {limit, number} {limit, plural, one{pixel} other{pixels}}.' =>
        'Высота {property} не может быть меньше {limit, number} {limit, plural, one{пикселя} other{пикселей}}.',
    'The width of {property} cannot be larger than {limit, number} {limit, plural, one{pixel} other{pixels}}.' =>
        'Ширина {property} не может быть больше {limit, number} {limit, plural, one{пикселя} other{пикселей}}.',
    'The height of {property}t cannot be larger than {limit, number} {limit, plural, one{pixel} other{pixels}}.' =>
        'Высота {property} не может быть больше {limit, number} {limit, plural, one{пикселя} other{пикселей}}.',
    'The aspect ratio of {property} must be {aspectRatioWidth, number}:{aspectRatioHeight, number} with margin {aspectRatioMargin, number}%.' =>
        'Соотношение стороно {property} должно быть {aspectRatioWidth, number}:{aspectRatioHeight, number} с отступом {aspectRatioMargin, number}%.',
];
