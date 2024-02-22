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
    'At least {min, number} {min, plural, one{attribute} other{attributes}} from this list must be filled' =>
        'Mindestens {min, number} {min, plural, one{Attribut aus dieser Liste muss} other{Attribute aus dieser ' .
        'Liste müssen}} ausgefüllt werden',
    /** @see BooleanValue */
    '{Attribute} must be either "{true}" or "{false}".' => '{Attribute} muss "{true}" oder "{false}" sein.',
    /** @see Count */
    '{Attribute} must be an array or implement \Countable interface.' => '{Attribute} muss ein Array sein oder das ' .
        '\Countable Interface implementieren.',
    '{Attribute} must contain at least {min, number} {min, plural, one{item} other{items}}.' => '{Attribute} muss ' .
        'mindestens {min, number} {min, plural, one{Eintrag} other{Einträge}} enthalten.',
    '{Attribute} must contain at most {max, number} {max, plural, one{item} other{items}}.' => '{Attribute} darf ' .
        'maximal {max, number} {max, plural, one{Eintrag} other{Einträge}} enthalten.',
    '{Attribute} must contain exactly {exactly, number} {exactly, plural, one{item} other{items}}.' => '{Attribute} ' .
        'muss genau {exactly, number} {exactly, plural, one{Eintrag} other{Einträge}} enthalten.',
    /** @see Each */
    '{Attribute} must be array or iterable.' => '{Attribute} muss ein Array oder iterierbar sein.',
    'Every iterable key must have an integer or a string type.' => 'Jeder iterierbarer Schlüssel muss vom Typ ' .
        'Integer oder String sein.',
    /** @see Email */
    '{Attribute} is not a valid email address.' => '{Attribute} ist keine gültige E-Mail-Adresse.',
    /** @see In */
    '{Attribute} is not in the list of acceptable values.' => '{Attribute} ist nicht in der Liste der akzeptierten ' .
        'Werte.',
    /** @see Ip */
    '{Attribute} must be a valid IP address.' => '{Attribute} muss eine gültige IP-Adresse sein.',
    '{Attribute} must not be an IPv4 address.' => '{Attribute} darf keine IPv4-Adresse sein.',
    '{Attribute} must not be an IPv6 address.' => '{Attribute} darf keine IPv6-Adresse sein.',
    '{Attribute} contains wrong subnet mask.' => '{Attribute} enthält falsche Subnetz-Maske.',
    '{Attribute} must be an IP address with specified subnet.' => '{Attribute} muss eine gültige IP-Adresse mit Subnetz sein.',
    '{Attribute} must not be a subnet.' => '{Attribute} darf kein Subnetz sein.',
    '{Attribute} is not in the allowed range.' => '{Attribute} ist nicht im erlaubten Bereich.',
    /** @see Integer */
    '{Attribute} must be an integer.' => '{Attribute} muss ein Integer sein.',
    /** @see Json */
    '{Attribute} is not JSON.' => '{Attribute} ist kein JSON.',
    /** @see Length */
    '{Attribute} must contain at least {min, number} {min, plural, one{character} other{characters}}.' =>
        '{Attribute} muss mindestens {min, number} {min, plural, one{Buchstabe} other{Buchstaben}} enthalten.',
    '{Attribute} must contain at most {max, number} {max, plural, one{character} other{characters}}.' =>
        '{Attribute} darf maximal {max, number} {max, plural, one{Buchstabe} other{Buchstaben}} enthalten.',
    '{Attribute} must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.' =>
        '{Attribute} muss genau {exactly, number} {exactly, plural, one{Buchstabe} other{Buchstaben}} enthalten.',
    /** @see Nested */
    'Nested rule without rules can be used for objects only.' => 'Verschachtelte Regeln ohne Regeln können nur ' .
        'für Objekte verwendet werden.',
    'An object data set data can only have an array type.' => 'Die Daten eines Objektdatensatzes können nur den ' .
        'Typ Array haben.',
    'Property "{path}" is not found.' => 'Eigenschaft "{path}" nicht gefunden.',
    /** @see Number */
    '{Attribute} must be a number.' => '{Attribute} muss eine Nummer sein.',
    /** @see OneOf */
    'Exactly 1 attribute from this list must be filled: {attributes}.' => 'Exakt ein Attribut aus dieser Liste ' .
        'muss gefüllt sein: {attributes}.',
    /** @see Regex */
    '{Attribute} is invalid.' => '{Attribute} ist ungültig.',
    /** @see Required */
    '{Attribute} cannot be blank.' => '{Attribute} darf nicht leer sein.',
    '{Attribute} not passed.' => '{Attribute} nicht übergeben.',
    /** @see Subset */
    '{Attribute} must be iterable.' => '{Attribute} muss iterierbar sein.',
    '{Attribute} is not a subset of acceptable values.' => '{Attribute} ist keine Teilmenge akzeptabler Werte.',
    /** @see TrueValue */
    '{Attribute} must be "{true}".' => '{Attribute} muss "{true}" sein.',
    /** @see Url */
    '{Attribute} is not a valid URL.' => '{Attribute} ist keine gültige URL.',

    // Used in multiple rules

    /**
     * @see AtLeast
     * @see Nested
     * @see OneOf
     */
    '{Attribute} must be an array or an object.' => '{Attribute} muss ein Array oder Object sein.',
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
    'The attribute value returned from a custom data set must have one of the following types: integer, float, ' .
    'string, boolean, null or an object implementing \Stringable interface or \DateTimeInterface.' => 'Der von ' .
        'einem benutzerdefinierten Datensatz zurückgegebene Attributwert muss einen der folgenden Typen haben: ' .
        'integer, float, string, boolean, null oder ein Objekte, das \Stringable oder \DateTimeInterface ' .
        'implementiert.',
    '{Attribute} must be equal to "{targetValueOrAttribute}".' => '{Attribute} muss gleich "{targetValueOrAttribute}" sein.',
    '{Attribute} must be strictly equal to "{targetValueOrAttribute}".' => '{Attribute} muss strikt gleich ' .
        '"{targetValueOrAttribute}" sein.',
    '{Attribute} must not be equal to "{targetValueOrAttribute}".' => '{Attribute} darf nicht gleich ' .
        '"{targetValueOrAttribute}" sein.',
    '{Attribute} must not be strictly equal to "{targetValueOrAttribute}".' => '{Attribute} darf nicht strikt gleich ' .
        '"{targetValueOrAttribute}" sein.',
    '{Attribute} must be greater than "{targetValueOrAttribute}".' => '{Attribute} muss größer als ' .
        '"{targetValueOrAttribute}" sein.',
    '{Attribute} must be greater than or equal to "{targetValueOrAttribute}".' => '{Attribute} muss größer als oder gleich ' .
        '"{targetValueOrAttribute}" sein.',
    '{Attribute} must be less than "{targetValueOrAttribute}".' => '{Attribute} muss kleiner als "{targetValueOrAttribute}" ' .
        'sein.',
    '{Attribute} must be less than or equal to "{targetValueOrAttribute}".' => '{Attribute} muss kleiner als oder gleich ' .
        '"{targetValueOrAttribute}" sein.',
    /**
     * @see Email
     * @see Ip
     * @see Json
     * @see Length
     * @see Regex
     * @see Url
     */
    '{Attribute} must be a string.' => '{Attribute} muss eine Zeichenkette sein.',
    /**
     * @see Number
     * @see Integer
     */
    'The allowed types are integer, float and string.' => 'Die erlaubte Typen sind: integer, float und string.',
    '{Attribute} must be no less than {min}.' => '{Attribute} darf nicht kleiner als {min} sein.',
    '{Attribute} must be no greater than {max}.' => '{Attribute} darf nicht größer als {max} sein.',
];
