<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Image;

final class CompositeInfoProvider implements InfoProviderInterface
{
    /**
     * @var InfoProviderInterface[]
     */
    private array $providers;

    public function __construct(
        InfoProviderInterface ...$providers
    ) {
        $this->providers = $providers;
    }

    public function get(string $path): ?Info
    {
        foreach ($this->providers as $provider) {
            $info = $provider->get($path);
            if ($info !== null) {
                return $info;
            }
        }
        return null;
    }
}
