<?php

namespace Yiisoft\Validator;

trait SkippableTrait
{
    /**
     * @var bool
     */
    private ?bool $skipOnEmpty = false;

    /**
     * @var bool
     */
    private ?bool $skipOnError = true;

    /**
     * @param bool $value
     * @return $this
     */
    public function skipOnError(bool $value): self
    {
        $new = clone $this;
        $new->skipOnError = $value;
        return $new;
    }

    /**
     * @param bool $value if validation should be skipped if value validated is empty
     * @return self
     */
    public function skipOnEmpty(bool $value): self
    {
        $new = clone $this;
        $new->skipOnEmpty = $value;
        return $new;
    }
}
