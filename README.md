# aisdk/meta

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
    ->model(Meta::model('Llama-4-Maverick-17B-128E-Instruct-FP8'))
    ->run();

echo $result->text;
```

## Configuration

| Variable | Description | Default |
|---|---|---|
| `META_API_KEY` | Meta Model API key for authentication | Required |
| `META_BASE_URL` | Base URL for API requests | `https://api.meta.ai/v1` |

## Supported Capabilities

| Capability | Support |
|---|---|
| Text generation | Native through the compatible endpoint |
| Streaming | Native through the compatible endpoint |
| Tool calling | Native through the compatible endpoint |
| Structured output | Adapted (`json_object` with an explicit JSON instruction) |
| Text input | Native |

Provider-specific fields can be passed through `providerOptions('meta', [...])`. Model IDs are opaque strings and Meta remains the authority on model availability.

## Testing

```bash
composer test:all
```

## Links

- [Meta Model API](https://developer.meta.com/ai/)
- [Build with Muse Spark](https://developer.meta.com/ai/resources/blog/build-with-muse-spark/)
- [Core Package](https://github.com/phpaisdk/core)
- [OpenAI-Compatible Package](https://github.com/phpaisdk/openai-compatible)
