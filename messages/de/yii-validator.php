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
    'Value must be either "{true}" or "{false}".' => 'Der Wert muss "{true}" oder "{false}" sein.',
    /** @see Count */
    'This value must be an array or implement \Countable interface.' => 'Dieser Wert muss ein Array sein oder das ' .
        '\Countable Interface implementieren.',
    'This value must contain at least {min, number} {min, plural, one{item} other{items}}.' => 'Dieser Wert muss ' .
        'mindestens {min, number} {min, plural, one{Eintrag} other{Einträge}} enthalten.',
    'This value must contain at most {max, number} {max, plural, one{item} other{items}}.' => 'Dieser Wert darf ' .
        'maximal {max, number} {max, plural, one{Eintrag} other{Einträge}} enthalten.',
    'This value must contain exactly {exactly, number} {exactly, plural, one{item} other{items}}.' => 'Dieser Wert ' .
        'muss genau {exactly, number} {exactly, plural, one{Eintrag} other{Einträge}} enthalten.',
    /** @see Each */
    'Value must be array or iterable.' => 'Wert muss ein Array oder iterierbar sein.',
    'Every iterable key must have an integer or a string type.' => 'Jeder iterierbarer Schlüssel muss vom Typ ' .
        'Integer oder String sein.',
    /** @see Email */
    'This value is not a valid email address.' => 'Dieser Wert ist keine gültige E-Mail-Adresse.',
    /** @see In */
    'This value is not in the list of acceptable values.' => 'Dieser Wert ist nicht in der Liste der akzeptierten ' .
        'Werte.',
    /** @see Ip */
    'Must be a valid IP address.' => 'Muss eine gültige IP-Adresse sein.',
    'Must not be an IPv4 address.' => 'Darf keine IPv4-Adresse sein.',
    'Must not be an IPv6 address.' => 'Darf keine IPv6-Adresse sein.',
    'Contains wrong subnet mask.' => 'Enthält falsche Subnetz-Maske.',
    'Must be an IP address with specified subnet.' => 'Muss eine gültige IP-Adresse mit Subnetz sein.',
    'Must not be a subnet.' => 'Darf kein Subnetz sein.',
    'Is not in the allowed range.' => 'Ist nicht im erlaubten Bereich.',
    /** @see Integer */
    'Value must be an integer.' => 'Wert muss ein Integer sein.',
    /** @see Json */
    'The value is not JSON.' => 'Der Wert ist kein JSON.',
    /** @see Length */
    'This value must contain at least {min, number} {min, plural, one{character} other{characters}}.' => 'Dieser ' .
        'Wert muss mindestens {min, number} {min, plural, one{Buchstabe} other{Buchstaben}} enthalten.',
    'This value must contain at most {max, number} {max, plural, one{character} other{characters}}.' => 'Dieser ' .
        'Wert darf maximal {max, number} {max, plural, one{Buchstabe} other{Buchstaben}} enthalten.',
    'This value must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.' =>
        'Dieser Wert muss genau {exactly, number} {exactly, plural, one{Buchstabe} other{Buchstaben}} enthalten.',
    /** @see Nested */
    'Nested rule without rules can be used for objects only.' => 'Verschachtelte Regeln ohne Regeln können nur ' .
        'für Objekte verwendet werden.',
    'An object data set data can only have an array type.' => 'Die Daten eines Objektdatensatzes können nur den ' .
        'Typ Array haben.',
    'Property "{path}" is not found.' => 'Eigenschaft "{path}" nicht gefunden.',
    /** @see Number */
    'Value must be a number.' => 'Wert muss eine Nummer sein.',
    /** @see OneOf */
    'Exactly 1 attribute from this list must be filled: {attributes}.' => 'Exakt ein Attribut aus dieser Liste ' .
        'muss gefüllt sein: {attributes}.',
    /** @see Regex */
    'Value is invalid.' => 'Wert ist ungültig.',
    /** @see Required */
    'Value cannot be blank.' => 'Wert darf nicht leer sein.',
    'Value not passed.' => 'Wert nicht übergeben.',
    /** @see Subset */
    'Value must be iterable.' => 'Der Wert muss iterierbar sein.',
    'This value is not a subset of acceptable values.' => 'Dieser Wert ist keine Teilmenge akzeptabler Werte.',
    /** @see TrueValue */
    'The value must be "{true}".' => 'Der Wert muss "{true}" sein.',
    /** @see Url */
    'This value is not a valid URL.' => 'Dieser Wert ist keine gültige URL.',

    // Used in multiple rules

    /**
     * @see AtLeast
     * @see Nested
     * @see OneOf
     */
    'The value must be an array or an object.' => 'Dieser Wert muss ein Array oder Object sein.',
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
    'Value must be equal to "{targetValueOrAttribute}".' => 'Der Wert muss gleich "{targetValueOrAttribute}" sein.',
    'Value must be strictly equal to "{targetValueOrAttribute}".' => 'Der Wert muss strikt gleich ' .
        '"{targetValueOrAttribute}" sein.',
    'Value must not be equal to "{targetValueOrAttribute}".' => 'Der Wert darf nicht gleich ' .
        '"{targetValueOrAttribute}" sein.',
    'Value must not be strictly equal to "{targetValueOrAttribute}".' => 'Der Wert darf nicht strikt gleich ' .
        '"{targetValueOrAttribute}" sein.',
    'Value must be greater than "{targetValueOrAttribute}".' => 'Der Wert muss größer als ' .
        '"{targetValueOrAttribute}" sein.',
    'Value must be greater than or equal to "{targetValueOrAttribute}".' => 'Der Wert muss größer als oder gleich ' .
        '"{targetValueOrAttribute}" sein.',
    'Value must be less than "{targetValueOrAttribute}".' => 'Der Wert muss kleiner als "{targetValueOrAttribute}" ' .
        'sein.',
    'Value must be less than or equal to "{targetValueOrAttribute}".' => 'Der Wert muss kleiner als oder gleich ' .
        '"{targetValueOrAttribute}" sein.',
    /**
     * @see Email
     * @see Ip
     * @see Json
     * @see Length
     * @see Regex
     * @see Url
     */
    'The value must be a string.' => 'Der Wert muss eine Zeichenkette sein.',
    /**
     * @see Number
     * @see Integer
     */
    'The allowed types are integer, float and string.' => 'Die erlaubte Typen sind: integer, float und string.',
    'Value must be no less than {min}.' => 'Der Wert darf nicht kleiner als {min} sein.',
    'Value must be no greater than {max}.' => 'Der Wert darf nicht größer als {max} sein.',
];
