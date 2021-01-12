<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Translator\TranslatorInterface;

final class ErrorMessage
{
    public string $message = '';
    public array $params = [];

    private ?TranslatorInterface $translator;

    public function __construct(string $message, array $params = [], ?TranslatorInterface $translator = null)
    {
        $this->message = $message;
        $this->params = $params;
        $this->translator = $translator;
    }

    public function withTranslator(?TranslatorInterface $translator = null): self
    {
        if ($translator !== null) {
            $new = clone $this;
            $new->translator = $translator;
            return $new;
        }
        return $this;
    }

    public function getMessage(?TranslatorInterface $translator = null): string
    {
        if ($translator === null) {
            $translator = $this->translator;
        }
        if ($translator !== null) {
            foreach ($this->params as &$value) {
                if ($value instanceof self) {
                    $value = $value->getMessage($translator);
                }
            }
            return $translator->translate($this->message, $this->params);
        }
        return $this->format($this->message, $this->params);
    }

    public function __toString(): string
    {
        return $this->getMessage();
    }

    private function format(string $message, array $params = []): string
    {
        $replacements = [];
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $value = 'array';
            } elseif (is_object($value)) {
                $value = 'object';
            } elseif (is_resource($value)) {
                $value = 'resource';
            }
            $replacements['{' . $key . '}'] = $value;
        }
        return strtr($message, $replacements);
    }
}
