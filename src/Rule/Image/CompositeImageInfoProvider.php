<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Image;

final class CompositeImageInfoProvider implements ImageInfoProviderInterface
{
    /**
     * @var ImageInfoProviderInterface[]
     */
    private readonly array $providers;

    public function __construct(
        ImageInfoProviderInterface ...$providers,
    ) {
        $this->providers = $providers;
    }

    public function get(string $path): ?ImageInfo
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
