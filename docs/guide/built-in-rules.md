# Built-in rules

## Available rules

Here is a list of all available built-in rules, sorted by category.

### String rules

- [HasLength](../blob/master/src/Rule/AtLeast.php)
- [Email](../blob/master/src/Rule/Email.php)
- [Ip](../blob/master/src/Rule/Ip.php)
- [Json](../blob/master/src/Rule/Json.php)
- [Regex](../blob/master/src/Rule/Regex.php)
- [Url](../blob/master/src/Rule/Url.php)

### Boolean rules

- [Boolean](../blob/master/src/Rule/Boolean.php)
- [IsTrue](../blob/master/src/Rule/IsTrue.php)

### Number rules

- [Number](../blob/master/src/Rule/Number.php)

### Comparison rules

- [CompareTo](../blob/master/src/Rule/CompareTo.php)
- [Equal](../blob/master/src/Rule/Equal.php)
- [GreaterThan](../blob/master/src/Rule/GreaterThan.php)
- [GreaterThanOrEqual](../blob/master/src/Rule/GreaterThanOrEqual.php)
- [LessThan](../blob/master/src/Rule/LessThan.php)
- [LessThanOrEqual](../blob/master/src/Rule/LessThanOrEqual.php)
- [NotEqual](../blob/master/src/Rule/NotEqual.php)

### Set rules

- [In](../blob/master/src/Rule/In.php)
- [Subset](../blob/master/src/Rule/Subset.php)

### Count rules

- [AtLeast](../blob/master/src/Rule/AtLeast.php)
- [Count](../blob/master/src/Rule/Count.php)

### General purpose rules

- [Callback](../blob/master/src/Rule/Callback.php)
- [Required](../blob/master/src/Rule/Required.php)

### Complex rules

- [Composite](../blob/master/src/Rule/Composite.php)
- [Each](../blob/master/src/Rule/Each.php)
- [Nested](../blob/master/src/Rule/Nested.php)
- [StopOnError](../blob/master/src/Rule/StopOnError.php)

## Guides

Some rules also have guides in addition to PHPDoc:

- [Callback](built-in-rules-callback.md)
- [Composite](built-in-rules-composite.md)
- [Each](built-in-rules-each.md)
- [Nested](built-in-rules-nested.md)
- [Required](built-in-rules-required.md)
- [StopOnError](built-in-rules-stop-on-error.md)

## Missing rules

Can't find a rule? Feel free to submit an issue / PR, so it can be included in the package after review. Another option,
if it's less generic, is to search for [an extension] first and [create a custom rule] if nothing is found.

[an extension]: extensions.md
[create a custom rule]: creating-custom-rules.md
