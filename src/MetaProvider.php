<?php

declare(strict_types=1);

namespace AiSdk\Meta;

use AiSdk\Contracts\BaseProvider;
use AiSdk\Contracts\TextModelInterface;
use AiSdk\Contracts\TextProviderInterface;
use AiSdk\Meta\Models\MetaTextModel;

final class MetaProvider extends BaseProvider implements TextProviderInterface
{
    public function __construct(public readonly MetaOptions $options) {}

    public function name(): string
    {
        return MetaOptions::PROVIDER_NAME;
    }

    protected function textModel(string $modelId): TextModelInterface
    {
        return new MetaTextModel($modelId, $this->options);
    }
}
