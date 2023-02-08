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
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Subset;
use Yiisoft\Validator\Rule\TrueValue;
use Yiisoft\Validator\Rule\Url;

return [
    // Used in single rule

    /** @see AtLeast */
    'The data must have at least "{min}" filled attributes.' => 'Dane muszą mieć co najmniej "{min}" wypełnionych atrybutów.',
    /** @see BooleanValue */
    'Value must be either "{true}" or "{false}".' => 'Wartość musi wynosić "{true}" albo "{false}".',
    /** @see Count */
    'This value must be an array or implement \Countable interface.' => 'Ta wartość musi być tablicą lub implementacją interfejsu \Countable.',
    'This value must contain at least {min, number} {min, plural, one{item} other{items}}.' => 'Ta wartość musi zawierać co najmniej {min, number} {min, plural, one{element} few{elementy} many{elementów} other{elementu}}.',
    'This value must contain at most {max, number} {max, plural, one{item} other{items}}.' => 'Ta wartość musi zawierać co najwyżej {max, number} {max, plural, one{element} few{elementy} many{elementów} other{elementu}}.',
    'This value must contain exactly {exactly, number} {exactly, plural, one{item} other{items}}.' => 'Ta wartość musi zawierać dokładnie {exactly, number} {exactly, plural, one{element} few{elementy} many{elementów} other{elementu}}.',
    /** @see Each */
    'Value must be array or iterable.' => 'Wartość musi być typu tablicowego lub iteracyjnego.',
    'Every iterable key must have an integer or a string type.' => 'Każdy klucz iterowalny musi być typu integer albo string.',
    /** @see Email */
    'This value is not a valid email address.' => 'Ta wartość nie jest prawidłowym adresem e-mail.',
    /** @see In */
    'This value is not in the list of acceptable values.' => 'Ta wartość nie znajduje się na liście dopuszczalnych wartości.',
    /** @see Ip */
    'Must be a valid IP address.' => 'Musi to być prawidłowy adres IP.',
    'Must not be an IPv4 address.' => 'Nie może to być adres IPv4.',
    'Must not be an IPv6 address.' => 'Nie może to być adres IPv6.',
    'Contains wrong subnet mask.' => 'Zawiera niewłaściwą maskę podsieci.',
    'Must be an IP address with specified subnet.' => 'Musi to być adres IP z określoną podsiecią.',
    'Must not be a subnet.' => 'Nie może to być podsieć.',
    'Is not in the allowed range.' => 'Nie mieści się w dozwolonym zakresie.',
    /** @see Integer */
    'Value must be an integer.' => 'Wartość musi być liczbą całkowitą.',
    /** @see Json */
    'The value is not JSON.' => 'Wartość nie jest w formacie JSON.',
    /** @see Length */
    'This value must contain at least {min, number} {min, plural, one{character} other{characters}}.' => 'Ta wartość musi zawierać co najmniej {min, number} {min, plural, one{znak} few{znaki} many{znaków} other{znaku}}.',
    'This value must contain at most {max, number} {max, plural, one{character} other{characters}}.' => 'Ta wartość musi zawierać co najwyżej {max, number} {max, plural, one{znak} few{znaki} many{znaków} other{znaku}}.',
    'This value must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.' => 'Ta wartość musi zawierać co najmniej {exactly, number} {exactly, plural, one{znak} few{znaki} many{znaków} other{znaku}}.',
    /** @see Nested */
    'Nested rule without rules can be used for objects only.' => 'Zagnieżdżona reguła bez reguł może być używana tylko dla obiektów.',
    'An object data set data can only have an array or an object type.' => 'Dane zestawu danych obiektu mogą zawierać tylko typ tablicowy lub obiektowy.',
    'Property "{path}" is not found.' => 'Właściwość "{path}" nie została znaleziona.',
    /** @see Number */
    'Value must be a number.' => 'Wartość musi być liczbą.',
    /** @see Regex */
    'Value is invalid.' => 'Wartość jest nieprawidłowa,',
    /** @see Required */
    'Value cannot be blank.' => 'Wartość nie może być pusta.',
    'Value not passed.' => 'Wartość nie została przekazana.',
    /** @see Subset */
    'Value must be iterable.' => 'Wartość musi być iterowalna.',
    'This value is not a subset of acceptable values.' => 'Ta wartość nie jest podzbiorem dopuszczalnych wartości.',
    /** @see TrueValue */
    'The value must be "{true}".' => 'Wartość musi być "{true}".',
    /** @see Url */
    'This value is not a valid URL.' => 'Ta wartość nie jest prawidłowym adresem URL.',

    // Used in multiple rules

    /**
     * @see AtLeast
     * @see Nested
     */
    'The value must be an array or an object.' => 'Wartość musi być tablicą lub obiektem.',
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
    'The allowed types are integer, float, string, boolean and null.' => 'Dozwolone typy to integer, float, string, boolean i null.',
    'The attribute value returned from a custom data set must have a scalar type or be null.' => 'Wartość atrybutu zwrócona z niestandardowego zestawu danych musi być typu skalarnego.', // TODO: Actualize
    'Value must be equal to "{targetValueOrAttribute}".' => 'Wartość musi być równa "{targetValueOrAttribute}".',
    'Value must be strictly equal to "{targetValueOrAttribute}".' => 'Wartość musi być równa "{targetValueOrAttribute}".', // TODO: Actualize
    'Value must not be equal to "{targetValueOrAttribute}".' => 'Wartość nie może być równa "{targetValueOrAttribute}".',
    'Value must not be strictly equal to "{targetValueOrAttribute}".' => 'Wartość nie może być równa "{targetValueOrAttribute}".', // TODO: Actualize
    'Value must be greater than "{targetValueOrAttribute}".' => 'Wartość musi być większa niż "{targetValueOrAttribute}".',
    'Value must be greater than or equal to "{targetValueOrAttribute}".' => 'Wartość musi być równa lub większa od "{targetValueOrAttribute}".',
    'Value must be less than "{targetValueOrAttribute}".' => 'Wartość musi być mniejsza niż "{targetValueOrAttribute}".',
    'Value must be less than or equal to "{targetValueOrAttribute}".' => 'Wartość musi być równa lub mniejsza od "{targetValueOrAttribute}".',
    /**
     * @see Email
     * @see Ip
     * @see Json
     * @see Length
     * @see Regex
     * @see Url
     */
    'The value must be a string.' => 'Wartość musi być tekstem.',
    /**
     * @see Number
     * @see Integer
     */
    'The allowed types are integer, float and string.' => 'Dozwolone typy to integer, float i string.',
    'Value must be no less than {min}.' => 'Wartość nie może być mniejsza niż {min}.',
    'Value must be no greater than {max}.' => 'Wartość nie może być większa niż {max}.',
];
