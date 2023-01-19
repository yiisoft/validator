# Client side validation

Unlike Yii 2, the package does not provide any handling of the validation rules on the client side. Likely it will be 
added later as another related package. 

But, despite this, there is a possibility to export rules' options as array for passing to client side using 
`RulesDumper` class:

- Multiple rules and nesting of rules are supported.
- If a rule does not provide options, only the name will be exported.
- The options' values that can't be serialized / reproduced at the client side - callables, for example, are excluded - 
either completely like `Callback::$callback` or partially like `$skipOnEmpty` in case of supporting multiple types.

Given built-in `HasLength` rule:

```php
$rules = [  
    'name' => [  
        new HasLength(min: 4, max: 10),  
    ],  
];  
$options = (new RulesDumper())->asArray($rules);
```

the output will be:

```php
[  
    'name' => [  
        [  
            'hasLength',  
            'min' => 4,  
            'max' => 10,  
            'exactly' => null,  
            'lessThanMinMessage' => [  
                'template' => 'This value must contain at least {min, number} {min, plural, one{character} other{characters}}.',  
                'parameters' => ['min' => 4],  
            ],  
            'greaterThanMaxMessage' => [  
                'template' => 'This value must contain at most {max, number} {max, plural, one{character} other{characters}}.',  
                'parameters' => ['max' => 10],  
            ],  
            'notExactlyMessage' => [  
                'template' => 'This value must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.',  
                'parameters' => ['exactly' => null],  
            ],  
            'incorrectInputMessage' => [  
                'template' => 'This value must be a string.',  
                'parameters' => [],  
            ],  
            'encoding' => 'UTF-8',  
            'skipOnEmpty' => false,  
            'skipOnError' => false,  
        ],
    ],  
],
```

Resulting array serialized as JSON can be unserialized back and applied to implementation of your choice.

## Structure of exported options

Here are some specifics of rules structure:

- Indexing of rules by attribute names is maintained.
- The first rule element is always a rule name with an `0` integer index.
- The rest rule elements are key-value pairs where key is an option name and value is a corresponding option value.
- For complex rules such as `Composite`, `Each` and `Nested` child rules' options are located under the `rules` key.

Note that the error messages have a special structure:

```php
[
    'lessThanMinMessage' => [  
        'template' => 'This value must contain at least {min, number} {min, plural, one{character} other{characters}}.',  
        'parameters' => ['min' => 4],  
    ],
];
```

It stays the same regardless of placeholders and parameters presence:

```php
'message' => [
    'template' => 'Value is invalid.',
    'parameters' => [],
],
```
