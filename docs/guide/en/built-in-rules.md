# Built-in rules

## Available rules

Here is a list of all available built-in rules, divided by category.

### Type rules

- [BooleanType](../../../src/Rule/Type/BooleanType.php)
- [FloatType](../../../src/Rule/Type/FloatType.php)
- [IntegerType](../../../src/Rule/Type/IntegerType.php)
- [StringType](../../../src/Rule/Type/StringType.php)

### String rules

- [StringValue](../../../src/Rule/StringValue.php)
- [Length](../../../src/Rule/Length.php)
- [Regex](../../../src/Rule/Regex.php)
- [Email](../../../src/Rule/Email.php)
- [Ip](../../../src/Rule/Ip.php)
- [Json](../../../src/Rule/Json.php)
- [Url](../../../src/Rule/Url.php)

### Boolean rules

- [Boolean](../../../src/Rule/BooleanValue.php)
- [IsTrue](../../../src/Rule/TrueValue.php)

### Number rules

- [Number](../../../src/Rule/Number.php)
- [Integer](../../../src/Rule/Integer.php)

### Comparison rules

- [Compare](../../../src/Rule/Compare.php)
- [Equal](../../../src/Rule/Equal.php)
- [NotEqual](../../../src/Rule/NotEqual.php)
- [GreaterThan](../../../src/Rule/GreaterThan.php)
- [GreaterThanOrEqual](../../../src/Rule/GreaterThanOrEqual.php)
- [LessThan](../../../src/Rule/LessThan.php)
- [LessThanOrEqual](../../../src/Rule/LessThanOrEqual.php)

### Set rules

- [In](../../../src/Rule/In.php)
- [InEnum](../../../src/Rule/InEnum.php)
- [Subset](../../../src/Rule/Subset.php)
- [UniqueIterable](../../../src/Rule/UniqueIterable.php)

### Count rules

- [Count](../../../src/Rule/Count.php)
- [FilledAtLeast](../../../src/Rule/FilledAtLeast.php)
- [FilledOnlyOneOf](../../../src/Rule/FilledOnlyOneOf.php)

### File rules

- [Image](../../../src/Rule/Image/Image.php)

### Date rules

- [Date](../../../src/Rule/Date/Date.php)
- [DateTime](../../../src/Rule/Date/DateTime.php)
- [Time](../../../src/Rule/Date/Time.php)

### General purpose rules

- [Callback](../../../src/Rule/Callback.php)
- [Required](../../../src/Rule/Required.php)

### Complex rules

- [AnyRule](../../../src/Rule/AnyRule.php)
- [Composite](../../../src/Rule/Composite.php)
- [Each](../../../src/Rule/Each.php)
- [Nested](../../../src/Rule/Nested.php)
- [StopOnError](../../../src/Rule/StopOnError.php)

## Guides

Some rules also have guides in addition to PHPDoc:

- [Callback](built-in-rules-callback.md)
- [Compare](built-in-rules-compare.md)
- [Composite](built-in-rules-composite.md)
- [Each](built-in-rules-each.md)
- [Nested](built-in-rules-nested.md)
- [Required](built-in-rules-required.md)
- [StopOnError](built-in-rules-stop-on-error.md)

## Missing rules

Can't find a rule? Feel free to submit an issue / PR, so it can be included in the package after review. Another option,
if your use case is less generic, is to search for [an extension] or [create a custom rule].

[an extension]: extensions.md
[create a custom rule]: creating-custom-rules.md
