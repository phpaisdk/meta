# aisdk/meta

<a href="https://github.com/phpaisdk/meta/actions"><img alt="GitHub Workflow Status" src="https://img.shields.io/github/actions/workflow/status/phpaisdk/meta/tests.yml?branch=main&label=Tests"></a>
<a href="https://packagist.org/packages/aisdk/meta"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/aisdk/meta"></a>
<a href="https://packagist.org/packages/aisdk/meta"><img alt="Latest Version" src="https://img.shields.io/packagist/v/aisdk/meta"></a>
<a href="https://packagist.org/packages/aisdk/meta"><img alt="License" src="https://img.shields.io/packagist/l/aisdk/meta"></a>
<a href="https://whyphp.dev"><img src="https://img.shields.io/badge/Why_PHP-in_2026-7A86E8?style=flat-square&labelColor=18181b" alt="Why PHP in 2026"></a>

------

Official Meta provider for the PHP AI SDK. Uses the Meta Model API's OpenAI-compatible chat completions endpoint.

## Installation

```bash
composer require aisdk/meta
```

## Basic Usage

```php
use AiSdk\Generate;
use AiSdk\Meta;

$result = Generate::text('Write a PHP function that reverses a string.')
    ->model(Meta::model('muse-spark-1.1'))
    ->run();

echo $result->text;
```

## Configuration

| Variable | Description | Default |
|---|---|---|
| `MODEL_API_KEY` | Meta Model API key for authentication | Required |
| `META_BASE_URL` | Base URL for API requests | `https://api.meta.ai/v1` |

## Supported Capabilities

| Capability | Support |
|---|---|
| Text generation | Native through Chat Completions |
| Streaming | Native through Chat Completions |
| Tool calling | Native through Chat Completions |
| Structured output | Native (`json_schema`) |
| Reasoning effort | Native (`reasoning_effort`) |
| Text input | Native |
| Image input | Native (URLs and data URLs) |

This package uses Meta's Chat Completions API. For Responses API-only features such as search grounding, files, and encrypted reasoning replay, use Meta's Responses API directly. Provider-specific fields can be passed through `providerOptions('meta', [...])`. Model IDs are opaque strings and Meta remains the authority on model availability.

## Testing

```bash
composer test:all
```

## Links

- [Meta Model API documentation](https://dev.meta.ai/docs/getting-started/overview/)
- [Chat Completions API](https://dev.meta.ai/docs/features/chat-completion)
- [Core Package](https://github.com/phpaisdk/core)
- [OpenAI-Compatible Package](https://github.com/phpaisdk/openai-compatible)
