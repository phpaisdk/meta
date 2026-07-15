<?php

declare(strict_types=1);

namespace AiSdk;

use AiSdk\Contracts\Model;
use AiSdk\Meta\MetaOptions;
use AiSdk\Meta\MetaProvider;

final class Meta
{
    private static ?MetaProvider $default = null;

    /**
     * @param  array<string, mixed>  $config
     */
    public static function create(array $config = []): MetaProvider
    {
        return self::$default = new MetaProvider(MetaOptions::fromArray($config));
    }

    public static function default(): MetaProvider
    {
        return self::$default ??= self::create();
    }

    public static function reset(): void
    {
        self::$default = null;
    }

    public static function model(string $modelId): Model
    {
        return self::default()->model($modelId);
    }
}
