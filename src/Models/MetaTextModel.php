<?php

declare(strict_types=1);

namespace AiSdk\Meta\Models;

use AiSdk\Capability;
use AiSdk\Contracts\BaseModel;
use AiSdk\Contracts\TextModelInterface;
use AiSdk\Meta\MetaOptions;
use AiSdk\OpenAICompatible\ChatRequestBuilder;
use AiSdk\OpenAICompatible\ChatRequestProfile;
use AiSdk\OpenAICompatible\ChatResponseParser;
use AiSdk\OpenAICompatible\ChatStreamParser;
use AiSdk\Requests\TextModelRequest;
use AiSdk\Responses\TextModelResponse;
use AiSdk\Utils\Support\Url;
use Generator;

final class MetaTextModel extends BaseModel implements TextModelInterface
{
    private const array ADAPTER_CAPABILITIES = [
        Capability::TextGeneration,
        Capability::Streaming,
        Capability::ToolCalling,
        Capability::StructuredOutput,
        Capability::Reasoning,
        Capability::TextInput,
        Capability::ImageInput,
    ];

    public function __construct(
        private readonly string $modelId,
        private readonly MetaOptions $options,
    ) {}

    public function provider(): string
    {
        return MetaOptions::PROVIDER_NAME;
    }

    public function modelId(): string
    {
        return $this->modelId;
    }

    public function generate(TextModelRequest $request): TextModelResponse
    {
        $this->ensureTextRequestSupported($request, self::ADAPTER_CAPABILITIES);

        $payload = $this->runner($this->options->sdk)->postJson(
            Url::joinPath($this->options->baseUrl, '/chat/completions'),
            $this->buildBody($request, stream: false),
            $this->options->authHeaders(),
            $this->provider(),
        );

        return ChatResponseParser::parse($payload, $this->provider());
    }

    public function stream(TextModelRequest $request): Generator
    {
        $this->ensureTextRequestSupported($request, self::ADAPTER_CAPABILITIES, streaming: true);

        $events = $this->runner($this->options->sdk)->postStream(
            Url::joinPath($this->options->baseUrl, '/chat/completions'),
            $this->buildBody($request, stream: true),
            $this->options->authHeaders(),
            $this->provider(),
        );

        yield from ChatStreamParser::parse($events, $this->provider());
    }

    /**
     * @return array<string, mixed>
     */
    private function buildBody(TextModelRequest $request, bool $stream): array
    {
        return ChatRequestBuilder::build(
            $this->modelId,
            $this->provider(),
            $request,
            $stream,
            new ChatRequestProfile(maxTokensParameter: 'max_completion_tokens'),
        );
    }
}
