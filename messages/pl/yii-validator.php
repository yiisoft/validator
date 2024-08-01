<?php

declare(strict_types=1);

use Yiisoft\Validator\Rule\FilledAtLeast;
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
use Yiisoft\Validator\Rule\FilledOnlyOneOf;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Subset;
use Yiisoft\Validator\Rule\TrueValue;
use Yiisoft\Validator\Rule\Url;

return [
    // Used in single rule

    /** @see FilledAtLeast */
    'At least {min, number} {min, plural, one{property} other{properties}} from this list must be filled' => 'At least {min, number} {min, plural, one{property} other{properties}} from this list must be filled',
    /** @see BooleanValue */
    '{Property} must be either "{true}" or "{false}".' => '{Property} wynosić "{true}" albo "{false}".',
    /** @see Count */
    '{Property} must be an array or implement \Countable interface.' => '{Property} być tablicą lub implementacją interfejsu \Countable.',
    '{Property} must contain at least {min, number} {min, plural, one{item} other{items}}.' => '{Property} zawierać co najmniej {min, number} {min, plural, one{element} few{elementy} many{elementów} other{elementu}}.',
    '{Property} must contain at most {max, number} {max, plural, one{item} other{items}}.' => '{Property} zawierać co najwyżej {max, number} {max, plural, one{element} few{elementy} many{elementów} other{elementu}}.',
    '{Property} must contain exactly {exactly, number} {exactly, plural, one{item} other{items}}.' => '{Property} zawierać dokładnie {exactly, number} {exactly, plural, one{element} few{elementy} many{elementów} other{elementu}}.',
    /** @see Each */
    '{Property} must be array or iterable.' => '{Property} być typu tablicowego lub iteracyjnego.',
    'Every iterable key must have an integer or a string type.' => 'Każdy klucz iterowalny musi być typu integer albo string.',
    /** @see Email */
    '{Property} is not a valid email address.' => 'Ta wartość nie jest prawidłowym adresem e-mail.',
    /** @see In */
    '{Property} is not in the list of acceptable values.' => 'Ta wartość nie znajduje się na liście dopuszczalnych wartości.',
    /** @see Ip */
    '{Property} must be a valid IP address.' => '{Property} musi to być prawidłowy adres IP.',
    '{Property} must not be an IPv4 address.' => '{Property} nie może to być adres IPv4.',
    '{Property} must not be an IPv6 address.' => '{Property} nie może to być adres IPv6.',
    '{Property} contains wrong subnet mask.' => '{Property} zawiera niewłaściwą maskę podsieci.',
    '{Property} must be an IP address with specified subnet.' => '{Property} musi to być adres IP z określoną podsiecią.',
    '{Property} must not be a subnet.' => '{Property} nie może to być podsieć.',
    '{Property} is not in the allowed range.' => '{Property} nie mieści się w dozwolonym zakresie.',
    /** @see Integer */
    '{Property} must be an integer.' => '{Property} być liczbą całkowitą.',
    /** @see Json */
    '{Property} is not JSON.' => 'Wartość nie jest w formacie JSON.',
    /** @see Length */
    '{Property} must contain at least {min, number} {min, plural, one{character} other{characters}}.' => '{Property} zawierać co najmniej {min, number} {min, plural, one{znak} few{znaki} many{znaków} other{znaku}}.',
    '{Property} must contain at most {max, number} {max, plural, one{character} other{characters}}.' => '{Property} zawierać co najwyżej {max, number} {max, plural, one{znak} few{znaki} many{znaków} other{znaku}}.',
    '{Property} must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.' => '{Property} zawierać co najmniej {exactly, number} {exactly, plural, one{znak} few{znaki} many{znaków} other{znaku}}.',
    /** @see Nested */
    'Nested rule without rules can be used for objects only.' => 'Zagnieżdżona reguła bez reguł może być używana tylko dla obiektów.',
    'An object data set data can only have an array type.' => 'Dane zestawu danych obiektu mogą zawierać tylko typ tablicowy.',
    'Property "{path}" is not found.' => 'Właściwość "{path}" nie została znaleziona.',
    /** @see Number */
    '{Property} must be a number.' => '{Property} być liczbą.',
    /** @see FilledOnlyOneOf */
    'Exactly 1 property from this list must be filled: {properties}.' => 'Exactly 1 property from this list must be filled: {properties}.',
    /** @see Regex */
    '{Property} is invalid.' => 'Wartość jest nieprawidłowa.',
    /** @see Required */
    '{Property} cannot be blank.' => 'Wartość nie może być pusta.',
    '{Property} not passed.' => 'Wartość nie została przekazana.',
    /** @see Subset */
    '{Property} must be iterable.' => '{Property} być iterowalna.',
    '{Property} is not a subset of acceptable values.' => 'Ta wartość nie jest podzbiorem dopuszczalnych wartości.',
    /** @see TrueValue */
    '{Property} must be "{true}".' => '{Property} być "{true}".',
    /** @see Url */
    '{Property} is not a valid URL.' => 'Ta wartość nie jest prawidłowym adresem URL.',

    // Used in multiple rules

    /**
     * @see FilledAtLeast
     * @see Nested
     * @see FilledOnlyOneOf
     */
    '{Property} must be an array or an object.' => '{Property} być tablicą lub obiektem.',
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
    'The property value returned from a custom data set must have one of the following types: integer, float, string, boolean, null or an object implementing \Stringable interface or \DateTimeInterface.' => 'Wartość atrybutu zwrócona z niestandardowego zestawu danych musi być typu skalarnego.', // TODO: Actualize
    '{Property} must be equal to "{targetValueOrProperty}".' => '{Property} być równa "{targetValueOrProperty}".',
    '{Property} must be strictly equal to "{targetValueOrProperty}".' => '{Property} być równa "{targetValueOrProperty}".', // TODO: Actualize
    '{Property} must not be equal to "{targetValueOrProperty}".' => 'Wartość nie może być równa "{targetValueOrProperty}".',
    '{Property} must not be strictly equal to "{targetValueOrProperty}".' => 'Wartość nie może być równa "{targetValueOrProperty}".', // TODO: Actualize
    '{Property} must be greater than "{targetValueOrProperty}".' => '{Property} być większa niż "{targetValueOrProperty}".',
    '{Property} must be greater than or equal to "{targetValueOrProperty}".' => '{Property} być równa lub większa od "{targetValueOrProperty}".',
    '{Property} must be less than "{targetValueOrProperty}".' => '{Property} być mniejsza niż "{targetValueOrProperty}".',
    '{Property} must be less than or equal to "{targetValueOrProperty}".' => '{Property} być równa lub mniejsza od "{targetValueOrProperty}".',
    /**
     * @see Email
     * @see Ip
     * @see Json
     * @see Length
     * @see Regex
     * @see Url
     */
    '{Property} must be a string.' => '{Property} być tekstem.',
    /**
     * @see Number
     * @see Integer
     */
    'The allowed types are integer, float and string.' => 'Dozwolone typy to integer, float i string.',
    '{Property} must be no less than {min}.' => 'Wartość nie może być mniejsza niż {min}.',
    '{Property} must be no greater than {max}.' => 'Wartość nie może być większa niż {max}.',
];
