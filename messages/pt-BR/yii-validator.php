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
        'Pelo menos {min, number} {min, plural, one{propriedade} few{propriedades} many{propriedades} other{propriedades}} desta lista devem ser preenchidas para {property}: {properties}.',
    /** @see BooleanValue */
    '{Property} must be either "{true}" or "{false}".' => '{Property} deve ser "{true}" ou "{false}".',
    /** @see Count */
    '{Property} must be an array or implement \Countable interface. {type} given.' => '{Property} deve ser um array ou implementar a interface \Countable. {type} informado.',
    '{Property} must contain at least {min, number} {min, plural, one{item} other{items}}.' => '{Property} deve conter pelo menos {min, number} {min, plural, one{item} few{itens} many{itens} other{itens}}.',
    '{Property} must contain at most {max, number} {max, plural, one{item} other{items}}.' => '{Property} não deve conter mais que {max, number} {max, plural, one{item} few{itens} many{itens} other{itens}}.',
    '{Property} must contain exactly {exactly, number} {exactly, plural, one{item} other{items}}.' => '{Property} deve conter exatamente {exactly, number} {exactly, plural, one{item} few{itens} many{itens} other{itens}}.',
    /** @see Each */
    '{Property} must be array or iterable. {type} given.' => '{Property} deve ser um array ou iterável. {type} informado.',
    'Every iterable key of {property} must have an integer or a string type. {type} given.' => 'Cada chave iterável de {property} deve ser do tipo inteiro ou string. {type} informado.',
    /** @see Email */
    '{Property} is not a valid email address.' => '{Property} não é um endereço de e-mail válido.',
    /** @see In */
    '{Property} is not in the list of acceptable values.' => '{Property} não está na lista de valores válidos.',
    /** @see Ip */
    '{Property} must be a valid IP address.' => '{Property} deve ser um endereço IP válido.',
    '{Property} must not be an IPv4 address.' => '{Property} não deve ser um endereço IPv4.',
    '{Property} must not be an IPv6 address.' => '{Property} não deve ser um endereço IPv6.',
    '{Property} contains wrong subnet mask.' => '{Property} contém uma máscara de sub-rede inválida.',
    '{Property} must be an IP address with specified subnet.' => '{Property} deve ser um endereço IP com sub-rede especificada.',
    '{Property} must not be a subnet.' => '{Property} não deve ser uma sub-rede.',
    '{Property} is not in the allowed range.' => '{Property} não está no intervalo de endereços permitidos.',
    /**
     * @see IntegerType
     * @see Integer
     */
    '{Property} must be an integer.' => '{Property} deve ser um número inteiro.',
    /** @see Json */
    '{Property} is not a valid JSON.' => '{Property} não é um JSON válido.',
    /** @see Length */
    '{Property} must contain at least {min, number} {min, plural, one{character} other{characters}}.' => '{Property} deve conter pelo menos {min, number} {min, plural, one{caractere} few{caracteres} many{caracteres} other{caracteres}}.',
    '{Property} must contain at most {max, number} {max, plural, one{character} other{characters}}.' => '{Property} não deve conter mais que {max, number} {max, plural, one{caractere} few{caracteres} many{caracteres} other{caracteres}}.',
    '{Property} must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.' => '{Property} deve conter exatamente {exactly, number} {exactly, plural, one{caractere} few{caracteres} many{caracteres} other{caracteres}}.',
    /** @see Nested */
    'Nested rule without rules requires {property} to be an object. {type} given.' => 'Regra aninhada sem regras requer que {property} seja um objeto. {type} informado.',
    'An object data set data for {property} can only have an array type. {type} given.' => 'Os dados do conjunto de dados do objeto para {property} só podem ser do tipo array. {type} informado.',
    'Property "{path}" is not found in {property}.' => 'Propriedade "{path}" não encontrada em {property}.',
    /** @see Number */
    '{Property} must be a number.' => '{Property} deve ser um número.',
    /** @see FilledOnlyOneOf */
    'Exactly 1 property from this list must be filled for {property}: {properties}.' => 'Exatamente 1 propriedade desta lista deve ser preenchida para {property}: {properties}.',
    /** @see Regex */
    '{Property} is invalid.' => '{Property} é inválido.',
    /** @see Required */
    '{Property} cannot be blank.' => '{Property} não pode estar vazio.',
    '{Property} not passed.' => '{Property} não foi informado.',
    /** @see StringValue */
    '{Property} must be a string.' => '{Property} deve ser uma string.',
    /** @see Subset */
    '{Property} must be iterable. {type} given.' => '{Property} deve ser iterável. {type} informado.',
    '{Property} is not a subset of acceptable values.' => '{Property} não é um subconjunto de valores válidos.',
    /** @see TrueValue */
    '{Property} must be "{true}".' => '{Property} deve ser "{true}".',
    /** @see Url */
    '{Property} is not a valid URL.' => '{Property} não é uma URL válida.',
    /** @see Uuid */
    'The value of {property} is not a valid UUID.' => 'O valor de {property} não é um UUID válido.',

    // Used in multiple rules

    /**
     * @see FilledAtLeast
     * @see Nested
     * @see FilledOnlyOneOf
     */
    '{Property} must be an array or an object. {type} given.' => '{Property} deve ser um array ou um objeto. {type} informado.',
    /**
     * @see BooleanValue
     * @see TrueValue
     */
    'The allowed types for {property} are integer, float, string, boolean. {type} given.' => 'Os tipos permitidos para {property} são inteiro, float, string, booleano. {type} informado.',
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
        'Os tipos permitidos para {property} são inteiro, float, string, booleano, null e objeto que implemente a interface \Stringable ou \DateTimeInterface. {type} informado.',
    '{Property} returned from a custom data set must have one of the following types: integer, float, string, boolean, null or an object implementing \Stringable interface or \DateTimeInterface.' =>
        '{Property} retornado de um conjunto de dados personalizado deve ter um dos seguintes tipos: inteiro, float, string, booleano, null ou um objeto que implemente a interface \Stringable ou \DateTimeInterface.',
    '{Property} must be equal to "{targetValueOrProperty}".' => '{Property} deve ser igual a "{targetValueOrProperty}".',
    '{Property} must be strictly equal to "{targetValueOrProperty}".' => '{Property} deve ser exatamente igual a "{targetValueOrProperty}".',
    '{Property} must not be equal to "{targetValueOrProperty}".' => '{Property} não deve ser igual a "{targetValueOrProperty}".',
    '{Property} must not be strictly equal to "{targetValueOrProperty}".' => '{Property} não deve ser exatamente igual a "{targetValueOrProperty}".',
    '{Property} must be greater than "{targetValueOrProperty}".' => '{Property} deve ser maior que "{targetValueOrProperty}".',
    '{Property} must be greater than or equal to "{targetValueOrProperty}".' => '{Property} deve ser maior ou igual a "{targetValueOrProperty}".',
    '{Property} must be less than "{targetValueOrProperty}".' => '{Property} deve ser menor que "{targetValueOrProperty}".',
    '{Property} must be less than or equal to "{targetValueOrProperty}".' => '{Property} deve ser menor ou igual a "{targetValueOrProperty}".',
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
    '{Property} must be a string. {type} given.' => '{Property} deve ser uma string. {type} informado.',
    /**
     * @see Number
     * @see Integer
     */
    'The allowed types for {property} are integer, float and string. {type} given.' => 'Os tipos permitidos para {property} são inteiro, float e string. {type} informado.',
    '{Property} must be no less than {min}.' => '{Property} não deve ser menor que {min}.',
    '{Property} must be no greater than {max}.' => '{Property} não deve ser maior que {max}.',

    /**
     * @see \Yiisoft\Validator\Rule\Date\Date
     * @see \Yiisoft\Validator\Rule\Date\DateTime
     * @see \Yiisoft\Validator\Rule\Date\Time
     */
    '{Property} must be no earlier than {limit}.' => '{Property} não deve ser anterior a {limit}.',
    '{Property} must be no later than {limit}.' => '{Property} não deve ser posterior a {limit}.',

    /**
     * @see \Yiisoft\Validator\Rule\Date\Date
     * @see \Yiisoft\Validator\Rule\Date\DateTime
     */
    '{Property} must be a date.' => '{Property} deve ser uma data.',

    /**
     * @see \Yiisoft\Validator\Rule\Date\Time
     */
    '{Property} must be a time.' => '{Property} deve ser um horário.',

    /** @see UniqueIterable */
    '{Property} must be array or iterable.' => '{Property} deve ser um array ou iterável.',
    'The allowed types for iterable\'s item values of {property} are integer, float, string, boolean and object implementing \Stringable or \DateTimeInterface.' =>
        'Os tipos permitidos para os valores dos itens iteráveis de {property} são inteiro, float, string, booleano e objeto que implemente \Stringable ou \DateTimeInterface.',
    'All iterable items of {property} must have the same type.' =>
        'Todos os itens iteráveis de {property} devem ter o mesmo tipo.',
    'Every iterable\'s item of {property} must be unique.' => 'Cada item iterável de {property} deve ser único.',

    /** @see BooleanType */
    '{Property} must be a boolean.' => '{Property} deve ser um booleano.',
    /** @see FloatType */
    '{Property} must be a float.' => '{Property} deve ser um float.',
    /** @see AnyRule */
    'At least one of the inner rules must pass the validation.' => 'Pelo menos uma das regras internas deve passar na validação.',

    /** @see Image */
    '{Property} must be an image.' => '{Property} deve ser uma imagem.',
    'The width of {property} must be exactly {exactly, number} {exactly, plural, one{pixel} other{pixels}}.' =>
        'A largura de {property} deve ser exatamente {exactly, number} {exactly, plural, one{pixel} few{pixels} many{pixels} other{pixels}}.',
    'The height of {property} must be exactly {exactly, number} {exactly, plural, one{pixel} other{pixels}}.' =>
        'A altura de {property} deve ser exatamente {exactly, number} {exactly, plural, one{pixel} few{pixels} many{pixels} other{pixels}}.',
    'The width of {property} cannot be smaller than {limit, number} {limit, plural, one{pixel} other{pixels}}.' =>
        'A largura de {property} não pode ser menor que {limit, number} {limit, plural, one{pixel} few{pixels} many{pixels} other{pixels}}.',
    'The height of {property} cannot be smaller than {limit, number} {limit, plural, one{pixel} other{pixels}}.' =>
        'A altura de {property} não pode ser menor que {limit, number} {limit, plural, one{pixel} few{pixels} many{pixels} other{pixels}}.',
    'The width of {property} cannot be larger than {limit, number} {limit, plural, one{pixel} other{pixels}}.' =>
        'A largura de {property} não pode ser maior que {limit, number} {limit, plural, one{pixel} few{pixels} many{pixels} other{pixels}}.',
    'The height of {property} cannot be larger than {limit, number} {limit, plural, one{pixel} other{pixels}}.' =>
        'A altura de {property} não pode ser maior que {limit, number} {limit, plural, one{pixel} few{pixels} many{pixels} other{pixels}}.',
    'The aspect ratio of {property} must be {aspectRatioWidth, number}:{aspectRatioHeight, number} with margin {aspectRatioMargin, number}%.' =>
        'A proporção de {property} deve ser {aspectRatioWidth, number}:{aspectRatioHeight, number} com margem de {aspectRatioMargin, number}%.',
];
