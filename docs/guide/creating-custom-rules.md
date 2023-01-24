# Creating custom rules

When desired validation logic is missing in both built-in rules and extensions - it's time to create a custom rule. 

## Rules concept

The key feature of the rules' concept is a separation into 2 parts: 

- Rule (a class implementing `RuleInterface`). Only stores configuration options and a reference to its handler. Does 
not perform actual validation.
- Rule handler (a class implementing `RuleHandlerInterface`). Given a rule and input data, performs the actual 
validation within a current validation context. 

Besides responsibilities' separation, this approach allows to automatically resolve dependencies for a handler. For 
example, if you need a database connection object within a handler, you don't have to pass it there explicitly - it 
can be automatically obtained from a dependency container.

## Instructions for creating a custom rule and what to avoid

Let's try to create a rule for checking that a value is a valid [RGB color].

### Creating a rule

The 1st step is creation a rule:

```php
use Yiisoft\Validator\RuleInterface;

final class RgbColor implements RuleInterface 
{  
    public function __construct(
        public readonly string $message = 'Invalid RGB color value.',  
    ) {  
    }  
  
    public function getName(): string  
    {  
        return 'rgbColor';  
    }  
  
    public function getHandler(): string  
    {  
        return RgbColorHandler::class;  
    }  
}
```

> **Note:** `readonly` properties are supported only starting from PHP 8.1. For versions below that that use a getter 
> instead.

Besides required interface method implementations it only contains customizable error message. Of course, more features 
can be added - conditional validation, client options, etc. But this is a bare mininum to start with.

### Creating a handler

The 2nd step is creation of the handler. Let's define what is exactly a valid RGB color:

- It's an array (list to be exact).
- Contains exactly 3 items.
- Each item is an integer number within 0 - 255 range.

```php
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

final class RgbColorHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        // Every rule handler must start with this check.  
        if (!$rule instanceof RgbColor) {
            throw new UnexpectedRuleException(RgbColor::class, $rule);
        }

        if (!is_array($value) || array_keys($value) !== [0, 1, 2]) {
            return (new Result())->addError($rule->getMessage());
        }

        foreach ($value as $item) {
            if (!is_int($item) || $item < 0 || $item > 255) {
                return (new Result())->addError($rule->getMessage());
            }
        }

        return new Result();
    }
}
```

> **Note:** A `validate()` method is not intended to be called directly. Both resolving handler and calling the method
> happen automatically when using `Validator`.

### Tips for improving code

#### More specific error messages

Prefer more specific error messages to broad ones. Even this requires a bigger amount of messages and code, it helps to 
quicker understand what exactly is wrong with input data. RGB color is quite simple and compact structure, but in case 
of more complex data it will definitely pay off.

Keeping this in mind the rule can be rewritten something like this:

```php
use Yiisoft\Validator\RuleInterface;

final class RgbColor implements RuleInterface 
{  
    public function __construct(
        public readonly string $incorrectInputTypeMessage = 'Value must be an array. {type} given.',
        public readonly string $incorrectInputRepresentationMessage = 'Value must be a list.',
        public readonly string $incorrectItemsCountMessage = 'Value must contain exactly 3 items. ' . 
        '{itemsCount} {itemsCount, plural, one{item} other{items}} given.',
        public readonly string $incorrectItemTypeMessage = 'Every item must be an integer. {type} given at ' .
        '{position, selectordinal, one {#st} two {#nd} few {#rd} other {#th}} position.',          
        public readonly string $incorrectItemValueMessage = 'Every item must be between 0 and 255. {value} given at ' . 
        '{position, selectordinal, one {#st} two {#nd} few {#rd} other {#th}} position.',          
    ) {  
    }
  
    public function getName(): string  
    {  
        return 'rgbColor';  
    }  
  
    public function getHandler(): string  
    {  
        return RgbColorHandler::class;  
    }  
}
```

