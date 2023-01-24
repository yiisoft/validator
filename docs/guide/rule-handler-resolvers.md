# Resolving rule handler dependencies

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

## Using [Yii config](https://github.com/yiisoft/config)

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
