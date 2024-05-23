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
    // Usado em regra única

    /** @see AtLeast */
    'The data must have at least "{min}" filled attributes.' => 'Os dados devem conter no mínimo {min, number} {min, plural, one{atributo preenchido} few{atributo preenchido} many{atributos preenchidos} other{atributo preenchido}}.',
    /** @see BooleanValue */
    'Value must be either "{true}" or "{false}".' => 'O valor deve ser "{true}" ou "{false}".',
    /** @see Count */
    'This value must be an array or implement \Countable interface.' => 'O valor deve ser um array ou um objeto que implemente a interface \Countable.',
    'This value must contain at least {min, number} {min, plural, one{item} other{items}}.' => 'O valor deve conter pelo menos {min, number} {min, plural, one{item} few{itens} many{itens} other{itens}}.',
    'This value must contain at most {max, number} {max, plural, one{item} other{items}}.' => 'O valor não deve conter mais que {max, number} {max, plural, one{item} few{itens} many{itens} other{itens}}.',
    'This value must contain exactly {exactly, number} {exactly, plural, one{item} other{items}}.' => 'O valor deve conter exatamente {exactly, number} {exactly, plural, one{item} few{itens} many{itens} other{itens}}.',
    /** @see Each */
    'Value must be array or iterable.' => 'O valor deve ser um array ou iterável.',
    'Every iterable key must have an integer or a string type.' => 'A chave deve ser do tipo inteiro ou string.',
    /** @see Email */
    'This value is not a valid email address.' => 'O valor não é um endereço de e-mail válido.',
    /** @see In */
    'This value is not in the list of acceptable values.' => 'Este valor não está na lista de valores válidos.',
    /** @see IP */
    'Must be a valid IP address.' => 'Deve ser um endereço IP válido.',
    'Must not be an IPv4 address.' => 'Não deve ser um endereço IPv4.',
    'Must not be an IPv6 address.' => 'Não deve ser um endereço IPv6.',
    'Contains wrong subnet mask.' => 'Contém uma máscara de sub-rede inválida.',
    'Must be an IP address with specified subnet.' => 'Deve ser um endereço IP com sub-rede especificada.',
    'Must not be a subnet.' => 'Não deve ser uma sub-rede.',
    'Is not in the allowed range.' => 'Não incluído na lista de intervalos de endereços permitidos.',
    /** @see Integer */
    'Value must be an integer.' => 'O valor deve ser um número inteiro.',
    /** @see Json */
    'The value is not JSON.' => 'O valor não é uma string JSON.',
    /** @see Length */
    'This value must contain at least {min, number} {min, plural, one{character} other{characters}}.' => 'O valor deve conter pelo menos {min, number} {min, plural, one{character} few{characters} many{characters} other{characters}}.',
    'This value must contain at most {max, number} {max, plural, one{character} other{characters}}.' => 'O valor não deve conter mais que {max, number} {max, plural, one{character} few{characters} many{characters} other{characters}}.',
    'This value must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.' => 'O valor deve conter exatamente {exactly, number} {exactly, plural, one{character} few{characters} many{characters} other{characters}}.',
    /** @see Nested */
    'Nested rule without rules can be used for objects only.' => 'Regras aninhada sem especificar regras só pode ser usada para objetos.',
    'An object data set data can only have an array type.' => 'Os dados no objeto devem ser do tipo array.',
    'Property "{path}" is not found.' => 'Propriedade "{path}" não encontrada.',
    /** @see Number */
    'Value must be a number.' => 'O valor deve ser um número.',
    /** @see OneOf */
    'The data must have at least 1 filled attribute.' => 'Os dados devem conter pelo menos 1 atributo preenchido.',
    /** @see Regex */
    'Value is invalid.' => 'O valor está incorreto.',
    /** @see Required */
    'Value cannot be blank.' => 'O valor não pode estar vazio.',
    'Value not passed.' => 'Valor não passado.',
    /** @see Subset */
    'Value must be iterable.' => 'O valor deve ser iterável.',
    'This value is not a subset of acceptable values.' => 'Este valor não é um subconjunto de valores válidos.',
    /** @see TrueValue */
    'The value must be "{true}".' => 'O valor deve ser "{true}".',
    /** @see Url */
    'This value is not a valid URL.' => 'O valor não é uma URL válida.',

    // Usado em múltiplas regras

    /**
     * @see AtLeast
     * @see Nested
     * @see OneOf
     */
    'The value must be an array or an object.' => 'O valor deve ser um array ou um objeto.',
    /**
     * @see BooleanValue
     * @see TrueValue
     */
    'The allowed types are integer, float, string, boolean. {type} given.' => 'Tipos permitidos: inteiro, flutuante, string, booleano. Enviado {tipo}.',
    /**
     * @see Compare
     * @see Equal
     * @see GreaterThan
     * @see GreaterThanOrEqual
     * @see LessThan
     * @see LessThanOrEqual
     * @see NotEqual
     */
    'The allowed types are integer, float, string, boolean, null and object implementing \Stringable or \DateTimeInterface.' => 'Tipos permitidos: inteiro, float, string, boolean, null e um objeto implementando a interface \Stringable ou \DateTimeInterface.',
    'The attribute value returned from a custom data set must have one of the following types: integer, float, string, boolean, null or an object implementing \Stringable interface or \DateTimeInterface.' => 'O valor retornado deve ser um dos seguintes tipos: inteiro, flutuante, string, bool, nulo ou um objeto que implemente a interface \Stringable ou \DateTimeInterface.',
    'Value must be equal to "{targetValueOrAttribute}".' => 'O valor deve ser igual a "{targetValueOrAttribute}".',
    'Value must be strictly equal to "{targetValueOrAttribute}".' => 'O valor deve ser exatamente igual a "{targetValueOrAttribute}".',
    'Value must not be equal to "{targetValueOrAttribute}".' => 'O valor não deve ser igual a "{targetValueOrAttribute}".',
    'Value must not be strictly equal to "{targetValueOrAttribute}".' => 'O valor não deve ser exatamente igual a "{targetValueOrAttribute}".',
    'Value must be greater than "{targetValueOrAttribute}".' => 'O valor deve ser maior que "{targetValueOrAttribute}".',
    'Value must be greater than or equal to "{targetValueOrAttribute}".' => 'O valor deve ser maior ou igual a "{targetValueOrAttribute}".',
    'Value must be less than "{targetValueOrAttribute}".' => 'O valor deve ser menor que "{targetValueOrAttribute}".',
    'Value must be less than or equal to "{targetValueOrAttribute}".' => 'O valor deve ser menor ou igual a "{targetValueOrAttribute}".',
    /**
     * @see Email
     * @see Ip
     * @see Json
     * @see Length
     * @see Regex
     * @see Url
     */
    'The value must be a string.' => 'O valor deve ser uma string.',
    /**
     * @see Number
     * @see Integer
     */
    'The allowed types are integer, float and string.' => 'Tipos permitidos: inteiro, flutuante e string.',
    'Value must be no less than {min}.' => 'O valor deve não deve ser menor que {min}.',
    'Value must be no greater than {max}.' => 'O valor não deve ser maior que {max}.',

    /**
     * @see \Yiisoft\Validator\Rule\Date\Date
     * @see \Yiisoft\Validator\Rule\Date\DateTime
     * @see \Yiisoft\Validator\Rule\Date\Time
     */
    'The value must be no early than {limit}.' => 'O valor não deve ser anterior a {limit}.',
    'The value must be no late than {limit}.' => 'O valor não deve ser posterior a {limit}.',

    /**
     * @see \Yiisoft\Validator\Rule\Date\Date
     * @see \Yiisoft\Validator\Rule\Date\DateTime
     */
    'Invalid date value.' => 'Valor de data inválido.',

    /**
     * @see \Yiisoft\Validator\Rule\Date\Time
     */
    'Invalid time value.' => 'Valor de tempo inválido.',
];
