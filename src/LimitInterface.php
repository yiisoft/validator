<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

interface LimitInterface
{
    public function getMin(): ?int;

    public function getMax(): ?int;

    public function getExactly(): ?int;

    public function getLessThanMinMessage(): string;

    public function getGreaterThanMaxMessage(): string;

    public function getNotExactlyMessage(): string;
}
