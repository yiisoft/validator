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
        'Mindestens {min, number} {min, plural, one{Attribut aus dieser Liste muss} other{Attribute aus dieser ' .
        'Liste müssen}} ausgefüllt werden für {property}: {properties}.',
    /** @see BooleanValue */
    '{Property} must be either "{true}" or "{false}".' => '{Property} muss "{true}" oder "{false}" sein.',
    /** @see Count */
    '{Property} must be an array or implement \Countable interface. {type} given.' => '{Property} muss ein Array sein oder das ' .
        '\Countable Interface implementieren. {type} übergeben.',
    '{Property} must contain at least {min, number} {min, plural, one{item} other{items}}.' => '{Property} muss ' .
        'mindestens {min, number} {min, plural, one{Eintrag} other{Einträge}} enthalten.',
    '{Property} must contain at most {max, number} {max, plural, one{item} other{items}}.' => '{Property} darf ' .
        'maximal {max, number} {max, plural, one{Eintrag} other{Einträge}} enthalten.',
    '{Property} must contain exactly {exactly, number} {exactly, plural, one{item} other{items}}.' => '{Property} ' .
        'muss genau {exactly, number} {exactly, plural, one{Eintrag} other{Einträge}} enthalten.',
    /** @see Each */
    '{Property} must be array or iterable. {type} given.' => '{Property} muss ein Array oder iterierbar sein. {type} übergeben.',
    'Every iterable key of {property} must have an integer or a string type. {type} given.' => 'Jeder iterierbarer Schlüssel von {property} muss vom Typ ' .
        'Integer oder String sein. {type} übergeben.',
    /** @see Email */
    '{Property} is not a valid email address.' => '{Property} ist keine gültige E-Mail-Adresse.',
    /** @see In */
    '{Property} is not in the list of acceptable values.' => '{Property} ist nicht in der Liste der akzeptierten ' .
        'Werte.',
    /** @see Ip */
    '{Property} must be a valid IP address.' => '{Property} muss eine gültige IP-Adresse sein.',
    '{Property} must not be an IPv4 address.' => '{Property} darf keine IPv4-Adresse sein.',
    '{Property} must not be an IPv6 address.' => '{Property} darf keine IPv6-Adresse sein.',
    '{Property} contains wrong subnet mask.' => '{Property} enthält falsche Subnetz-Maske.',
    '{Property} must be an IP address with specified subnet.' => '{Property} muss eine gültige IP-Adresse mit Subnetz sein.',
    '{Property} must not be a subnet.' => '{Property} darf kein Subnetz sein.',
    '{Property} is not in the allowed range.' => '{Property} ist nicht im erlaubten Bereich.',
    /**
     * @see IntegerType
     * @see Integer
     */
    '{Property} must be an integer.' => '{Property} muss ein Integer sein.',
    /** @see Json */
    '{Property} is not a valid JSON.' => '{Property} ist kein gültiges JSON.',
    /** @see Length */
    '{Property} must contain at least {min, number} {min, plural, one{character} other{characters}}.' =>
        '{Property} muss mindestens {min, number} {min, plural, one{Buchstabe} other{Buchstaben}} enthalten.',
    '{Property} must contain at most {max, number} {max, plural, one{character} other{characters}}.' =>
        '{Property} darf maximal {max, number} {max, plural, one{Buchstabe} other{Buchstaben}} enthalten.',
    '{Property} must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.' =>
        '{Property} muss genau {exactly, number} {exactly, plural, one{Buchstabe} other{Buchstaben}} enthalten.',
    /** @see Nested */
    'Nested rule without rules requires {property} to be an object. {type} given.' => 'Verschachtelte Regeln ohne Regeln erfordern, dass {property} ein Objekt ist. {type} übergeben.',
    'An object data set data for {property} can only have an array type. {type} given.' => 'Die Daten eines Objektdatensatzes für {property} können nur den ' .
        'Typ Array haben. {type} übergeben.',
    'Property "{path}" is not found in {property}.' => 'Eigenschaft "{path}" nicht gefunden in {property}.',
    /** @see Number */
    '{Property} must be a number.' => '{Property} muss eine Zahl sein.',
    /** @see FilledOnlyOneOf */
    'Exactly 1 property from this list must be filled for {property}: {properties}.' => 'Exakt ein Attribut aus dieser Liste ' .
        'muss gefüllt sein für {property}: {properties}.',
    /** @see Regex */
    '{Property} is invalid.' => '{Property} ist ungültig.',
    /** @see Required */
    '{Property} cannot be blank.' => '{Property} darf nicht leer sein.',
    '{Property} not passed.' => '{Property} nicht übergeben.',
    /** @see StringValue */
    '{Property} must be a string.' => '{Property} muss eine Zeichenkette sein.',
    /** @see Subset */
    '{Property} must be iterable. {type} given.' => '{Property} muss iterierbar sein. {type} übergeben.',
    '{Property} is not a subset of acceptable values.' => '{Property} ist keine Teilmenge akzeptabler Werte.',
    /** @see TrueValue */
    '{Property} must be "{true}".' => '{Property} muss "{true}" sein.',
    /** @see Url */
    '{Property} is not a valid URL.' => '{Property} ist keine gültige URL.',
    /** @see Uuid */
    'The value of {property} is not a valid UUID.' => 'Der Wert von {property} ist keine gültige UUID.',

    // Used in multiple rules

    /**
     * @see FilledAtLeast
     * @see Nested
     * @see FilledOnlyOneOf
     */
    '{Property} must be an array or an object. {type} given.' => '{Property} muss ein Array oder Object sein. {type} übergeben.',
    /**
     * @see BooleanValue
     * @see TrueValue
     */
    'The allowed types for {property} are integer, float, string, boolean. {type} given.' => 'Die erlaubten Typen für {property} sind: integer, ' .
        'float, string, boolean. Es ist aber vom Typ {type}.',
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
        'Die erlaubten Typen für {property} sind: integer, float, string, boolean, null und Objekte, die ' .
        '\Stringable oder \DateTimeInterface implementieren. {type} übergeben.',
    '{Property} returned from a custom data set must have one of the following types: integer, float, string, boolean, null or an object implementing \Stringable interface or \DateTimeInterface.' =>
        '{Property}, zurückgegeben von einem benutzerdefinierten Datensatz, muss einen der folgenden Typen haben: ' .
        'integer, float, string, boolean, null oder ein Objekt, das \Stringable oder \DateTimeInterface ' .
        'implementiert.',
    '{Property} must be equal to "{targetValueOrProperty}".' => '{Property} muss gleich "{targetValueOrProperty}" sein.',
    '{Property} must be strictly equal to "{targetValueOrProperty}".' => '{Property} muss strikt gleich ' .
        '"{targetValueOrProperty}" sein.',
    '{Property} must not be equal to "{targetValueOrProperty}".' => '{Property} darf nicht gleich ' .
        '"{targetValueOrProperty}" sein.',
    '{Property} must not be strictly equal to "{targetValueOrProperty}".' => '{Property} darf nicht strikt gleich ' .
        '"{targetValueOrProperty}" sein.',
    '{Property} must be greater than "{targetValueOrProperty}".' => '{Property} muss größer als ' .
        '"{targetValueOrProperty}" sein.',
    '{Property} must be greater than or equal to "{targetValueOrProperty}".' => '{Property} muss größer als oder gleich ' .
        '"{targetValueOrProperty}" sein.',
    '{Property} must be less than "{targetValueOrProperty}".' => '{Property} muss kleiner als "{targetValueOrProperty}" ' .
        'sein.',
    '{Property} must be less than or equal to "{targetValueOrProperty}".' => '{Property} muss kleiner als oder gleich ' .
        '"{targetValueOrProperty}" sein.',
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
    '{Property} must be a string. {type} given.' => '{Property} muss eine Zeichenkette sein. {type} übergeben.',
    /**
     * @see Number
     * @see Integer
     */
    'The allowed types for {property} are integer, float and string. {type} given.' => 'Die erlaubten Typen für {property} sind: integer, float und string. {type} übergeben.',
    '{Property} must be no less than {min}.' => '{Property} darf nicht kleiner als {min} sein.',
    '{Property} must be no greater than {max}.' => '{Property} darf nicht größer als {max} sein.',

    /**
     * @see \Yiisoft\Validator\Rule\Date\Date
     * @see \Yiisoft\Validator\Rule\Date\DateTime
     * @see \Yiisoft\Validator\Rule\Date\Time
     */
    '{Property} must be no earlier than {limit}.' => '{Property} darf nicht früher als {limit} sein.',
    '{Property} must be no later than {limit}.' => '{Property} darf nicht später als {limit} sein.',

    /**
     * @see \Yiisoft\Validator\Rule\Date\Date
     * @see \Yiisoft\Validator\Rule\Date\DateTime
     */
    '{Property} must be a date.' => '{Property} muss ein Datum sein.',

    /**
     * @see \Yiisoft\Validator\Rule\Date\Time
     */
    '{Property} must be a time.' => '{Property} muss eine Uhrzeit sein.',

    /** @see UniqueIterable */
    '{Property} must be array or iterable.' => '{Property} muss ein Array oder iterierbar sein.',
    'The allowed types for iterable\'s item values of {property} are integer, float, string, boolean and object implementing \Stringable or \DateTimeInterface.' =>
        'Die erlaubten Typen für die Elementwerte von {property} sind: integer, float, string, boolean und Objekte, die \Stringable oder \DateTimeInterface implementieren.',
    'All iterable items of {property} must have the same type.' =>
        'Alle iterierbaren Elemente von {property} müssen denselben Typ haben.',
    'Every iterable\'s item of {property} must be unique.' => 'Jedes iterierbare Element von {property} muss einzigartig sein.',

    /** @see BooleanType */
    '{Property} must be a boolean.' => '{Property} muss ein Boolean sein.',
    /** @see FloatType */
    '{Property} must be a float.' => '{Property} muss ein Float sein.',
    /** @see AnyRule */
    'At least one of the inner rules must pass the validation.' => 'Mindestens eine der inneren Regeln muss die Validierung bestehen.',

    /** @see Image */
    '{Property} must be an image.' => '{Property} muss ein Bild sein.',
    'The width of {property} must be exactly {exactly, number} {exactly, plural, one{pixel} other{pixels}}.' =>
        'Die Breite von {property} muss genau {exactly, number} {exactly, plural, one{Pixel} other{Pixel}} betragen.',
    'The height of {property} must be exactly {exactly, number} {exactly, plural, one{pixel} other{pixels}}.' =>
        'Die Höhe von {property} muss genau {exactly, number} {exactly, plural, one{Pixel} other{Pixel}} betragen.',
    'The width of {property} cannot be smaller than {limit, number} {limit, plural, one{pixel} other{pixels}}.' =>
        'Die Breite von {property} darf nicht kleiner als {limit, number} {limit, plural, one{Pixel} other{Pixel}} sein.',
    'The height of {property} cannot be smaller than {limit, number} {limit, plural, one{pixel} other{pixels}}.' =>
        'Die Höhe von {property} darf nicht kleiner als {limit, number} {limit, plural, one{Pixel} other{Pixel}} sein.',
    'The width of {property} cannot be larger than {limit, number} {limit, plural, one{pixel} other{pixels}}.' =>
        'Die Breite von {property} darf nicht größer als {limit, number} {limit, plural, one{Pixel} other{Pixel}} sein.',
    'The height of {property} cannot be larger than {limit, number} {limit, plural, one{pixel} other{pixels}}.' =>
        'Die Höhe von {property} darf nicht größer als {limit, number} {limit, plural, one{Pixel} other{Pixel}} sein.',
    'The aspect ratio of {property} must be {aspectRatioWidth, number}:{aspectRatioHeight, number} with margin {aspectRatioMargin, number}%.' =>
        'Das Seitenverhältnis von {property} muss {aspectRatioWidth, number}:{aspectRatioHeight, number} mit einer Toleranz von {aspectRatioMargin, number}% betragen.',
];