> **Note:** `readonly` properties are supported only starting from PHP 8.1. For versions below that use a getter
> instead.

> **Note:** Formatting used in `$incorrectItemTypeMessage` and `$incorrectItemValueMessage` requires `intl` PHP 
> extension.

The handler needs to be changed accordingly. Let's also add error parameters to be able to use them as placeholders in 
message templates:

```php
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

final class RgbColorHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof RgbColor) {
            throw new UnexpectedRuleException(RgbColor::class, $rule);
        }

        if (!is_array($value)) {
            return (new Result())->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'type' => get_debug_type($value),
            ]);
        }

        $itemsCount = 0;
        foreach (array_keys($value) as $index => $keyValue) {
            if ($keyValue !== $index) {
                return (new Result())->addError($rule->getIncorrectInputRepresentationMessage(), [
                    'attribute' => $context->getTranslatedAttribute(),
                ]);
            }

            $itemsCount++;
        }

        if ($itemsCount !== 3) {
            return (new Result())->addError($rule->getIncorrectItemsCountMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'itemsCount' => $itemsCount,
            ]);
        }

        foreach ($value as $index => $item) {
            if (!is_int($item)) {
                return (new Result())->addError($rule->getIncorrectItemTypeMessage(), [
                    'attribute' => $context->getTranslatedAttribute(),
                    'position' => $index + 1,
                    'type' => get_debug_type($item),
                ]);
            }

            if ($item < 0 || $item > 255) {
                return (new Result())->addError($rule->getIncorrectItemValueMessage(), [
                    'attribute' => $context->getTranslatedAttribute(),
                    'position' => $index + 1,
                    'value' => $value,
                ]);
            }
        }

        return new Result();
    }
}
```

> **Note:** It's also a good idea to utilize the features of used language version. For example, for PHP >= 8.1 we can 
> simplify checking that a given array is a list with [array_is_list] function.

#### Using built-in rules if possible

Before creating a custom rule, please check thoroughly - maybe it can be replaced with built-in rules, so it's 
unnecessary at all?

##### Replacing with `Composite`

The example with RGB color can be significantly simplified after realizing that it's also possible to achieve the same 
effect by just using only built-in rules:

```php
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Number;

$rules = [
    new Count(exactly: 3),
    new Each([new Number(integerOnly: true, min: 0, max: 255)])
];
```

Making them reusable isn't much harder - the whole set can be placed inside a `Composite` rule and used as a single 
regular rule.

```php
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Validator;

final class RgbColorRuleSet extends Composite
{
    public function getRules(): array
    {
        return [
            new Count(exactly: 3),
            new Each([new Number(integerOnly: true, min: 0, max: 255)])
        ];
    }
}

$result = (new Validator())->validate([205, 92, 92], new RgbColorRuleSet());
```

##### Replacing with separate rules and `when`

Below is an attempt of using validation context for validating attributes depending on each other:

- Validate company name only when the other attribute `hasCompany` name is filled.
- The company name must be a string with length between 1 and 50 characters.

```php
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

final class CompanyNameHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof CompanyName) {
            throw new UnexpectedRuleException(CompanyName::class, $rule);
        }

        if ($context->getDataSet()->getAttributeValue('hasCompany') !== true) {
            return new Result();
        }

        if (!is_string($value)) {
            return (new Result())->addError($rule->getIncorrectInputMessage());
        }

        $length = strlen($value);
        if ($length < 1 || $length > 50) {
            return (new Result())->addError($rule->getMessage());
        }

        return new Result();
    }
}
```

This custom rule can also be separated and refactored using built-in rules reducing coupling:

```php
use Yiisoft\Validator\Rule\BooleanValue;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\ValidationContext;

$rules = [
    'hasCompany' => new BooleanValue(),
    'companyName' => new HasLength(
        min: 1,
        max: 50,
        when: static function (mixed $value, ValidationContext $context): bool {
            return $context->getDataSet()->getAttributeValue('hasCompany') === true;
        },
    ),
];
```

