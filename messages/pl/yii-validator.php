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
    'At least {min, number} {min, plural, one{attribute} other{attributes}} from this list must be filled' => 'At least {min, number} {min, plural, one{attribute} other{attributes}} from this list must be filled',
    /** @see BooleanValue */
    '{Attribute} must be either "{true}" or "{false}".' => '{Attribute} wynosić "{true}" albo "{false}".',
    /** @see Count */
    '{Attribute} must be an array or implement \Countable interface.' => '{Attribute} być tablicą lub implementacją interfejsu \Countable.',
    '{Attribute} must contain at least {min, number} {min, plural, one{item} other{items}}.' => '{Attribute} zawierać co najmniej {min, number} {min, plural, one{element} few{elementy} many{elementów} other{elementu}}.',
    '{Attribute} must contain at most {max, number} {max, plural, one{item} other{items}}.' => '{Attribute} zawierać co najwyżej {max, number} {max, plural, one{element} few{elementy} many{elementów} other{elementu}}.',
    '{Attribute} must contain exactly {exactly, number} {exactly, plural, one{item} other{items}}.' => '{Attribute} zawierać dokładnie {exactly, number} {exactly, plural, one{element} few{elementy} many{elementów} other{elementu}}.',
    /** @see Each */
    '{Attribute} must be array or iterable.' => '{Attribute} być typu tablicowego lub iteracyjnego.',
    'Every iterable key must have an integer or a string type.' => 'Każdy klucz iterowalny musi być typu integer albo string.',
    /** @see Email */
    '{Attribute} is not a valid email address.' => 'Ta wartość nie jest prawidłowym adresem e-mail.',
    /** @see In */
    '{Attribute} is not in the list of acceptable values.' => 'Ta wartość nie znajduje się na liście dopuszczalnych wartości.',
    /** @see Ip */
    '{Attribute} must be a valid IP address.' => '{Attribute} musi to być prawidłowy adres IP.',
    '{Attribute} must not be an IPv4 address.' => '{Attribute} nie może to być adres IPv4.',
    '{Attribute} must not be an IPv6 address.' => '{Attribute} nie może to być adres IPv6.',
    '{Attribute} contains wrong subnet mask.' => '{Attribute} zawiera niewłaściwą maskę podsieci.',
    '{Attribute} must be an IP address with specified subnet.' => '{Attribute} musi to być adres IP z określoną podsiecią.',
    '{Attribute} must not be a subnet.' => '{Attribute} nie może to być podsieć.',
    '{Attribute} is not in the allowed range.' => '{Attribute} nie mieści się w dozwolonym zakresie.',
    /** @see Integer */
    '{Attribute} must be an integer.' => '{Attribute} być liczbą całkowitą.',
    /** @see Json */
    '{Attribute} is not JSON.' => 'Wartość nie jest w formacie JSON.',
    /** @see Length */
    '{Attribute} must contain at least {min, number} {min, plural, one{character} other{characters}}.' => '{Attribute} zawierać co najmniej {min, number} {min, plural, one{znak} few{znaki} many{znaków} other{znaku}}.',
    '{Attribute} must contain at most {max, number} {max, plural, one{character} other{characters}}.' => '{Attribute} zawierać co najwyżej {max, number} {max, plural, one{znak} few{znaki} many{znaków} other{znaku}}.',
    '{Attribute} must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.' => '{Attribute} zawierać co najmniej {exactly, number} {exactly, plural, one{znak} few{znaki} many{znaków} other{znaku}}.',
    /** @see Nested */
    'Nested rule without rules can be used for objects only.' => 'Zagnieżdżona reguła bez reguł może być używana tylko dla obiektów.',
    'An object data set data can only have an array type.' => 'Dane zestawu danych obiektu mogą zawierać tylko typ tablicowy.',
    'Property "{path}" is not found.' => 'Właściwość "{path}" nie została znaleziona.',
    /** @see Number */
    '{Attribute} must be a number.' => '{Attribute} być liczbą.',
    /** @see OneOf */
    'Exactly 1 attribute from this list must be filled: {attributes}.' => 'Exactly 1 attribute from this list must be filled: {attributes}.',
    /** @see Regex */
    '{Attribute} is invalid.' => 'Wartość jest nieprawidłowa.',
    /** @see Required */
    '{Attribute} cannot be blank.' => 'Wartość nie może być pusta.',
    '{Attribute} not passed.' => 'Wartość nie została przekazana.',
    /** @see Subset */
    '{Attribute} must be iterable.' => '{Attribute} być iterowalna.',
    '{Attribute} is not a subset of acceptable values.' => 'Ta wartość nie jest podzbiorem dopuszczalnych wartości.',
    /** @see TrueValue */
    '{Attribute} must be "{true}".' => '{Attribute} być "{true}".',
    /** @see Url */
    '{Attribute} is not a valid URL.' => 'Ta wartość nie jest prawidłowym adresem URL.',

    // Used in multiple rules

    /**
     * @see AtLeast
     * @see Nested
     * @see OneOf
     */
    '{Attribute} must be an array or an object.' => '{Attribute} być tablicą lub obiektem.',
    /**
     * @see BooleanValue
     * @see TrueValue
     */
    'The allowed types are integer, float, string, boolean. {type} given.' => 'Dozwolone typy to integer, float, string, boolean. Podano {type}.',
    /**
     * @see Compare
     * @see Equal
     * @see GreaterThan
     * @see GreaterThanOrEqual
     * @see LessThan
     * @see LessThanOrEqual
     * @see NotEqual
     */
    'The allowed types are integer, float, string, boolean, null and object implementing \Stringable interface or \DateTimeInterface.' => 'Dozwolone typy to integer, float, string, boolean i null.', // TODO: Actualize
    'The attribute value returned from a custom data set must have one of the following types: integer, float, string, boolean, null or an object implementing \Stringable interface or \DateTimeInterface.' => 'Wartość atrybutu zwrócona z niestandardowego zestawu danych musi być typu skalarnego.', // TODO: Actualize
    '{Attribute} must be equal to "{targetValueOrAttribute}".' => '{Attribute} być równa "{targetValueOrAttribute}".',
    '{Attribute} must be strictly equal to "{targetValueOrAttribute}".' => '{Attribute} być równa "{targetValueOrAttribute}".', // TODO: Actualize
    '{Attribute} must not be equal to "{targetValueOrAttribute}".' => 'Wartość nie może być równa "{targetValueOrAttribute}".',
    '{Attribute} must not be strictly equal to "{targetValueOrAttribute}".' => 'Wartość nie może być równa "{targetValueOrAttribute}".', // TODO: Actualize
    '{Attribute} must be greater than "{targetValueOrAttribute}".' => '{Attribute} być większa niż "{targetValueOrAttribute}".',
    '{Attribute} must be greater than or equal to "{targetValueOrAttribute}".' => '{Attribute} być równa lub większa od "{targetValueOrAttribute}".',
    '{Attribute} must be less than "{targetValueOrAttribute}".' => '{Attribute} być mniejsza niż "{targetValueOrAttribute}".',
    '{Attribute} must be less than or equal to "{targetValueOrAttribute}".' => '{Attribute} być równa lub mniejsza od "{targetValueOrAttribute}".',
    /**
     * @see Email
     * @see Ip
     * @see Json
     * @see Length
     * @see Regex
     * @see Url
     */
    '{Attribute} must be a string.' => '{Attribute} być tekstem.',
    /**
     * @see Number
     * @see Integer
     */
    'The allowed types are integer, float and string.' => 'Dozwolone typy to integer, float i string.',
    '{Attribute} must be no less than {min}.' => 'Wartość nie może być mniejsza niż {min}.',
    '{Attribute} must be no greater than {max}.' => 'Wartość nie może być większa niż {max}.',
];
