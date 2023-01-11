### Validation rule handlers

#### Creating your own validation rule handlers

##### Basic usage

To create your own validation rule handler you should implement `RuleHandlerInterface`:

```php
namespace MyVendor\Rules;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;use Yiisoft\Validator\FormatterInterface;use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;use Yiisoft\Validator\RuleInterface;

final class PiHandler implements RuleHandlerInterface
{
    use FormatMessageTrait;
    
    private FormatterInterface $formatter;
    
    public function __construct(
        ?FormatterInterface $formatter = null,
    ) {
        $this->formatter = $this->createFormatter();
    }
    
    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Pi) {
            throw new UnexpectedRuleException(Pi::class, $rule);
        }
        
        $result = new Result();
        $equal = \abs($value - M_PI) < PHP_FLOAT_EPSILON;

        if (!$equal) {
            $result->addError($this->formatter->format('Value is not PI.'));
        }

        return $result;
    }
    
    private function createFormatter(): FormatterInterface
    {
        // More complex logic
        // ...
        return CustomFormatter();
    }
}
```

Note that third argument in `validate()` is an instance of `ValidationContext` so you can use it if you need
whole data set context. For example, implementation might be the following if you need to validate "company"
property only if "hasCompany" is true:

```php
namespace MyVendor\Rules;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\ValidationContext;

final class CompanyNameHandler implements Rule\RuleHandlerInterface
{
    use FormatMessageTrait;
    
    private FormatterInterface $formatter;

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof CompanyName) {
            throw new UnexpectedRuleException(CompanyName::class, $rule);
        }
        
        $result = new Result();
        $dataSet = $context->getDataSet();
        $hasCompany = $dataSet->getAttributeValue('hasCompany') === true;

        if ($hasCompany && $this->isCompanyNameValid($value) === false) {
            $result->addError('Company name is not valid.');
        }

        return $result;
    }

    private function isCompanyNameValid(string $value): bool
    {
        // check company name    
    }
}
```

> Note: Do not call handler's `validate()` method directly. It must be used via Validator only.

##### Resolving rule handler dependencies

Basically, you can use `SimpleRuleHandlerResolver` to resolve rule handler.
In case you need extra dependencies, this can be done by `ContainerRuleHandlerResolver`.

That would work with the following implementation:

```php
final class NoLessThanExistingBidRuleHandler implements RuleHandlerInterface
{
    use FormatMessageTrait;
    
    private FormatterInterface $formatter;

    public function __construct(    
        private ConnectionInterface $connection,        
        ?FormatterInterface $formatter = null
    ) {
        $this->formatter = $formatter ?? new Formatter();
    }
    }
    
    public function validate(mixed $value, object $rule, ?ValidationContext $context): Result
    {
        $result = new Result();
        
        $currentMax = $connection->query('SELECT MAX(price) FROM bid')->scalar();
        if ($value <= $currentMax) {
            $result->addError($this->formatter->format('There is a higher bid of {bid}.', ['bid' => $currentMax]));
        }

        return $result;
    }
}

$ruleHandlerContainer = new ContainerRuleHandlerResolver(new MyContainer());
$ruleHandler = $ruleHandlerContainer->resolve(NoLessThanExistingBidRuleHandler::class);
```

`MyContainer` is a container for resolving dependencies and  must be an instance of
`Psr\Container\ContainerInterface`. [Yii Dependency Injection](https://github.com/yiisoft/di) implementation also can
be used.

###### Using [Yii config](https://github.com/yiisoft/config)

```php
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Validator\RuleHandlerResolverInterface;
use Yiisoft\Validator\RuleHandlerResolver\RuleHandlerContainer;

// Needs to be defined in common.php
$config = [
    RuleHandlerResolverInterface::class => RuleHandlerContainer::class,
];

$containerConfig = ContainerConfig::create()->withDefinitions($config); 
$container = new Container($containerConfig);
$ruleHandlerResolver = $container->get(RuleHandlerResolverInterface::class);        
$ruleHandler = $ruleHandlerResolver->resolve(PiHandler::class);
```

#### Using common arguments for multiple rules of the same type

Because concrete rules' implementations (`Number`, etc.) are marked as final, you can not extend them to set up
common arguments. For this and more complex cases use composition instead of inheritance:

```php
use Yiisoft\Validator\RuleInterface;

final class Coordinate implements RuleInterface
{
    private Number $baseRule;
    
    public function __construct() 
    {
        $this->baseRule = new Number(min: -10, max: 10);
    }        

    public function validate(mixed $value, ?ValidationContext $context = null): Result
    {
        return $this->baseRule->validate($value, $context);
    }
}
```
