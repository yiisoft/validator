# `StopOnError` - interrompe a validação no primeiro erro

Esta regra se aplica a um grupo de regras e permite interromper a validação para todo o grupo imediatamente após
ocorre um erro em qualquer uma das regras. Isso significa que todas as regras que seguem a regra cuja validação falhou não serão
executadas de jeito nenhum.

Isso pode ser útil para validações com alto desempenho, como consultas de banco de dados ou alguns cálculos complexos.
A ordem das regras dentro de um grupo é crucial aqui - as regras “leves” precisam ser colocadas acima das “pesadas”:

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StopOnError;
use Yiisoft\Validator\Validator;

$data = 2;
$rule = new StopOnError([
    new Length(min: 3), // "Lightweight" rule, will be run and won't pass the validation.
    new MyHeavyRule(), // "Heavy" rule, won't be run at all because of the existing error.
]);
$result = (new Validator())->validate($data, $rule);
```

Ao usar com outras regras e validação condicional, ele se comporta como uma única unidade. Por exemplo, com
configurações padrão, ela não será ignorada se a regra anterior não passar na validação. Para alterar esse comportamento, defina
`$skipOnError` para `true`. Isto permite utilizá-lo para limitar a lista de erros por atributo apenas ao primeiro (em
formulários HTML, por exemplo).

```php
$rules = [
    'attribute1' => new SimpleRule1(), // Let's say there is an error.
    // Then this rule is skipped completely with all its related rules because `skipOnError` is set to `true`. Useful
    // when all rules within `StopOnError` are heavy.
    'attribute2' => new StopOnError(
        [
            new HeavyRule1(), // Skipped.
            new HeavyRule2(), // Skipped.
        ],
        skipOnError: true,
    ),
    // This rule is not skipped because `skipOnError` is `false` by default. Useful for forcing validation and
    // limiting the errors.
    'attribute3' => new StopOnError([
        new SimpleRule2(), // Assuming there is another error.
        new SimpleRule3(), // Skipped.
    ]),
    // Skipping of other intermediate rules depends on `skipOnError` option set in these intermediate rules.
    'attribute4' => new SimpleRule4(), // Not skipped, because `skipOnError` is `false` by default.
];
```

Use a opção grouping / ordering / `skipOnError` para obter o efeito desejado.