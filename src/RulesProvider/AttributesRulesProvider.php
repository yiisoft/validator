<?php

declare(strict_types=1);

namespace Yiisoft\Validator\RulesProvider;

use ReflectionProperty;
use Yiisoft\Validator\Helper\ObjectParser;
use Yiisoft\Validator\RulesProviderInterface;

final class AttributesRulesProvider implements RulesProviderInterface
{
    private ObjectParser $parser;

    /**
     * @param class-string|object $source
     */
    public function __construct(
        string|object $source,
        int $propertyVisibility = ReflectionProperty::IS_PRIVATE
        | ReflectionProperty::IS_PROTECTED
        | ReflectionProperty::IS_PUBLIC,
        bool $skipStaticProperties = false,
    ) {
        $this->parser = new ObjectParser($source, $propertyVisibility, $skipStaticProperties);
    }

    public function getRules(): iterable
    {
        return $this->parser->getRules();
    }
}
