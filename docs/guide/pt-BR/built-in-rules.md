# Regras integradas

## Regras disponíveis

Aqui está uma lista de todas as regras integradas disponíveis, divididas por categoria.

### Regras de tipo

- [BooleanType](../../../src/Rule/Type/BooleanType.php)
- [FloatType](../../../src/Rule/Type/FloatType.php)
- [IntegerType](../../../src/Rule/Type/IntegerType.php)
- [StringType](../../../src/Rule/Type/StringType.php)

### String rules

- [StringValue](../../../src/Rule/StringValue.php)
- [Length](../../../src/Rule/Length.php)
- [Regex](../../../src/Rule/Regex.php)
- [Email](../../../src/Rule/Email.php)
- [IP](../../../src/Rule/Ip.php)
- [Json](../../../src/Rule/Json.php)
- [Url](../../../src/Rule/Url.php)

### Regras booleanas

- [Boolean](../../../src/Rule/BooleanValue.php)
- [IsTrue](../../../src/Rule/TrueValue.php)

### Regras numéricas

- [Number](../../../src/Rule/Number.php)
- [Integer](../../../src/Rule/Integer.php)

### Regras de comparação

- [Compare](../../../src/Rule/Compare.php)
- [Equal](../../../src/Rule/Equal.php)
- [NotEqual](../../../src/Rule/NotEqual.php)
- [NotEqual](../../../src/Rule/GreaterThan.php)
- [GreaterThanOrEqual](../../../src/Rule/GreaterThanOrEqual.php)
- [LessThan](../../../src/Rule/LessThan.php)
- [LessThanOrEqual](../../../src/Rule/LessThanOrEqual.php)

### Regras de conjunto

- [In](../../../src/Rule/In.php)
- [Subset](../../../src/Rule/Subset.php)
- [UniqueIterable](../../../src/Rule/UniqueIterable.php)

### Regras de contagem

- [AtLeast](../../../src/Rule/AtLeast.php)
- [Count](../../../src/Rule/Count.php)
- [OneOf](../../../src/Rule/OneOf.php)

### Regras de arquivo

- [Image](../../../src/Rule/Image/Image.php)

### Regras de data

- [Date](../../../src/Rule/Date/Date.php)
- [DateTime](../../../src/Rule/Date/DateTime.php)
- [Time](../../../src/Rule/Date/Time.php)

### Regras de uso geral

- [Callback](../../../src/Rule/Callback.php)
- [Required](../../../src/Rule/Required.php)

### Regras complexas

- [AnyRule](../../../src/Rule/AnyRule.php)
- [Composite](../../../src/Rule/Composite.php)
- [Each](../../../src/Rule/Each.php)
- [Nested](../../../src/Rule/Nested.php)
- [StopOnError](../../../src/Rule/StopOnError.php)

## Guias

Algumas regras também possuem documentação além do PHPDoc:

- [Callback](built-in-rules-callback.md)
- [Compare](built-in-rules-compare.md)
- [Composite](built-in-rules-composite.md)
- [Each](built-in-rules-each.md)
- [Nested](built-in-rules-nested.md)
- [Required](built-in-rules-required.md)
- [StopOnError](built-in-rules-stop-on-error.md)

## Regras ausentes

Não consegue encontrar uma regra? Sinta-se à vontade para enviar um issue/PR, para que possa ser incluído no pacote após análise. Outra opção,
se o seu caso de uso for menos genérico, procure por [uma extensão] ou [crie uma regra personalizada].

[uma extensão]: extensions.md
[crie uma regra personalizadaa]: creating-custom-rules.md