## More appropriate examples

The idea for previous examples was to show the process of creating custom rules with handlers using "learn by mistakes" 
principle. So in terms of practical usage they probably less valuable because of replacement with built-in-rules. 
Knowing the core principles, let's explore more appropriate real-life examples.

### Verifying `YAML`

There is built-in rule for validating JSON. But what if we need the same, but for [YAML] for example? Let's try to
implement it.

Rule:

```php
use Yiisoft\Validator\RuleInterface;

final class Yaml implements RuleInterface 
{  
    public function __construct(
        public readonly string $incorrectInputMessage = 'Value must be a string. {type} given.',        
        public readonly string $message = 'The value is not a valid YAML.',          
    ) {  
    }
  
    public function getName(): string  
    {  
        return 'yaml';  
    }  
  
    public function getHandler(): string  
    {  
        return YamlHandler::class;  
    }  
}
```

> **Note:** `readonly` properties are supported only starting from PHP 8.1. For versions below that use a getter
> instead.

Handler:

```php
use Exception;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

final class YamlHandler implements RuleHandlerInterface
{  
    public function validate(mixed $value, object $rule, ValidationContext $context): Result 
    {  
        if (!$rule instanceof Yaml) {
            throw new UnexpectedRuleException(RgbColor::class, $rule);
        }
  
        if (!is_string($value)) {
            return (new Result())->addError($rule->getMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'type' => get_debug_type($value),
            ]);
        }

        try {
            $data = yaml_parse($value);
        } catch (Exception $e) {
            return (new Result())->addError($rule->getMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
            ]);
        }

        if ($data === false) {
            return (new Result())->addError($rule->getMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
            ]);
        }

        return new Result();  
    }
}
```

> **Note:** Using [yaml_parse] additionally requires `yaml` PHP extension.

> **Note:** Processing untrusted user input with `yaml_parse()` can be dangerous with certain settings. Please refer to
> [yaml_parse docs] for more details.

### Wrapping validation

One of the right usages of validation context can be wrapping validation with some additional logic. This can be used
for implementing [scenarios from Yii 2] for example.

```php
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

final class OnHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof On) {
            throw new UnexpectedRuleException(On::class, $rule);
        }

        $scenario = $context->getParameter(On::SCENARIO_PARAMETER);

        try {
            $scenario = $this->prepareScenarioValue($scenario);
        } catch (InvalidArgumentException) {
            return (new Result())
                ->addError(
                    sprintf(
                        'Scenario must be null, a string or "\Stringable" type, "%s" given.',
                        get_debug_type($scenario),
                    ),
                );
        }

        return $this->isSatisfied($rule, $scenario)
            // With active scenario, perform the validation.
            ? $context->validate($value, $rule->getRules())
            // With all other scenarios, skip the validation.
            : new Result();
    }
}
```

This code snippet is taken from [Yii Validator Scenarios] extension by [Sergei Predvoditelev]. Read more in [Scenarios]
section.

## Making an extension

With a custom rule you can go even further. If it's not too project-specific and you feel that it might be useful to 
someone else - make it available as an extension.

[RGB color]: https://en.wikipedia.org/wiki/RGB_color_model
[array_is_list]: https://www.php.net/manual/en/function.array-is-list.php
[YAML]: https://en.wikipedia.org/wiki/YAML
[yaml_parse]: https://www.php.net/manual/en/function.yaml-parse.php
[yaml_parse docs]: https://www.php.net/manual/en/function.yaml-parse.php
[scenarios from Yii 2]: https://www.yiiframework.com/doc/guide/2.0/en/structure-models#scenarios
[Yii Validator Scenarios]: https://github.com/vjik/yii-validator-scenarios
[Sergei Predvoditelev]: https://github.com/vjik
[Scenarios]:
