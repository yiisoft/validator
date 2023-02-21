<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Attribute;
use Yiisoft\Validator\RulesProvider\AttributesRulesProvider;

/**
 * An optional interface for rules to implement. It provides {@see afterInitAttribute()} method-based event allowing to
 * execute custom code after a rule instance was created from a PHP attribute either when using {@see ObjectDataSet},
 * {@see AttributesRulesProvider} or {@see ObjectParser} directly.
 */
interface AfterInitAttributeEventInterface
{
    /**
     * Method-based event allowing to execute custom code after a rule instance was created from a PHP attribute when
     * using {@see ObjectDataSet}, {@see AttributesRulesProvider} or {@see ObjectParser} directly.
     *
     * @param object $object An object containing rules within attributes.
     */
    public function afterInitAttribute(object $object): void;
}
