# Extensions

The validator's architecture allows to replenish its missing functionality via extensions. A few number already exists 
and available for usage. Note that they are not official in terms of not being the part of Yiisoft packages.

## Scenarios

Yii2 has [scenarios] feature out of the box. Those of you that used it might be wondering why it's no longer the case 
with this package. Well, we think that from architectural point of view it's a design flaw. What seems to be more 
concise at first, tends to grow and became harder to read and maintain with a bigger amount of attributes / scenarios / 
business logic, while rewriting cost can be quite high. This was proved in practice, so this approach is discouraged and 
the recommended way with this package is using separate DTO for each scenario. Sure, this will lead to some code 
duplication, but it's acceptable and will pay off in the future. Anyway, we decided to make it available through 
extension, but use it with caution.   

[Yii Validator Scenarios] (`vjik/yii-validator-scenarios`) package from a core team member [Sergei Predvoditelev] adds
special `On` rule which allows to wrap other rules with declaring specific scenarios.

An example of the class using scenarios:

```php
final class UserDto
{
    public function __construct(
        #[On(
            'signup',
            [new Required(), new HasLength(min: 7, max: 10)]
        )]
        public string $name,

        #[Required]
        #[Email]
        public string $email,

        #[On(
            ['login', 'signup'],
            [new Required(), new HasLength(min: 8)],
        )]
        public string $password,
    ) {
    }
}
```

An active scenario for current validation is determined by a dedicated validation context parameter:

```php
$context = new ValidationContext([On::SCENARIO_PARAMETER => 'signup']);
$result = (new Validator())->validate($userDto, context: $context));
```

## Wrapper for Symfony rules

[Yii Validator Symfony Rule] (`vjik/yii-validator-symfony-rule`) package from a core team member [Sergei Predvoditelev] 
adapts [constraints from Symfony framework] to be used as rules in Yii Validator.

Using is straight forward, all you have to do is wrap a Symfony constraint (or a list of them) with `SymfonyRule` rule 
provided by this extension.

```php
use Symfony\Component\Validator\Constraints\{CssColor, NotEqualTo, Positive};
use Vjik\Yii\ValidatorSymfonyRule\SymfonyRule;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;

final class Car
{
    #[Required]
    #[HasLength(min: 3, skipOnError: true)]
    public string $name = '';

    #[Required]
    // A single constraint.
    #[SymfonyRule(
        new CssColor(CssColor::RGB),
        skipOnError: true,
    )]
    public string $cssColor = '#1123';

    // Multiple constraints.
    #[SymfonyRule([
        new Positive(),
        new NotEqualTo(13),
    ])]
    public int $number = 13;
}
```
 
[scenarios]: https://www.yiiframework.com/doc/guide/2.0/en/structure-models#scenarios
[Yii Validator Scenarios]: https://github.com/vjik/yii-validator-scenarios
[Sergei Predvoditelev]: https://github.com/vjik
[Yii Validator Symfony Rule]: https://github.com/vjik/yii-validator-symfony-rule
[constraints from Symfony framework]: https://symfony.com/doc/current/reference/constraints.html
