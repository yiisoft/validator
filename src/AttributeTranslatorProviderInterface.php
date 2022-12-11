<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

interface AttributeTranslatorProviderInterface
{
    public function getAttributeTranslator(): ?AttributeTranslatorInterface;
}
