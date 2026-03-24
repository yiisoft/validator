<?php

declare(strict_types=1);

use Yiisoft\Validator\Rule\AnyRule;
use Yiisoft\Validator\Rule\BooleanValue;
use Yiisoft\Validator\Rule\Compare;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Equal;
use Yiisoft\Validator\Rule\FilledAtLeast;
use Yiisoft\Validator\Rule\FilledOnlyOneOf;
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
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StringValue;
use Yiisoft\Validator\Rule\Subset;
use Yiisoft\Validator\Rule\TrueValue;
use Yiisoft\Validator\Rule\Type\BooleanType;
use Yiisoft\Validator\Rule\Type\FloatType;
use Yiisoft\Validator\Rule\Type\IntegerType;
use Yiisoft\Validator\Rule\Type\StringType;
use Yiisoft\Validator\Rule\UniqueIterable;
use Yiisoft\Validator\Rule\Url;
use Yiisoft\Validator\Rule\Uuid;

return [
    // Used in single rule

    /** @see FilledAtLeast */
    'At least {min, number} {min, plural, one{property} other{properties}} from this list must be filled for {property}: {properties}.' =>
        'Co najmniej {min, number} {min, plural, one{właściwość} few{właściwości} many{właściwości} other{właściwości}} z tej listy musi być wypełniona dla {property}: {properties}.',
    /** @see BooleanValue */
    '{Property} must be either "{true}" or "{false}".' => '{Property} musi wynosić "{true}" albo "{false}".',
    /** @see Count */
    '{Property} must be an array or implement \Countable interface. {type} given.' => '{Property} musi być tablicą lub implementacją interfejsu \Countable. Podano {type}.',
    '{Property} must contain at least {min, number} {min, plural, one{item} other{items}}.' => '{Property} musi zawierać co najmniej {min, number} {min, plural, one{element} few{elementy} many{elementów} other{elementu}}.',
    '{Property} must contain at most {max, number} {max, plural, one{item} other{items}}.' => '{Property} musi zawierać co najwyżej {max, number} {max, plural, one{element} few{elementy} many{elementów} other{elementu}}.',
    '{Property} must contain exactly {exactly, number} {exactly, plural, one{item} other{items}}.' => '{Property} musi zawierać dokładnie {exactly, number} {exactly, plural, one{element} few{elementy} many{elementów} other{elementu}}.',
    /** @see Each */
    '{Property} must be array or iterable. {type} given.' => '{Property} musi być typu tablicowego lub iteracyjnego. Podano {type}.',
    'Every iterable key of {property} must have an integer or a string type. {type} given.' => 'Każdy klucz iterowalny {property} musi być typu integer albo string. Podano {type}.',
    /** @see Email */
    '{Property} is not a valid email address.' => '{Property} nie jest prawidłowym adresem e-mail.',
    /** @see In */
    '{Property} is not in the list of acceptable values.' => '{Property} nie znajduje się na liście dopuszczalnych wartości.',
    /** @see Ip */
    '{Property} must be a valid IP address.' => '{Property} musi być prawidłowym adresem IP.',
    '{Property} must not be an IPv4 address.' => '{Property} nie może być adresem IPv4.',
    '{Property} must not be an IPv6 address.' => '{Property} nie może być adresem IPv6.',
    '{Property} contains wrong subnet mask.' => '{Property} zawiera niewłaściwą maskę podsieci.',
    '{Property} must be an IP address with specified subnet.' => '{Property} musi być adresem IP z określoną podsiecią.',
    '{Property} must not be a subnet.' => '{Property} nie może być podsiecią.',
    '{Property} is not in the allowed range.' => '{Property} nie mieści się w dozwolonym zakresie.',
    /**
     * @see IntegerType
     * @see Integer
     */
    '{Property} must be an integer.' => '{Property} musi być liczbą całkowitą.',
    /** @see Json */
    '{Property} is not a valid JSON.' => '{Property} nie jest prawidłowym JSON.',
    /** @see Length */
    '{Property} must contain at least {min, number} {min, plural, one{character} other{characters}}.' => '{Property} musi zawierać co najmniej {min, number} {min, plural, one{znak} few{znaki} many{znaków} other{znaku}}.',
    '{Property} must contain at most {max, number} {max, plural, one{character} other{characters}}.' => '{Property} musi zawierać co najwyżej {max, number} {max, plural, one{znak} few{znaki} many{znaków} other{znaku}}.',
    '{Property} must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.' => '{Property} musi zawierać dokładnie {exactly, number} {exactly, plural, one{znak} few{znaki} many{znaków} other{znaku}}.',
    /** @see Nested */
    'Nested rule without rules requires {property} to be an object. {type} given.' => 'Zagnieżdżona reguła bez reguł wymaga, aby {property} było obiektem. Podano {type}.',
    'An object data set data for {property} can only have an array type. {type} given.' => 'Dane zestawu danych obiektu dla {property} mogą zawierać tylko typ tablicowy. Podano {type}.',
    'Property "{path}" is not found in {property}.' => 'Właściwość "{path}" nie została znaleziona w {property}.',
    /** @see Number */
    '{Property} must be a number.' => '{Property} musi być liczbą.',
    /** @see FilledOnlyOneOf */
    'Exactly 1 property from this list must be filled for {property}: {properties}.' => 'Dokładnie 1 właściwość z tej listy musi być wypełniona dla {property}: {properties}.',
    /** @see Regex */
    '{Property} is invalid.' => '{Property} jest nieprawidłowe.',
    /** @see Required */
    '{Property} cannot be blank.' => '{Property} nie może być puste.',
    '{Property} not passed.' => '{Property} nie zostało przekazane.',
    /** @see StringValue */
    '{Property} must be a string.' => '{Property} musi być tekstem.',
    /** @see Subset */
    '{Property} must be iterable. {type} given.' => '{Property} musi być iterowalne. Podano {type}.',
    '{Property} is not a subset of acceptable values.' => '{Property} nie jest podzbiorem dopuszczalnych wartości.',
    /** @see TrueValue */
    '{Property} must be "{true}".' => '{Property} musi być "{true}".',
    /** @see Url */
    '{Property} is not a valid URL.' => '{Property} nie jest prawidłowym adresem URL.',
    /** @see Uuid */
    'The value of {property} is not a valid UUID.' => 'Wartość {property} nie jest prawidłowym UUID.',

    // Used in multiple rules

    /**
     * @see FilledAtLeast
     * @see Nested
     * @see FilledOnlyOneOf
     */
    '{Property} must be an array or an object. {type} given.' => '{Property} musi być tablicą lub obiektem. Podano {type}.',
    /**
     * @see BooleanValue
     * @see TrueValue
     */
    'The allowed types for {property} are integer, float, string, boolean. {type} given.' => 'Dozwolone typy dla {property} to integer, float, string, boolean. Podano {type}.',
    /**
     * @see Compare
     * @see Equal
     * @see GreaterThan
     * @see GreaterThanOrEqual
     * @see LessThan
     * @see LessThanOrEqual
     * @see NotEqual
     */
    'The allowed types for {property} are integer, float, string, boolean, null and object implementing \Stringable interface or \DateTimeInterface. {type} given.' =>
        'Dozwolone typy dla {property} to integer, float, string, boolean, null i obiekt implementujący interfejs \Stringable lub \DateTimeInterface. Podano {type}.',
    '{Property} returned from a custom data set must have one of the following types: integer, float, string, boolean, null or an object implementing \Stringable interface or \DateTimeInterface.' =>
        '{Property} zwrócone z niestandardowego zestawu danych musi mieć jeden z następujących typów: integer, float, string, boolean, null lub obiekt implementujący interfejs \Stringable lub \DateTimeInterface.',
    '{Property} must be equal to "{targetValueOrProperty}".' => '{Property} musi być równe "{targetValueOrProperty}".',
    '{Property} must be strictly equal to "{targetValueOrProperty}".' => '{Property} musi być ściśle równe "{targetValueOrProperty}".',
    '{Property} must not be equal to "{targetValueOrProperty}".' => '{Property} nie może być równe "{targetValueOrProperty}".',
    '{Property} must not be strictly equal to "{targetValueOrProperty}".' => '{Property} nie może być ściśle równe "{targetValueOrProperty}".',
    '{Property} must be greater than "{targetValueOrProperty}".' => '{Property} musi być większe niż "{targetValueOrProperty}".',
    '{Property} must be greater than or equal to "{targetValueOrProperty}".' => '{Property} musi być równe lub większe od "{targetValueOrProperty}".',
    '{Property} must be less than "{targetValueOrProperty}".' => '{Property} musi być mniejsze niż "{targetValueOrProperty}".',
    '{Property} must be less than or equal to "{targetValueOrProperty}".' => '{Property} musi być równe lub mniejsze od "{targetValueOrProperty}".',
    /**
     * @see Email
     * @see Ip
     * @see Json
     * @see Length
     * @see Regex
     * @see StringType
     * @see Url
     * @see Uuid
     */
    '{Property} must be a string. {type} given.' => '{Property} musi być tekstem. Podano {type}.',
    /**
     * @see Number
     * @see Integer
     */
    'The allowed types for {property} are integer, float and string. {type} given.' => 'Dozwolone typy dla {property} to integer, float i string. Podano {type}.',
    '{Property} must be no less than {min}.' => '{Property} nie może być mniejsze niż {min}.',
    '{Property} must be no greater than {max}.' => '{Property} nie może być większe niż {max}.',

    /**
     * @see \Yiisoft\Validator\Rule\Date\Date
     * @see \Yiisoft\Validator\Rule\Date\DateTime
     * @see \Yiisoft\Validator\Rule\Date\Time
     */
    '{Property} must be no earlier than {limit}.' => '{Property} nie może być wcześniejsze niż {limit}.',
    '{Property} must be no later than {limit}.' => '{Property} nie może być późniejsze niż {limit}.',

    /**
     * @see \Yiisoft\Validator\Rule\Date\Date
     * @see \Yiisoft\Validator\Rule\Date\DateTime
     */
    '{Property} must be a date.' => '{Property} musi być datą.',

    /**
     * @see \Yiisoft\Validator\Rule\Date\Time
     */
    '{Property} must be a time.' => '{Property} musi być godziną.',

    /** @see UniqueIterable */
    '{Property} must be array or iterable.' => '{Property} musi być tablicą lub iterowalne.',
    'The allowed types for iterable\'s item values of {property} are integer, float, string, boolean and object implementing \Stringable or \DateTimeInterface.' =>
        'Dozwolone typy dla wartości elementów iterowalnych {property} to integer, float, string, boolean i obiekt implementujący \Stringable lub \DateTimeInterface.',
    'All iterable items of {property} must have the same type.' =>
        'Wszystkie iterowalne elementy {property} muszą mieć ten sam typ.',
    'Every iterable\'s item of {property} must be unique.' => 'Każdy iterowalny element {property} musi być unikalny.',

    /** @see BooleanType */
    '{Property} must be a boolean.' => '{Property} musi być wartością logiczną.',
    /** @see FloatType */
    '{Property} must be a float.' => '{Property} musi być liczbą zmiennoprzecinkową.',
    /** @see AnyRule */
    'At least one of the inner rules must pass the validation.' => 'Co najmniej jedna z wewnętrznych reguł musi przejść walidację.',

    /** @see Image */
    '{Property} must be an image.' => '{Property} musi być obrazem.',
    'The width of {property} must be exactly {exactly, number} {exactly, plural, one{pixel} other{pixels}}.' =>
        'Szerokość {property} musi wynosić dokładnie {exactly, number} {exactly, plural, one{piksel} few{piksele} many{pikseli} other{piksela}}.',
    'The height of {property} must be exactly {exactly, number} {exactly, plural, one{pixel} other{pixels}}.' =>
        'Wysokość {property} musi wynosić dokładnie {exactly, number} {exactly, plural, one{piksel} few{piksele} many{pikseli} other{piksela}}.',
    'The width of {property} cannot be smaller than {limit, number} {limit, plural, one{pixel} other{pixels}}.' =>
        'Szerokość {property} nie może być mniejsza niż {limit, number} {limit, plural, one{piksel} few{piksele} many{pikseli} other{piksela}}.',
    'The height of {property} cannot be smaller than {limit, number} {limit, plural, one{pixel} other{pixels}}.' =>
        'Wysokość {property} nie może być mniejsza niż {limit, number} {limit, plural, one{piksel} few{piksele} many{pikseli} other{piksela}}.',
    'The width of {property} cannot be larger than {limit, number} {limit, plural, one{pixel} other{pixels}}.' =>
        'Szerokość {property} nie może być większa niż {limit, number} {limit, plural, one{piksel} few{piksele} many{pikseli} other{piksela}}.',
    'The height of {property} cannot be larger than {limit, number} {limit, plural, one{pixel} other{pixels}}.' =>
        'Wysokość {property} nie może być większa niż {limit, number} {limit, plural, one{piksel} few{piksele} many{pikseli} other{piksela}}.',
    'The aspect ratio of {property} must be {aspectRatioWidth, number}:{aspectRatioHeight, number} with margin {aspectRatioMargin, number}%.' =>
        'Proporcje {property} muszą wynosić {aspectRatioWidth, number}:{aspectRatioHeight, number} z marginesem {aspectRatioMargin, number}%.',
];
