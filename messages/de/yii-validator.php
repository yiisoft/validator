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
    'At least {min, number} {min, plural, one{property} other{properties}} from this list must be filled' =>
        'Mindestens {min, number} {min, plural, one{Attribut aus dieser Liste muss} other{Attribute aus dieser ' .
        'Liste müssen}} ausgefüllt werden',
    /** @see BooleanValue */
    '{Property} must be either "{true}" or "{false}".' => '{Property} muss "{true}" oder "{false}" sein.',
    /** @see Count */
    '{Property} must be an array or implement \Countable interface.' => '{Property} muss ein Array sein oder das ' .
        '\Countable Interface implementieren.',
    '{Property} must contain at least {min, number} {min, plural, one{item} other{items}}.' => '{Property} muss ' .
        'mindestens {min, number} {min, plural, one{Eintrag} other{Einträge}} enthalten.',
    '{Property} must contain at most {max, number} {max, plural, one{item} other{items}}.' => '{Property} darf ' .
        'maximal {max, number} {max, plural, one{Eintrag} other{Einträge}} enthalten.',
    '{Property} must contain exactly {exactly, number} {exactly, plural, one{item} other{items}}.' => '{Property} ' .
        'muss genau {exactly, number} {exactly, plural, one{Eintrag} other{Einträge}} enthalten.',
    /** @see Each */
    '{Property} must be array or iterable.' => '{Property} muss ein Array oder iterierbar sein.',
    'Every iterable key must have an integer or a string type.' => 'Jeder iterierbarer Schlüssel muss vom Typ ' .
        'Integer oder String sein.',
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
    /** @see Integer */
    '{Property} must be an integer.' => '{Property} muss ein Integer sein.',
    /** @see Json */
    '{Property} is not JSON.' => '{Property} ist kein JSON.',
    /** @see Length */
    '{Property} must contain at least {min, number} {min, plural, one{character} other{characters}}.' =>
        '{Property} muss mindestens {min, number} {min, plural, one{Buchstabe} other{Buchstaben}} enthalten.',
    '{Property} must contain at most {max, number} {max, plural, one{character} other{characters}}.' =>
        '{Property} darf maximal {max, number} {max, plural, one{Buchstabe} other{Buchstaben}} enthalten.',
    '{Property} must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.' =>
        '{Property} muss genau {exactly, number} {exactly, plural, one{Buchstabe} other{Buchstaben}} enthalten.',
    /** @see Nested */
    'Nested rule without rules can be used for objects only.' => 'Verschachtelte Regeln ohne Regeln können nur ' .
        'für Objekte verwendet werden.',
    'An object data set data can only have an array type.' => 'Die Daten eines Objektdatensatzes können nur den ' .
        'Typ Array haben.',
    'Property "{path}" is not found.' => 'Eigenschaft "{path}" nicht gefunden.',
    /** @see Number */
    '{Property} must be a number.' => '{Property} muss eine Nummer sein.',
    /** @see OneOf */
    'Exactly 1 property from this list must be filled: {properties}.' => 'Exakt ein Attribut aus dieser Liste ' .
        'muss gefüllt sein: {properties}.',
    /** @see Regex */
    '{Property} is invalid.' => '{Property} ist ungültig.',
    /** @see Required */
    '{Property} cannot be blank.' => '{Property} darf nicht leer sein.',
    '{Property} not passed.' => '{Property} nicht übergeben.',
    /** @see Subset */
    '{Property} must be iterable.' => '{Property} muss iterierbar sein.',
    '{Property} is not a subset of acceptable values.' => '{Property} ist keine Teilmenge akzeptabler Werte.',
    /** @see TrueValue */
    '{Property} must be "{true}".' => '{Property} muss "{true}" sein.',
    /** @see Url */
    '{Property} is not a valid URL.' => '{Property} ist keine gültige URL.',

    // Used in multiple rules

    /**
     * @see AtLeast
     * @see Nested
     * @see OneOf
     */
    '{Property} must be an array or an object.' => '{Property} muss ein Array oder Object sein.',
    /**
     * @see BooleanValue
     * @see TrueValue
     */
    'The allowed types are integer, float, string, boolean. {type} given.' => 'Die erlaubten Typen sind: integer, ' .
        'float, string, boolean. Er ist aber vom Typ {type}.',
    /**
     * @see Compare
     * @see Equal
     * @see GreaterThan
     * @see GreaterThanOrEqual
     * @see LessThan
     * @see LessThanOrEqual
     * @see NotEqual
     */
    'The allowed types are integer, float, string, boolean, null and object implementing \Stringable or ' .
    '\DateTimeInterface.' => 'Die erlaubten Typen sind: integer, float, string, boolean, null und Objekte, die ' .
        '\Stringable oder \DateTimeInterface implementieren.',
    'The property value returned from a custom data set must have one of the following types: integer, float, ' .
    'string, boolean, null or an object implementing \Stringable interface or \DateTimeInterface.' => 'Der von ' .
        'einem benutzerdefinierten Datensatz zurückgegebene Attributwert muss einen der folgenden Typen haben: ' .
        'integer, float, string, boolean, null oder ein Objekte, das \Stringable oder \DateTimeInterface ' .
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
     * @see Url
     */
    '{Property} must be a string.' => '{Property} muss eine Zeichenkette sein.',
    /**
     * @see Number
     * @see Integer
     */
    'The allowed types are integer, float and string.' => 'Die erlaubte Typen sind: integer, float und string.',
    '{Property} must be no less than {min}.' => '{Property} darf nicht kleiner als {min} sein.',
    '{Property} must be no greater than {max}.' => '{Property} darf nicht größer als {max} sein.',
];
