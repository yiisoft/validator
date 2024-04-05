# Extensões

A arquitetura do validador permite reabastecer a funcionalidade que falta por meio de extensões. Alguns já existem
e estão disponíveis para uso. Observe que eles não são oficiais no sentido de não fazerem parte dos pacotes Yiisoft.

## Cenários

Yii2 tem um recurso de [cenários] pronto para uso. Aqueles de vocês que usaram isso podem estar se perguntando por que não é mais o caso
com este pacote. Bem, pensamos que do ponto de vista arquitetônico é uma falha de design. O que parece ser mais
conciso no início, tende a crescer e se torna mais difícil de ler e manter com uma quantidade maior de
atributos/cenários/lógica de negócios, enquanto o custo de reescrita pode ser bastante alto. Isso foi comprovado na prática, então essa
abordagem é desencorajada e a forma recomendada com este pacote é usar DTO separado para cada cenário.
Claro, isso levará a alguma duplicação de código, mas é aceitável e terá retorno no futuro.
De qualquer forma, decidimos disponibilizá-lo através de uma extensão, mas use-a com cautela.

O pacote [Yii Validator Scenarios] (`vjik/yii-validator-scenarios`) de um membro da equipe principal [Sergei Predvoditelev] adiciona a
regra especial `On` que permite agrupar outras regras declarando cenários específicos.

Um exemplo da classe usando cenários:

```php
use Vjik\Yii\ValidatorScenarios\On;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;

final class UserDto
{
    public function __construct(
        #[On(
            'signup',
            [new Required(), new Length(min: 7, max: 10)]
        )]
        public string $name,

        #[Required]
        #[Email]
        public string $email,

        #[On(
            ['login', 'signup'],
            [new Required(), new Length(min: 8)],
        )]
        public string $password,
    ) {
    }
}

Um cenário ativo para validação atual é determinado por um parâmetro de contexto de validação dedicado:

```php
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\Validator;

$context = new ValidationContext([On::SCENARIO_PARAMETER => 'signup']);
$result = (new Validator())->validate($userDto, context: $context));
```

## Wrapper para regras do Symfony

O pacote [Yii Validator Symfony Rule] (`vjik/yii-validator-symfony-rule`) de um membro da equipe principal [Sergei Predvoditelev]
adapta [restrições do framework Symfony] para serem usadas como regras no Yii Validator.

Usá-lo é simples, tudo que você precisa fazer é agrupar uma restrição do Symfony (ou uma lista delas) com a regra `SymfonyRule`
fornecido por esta extensão.

```php
use Symfony\Component\Validator\Constraints\{CssColor, NotEqualTo, Positive};
use Vjik\Yii\ValidatorSymfonyRule\SymfonyRule;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;

final class Car
{
    #[Required]
    #[Length(min: 3, skipOnError: true)]
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
 
[cenários]: https://www.yiiframework.com/doc/guide/2.0/en/structure-models#scenarios
[Cenários do validador Yii]: https://github.com/vjik/yii-validator-scenarios
[Sergei Predvoditelev]: https://github.com/vjik
[Regra Symfony do validador Yii]: https://github.com/vjik/yii-validator-symfony-rule
[Restrições do framework Symfony]: https://symfony.com/doc/current/reference/constraints.html