# PHP AI SDK — Meta

Text generation, streaming, reasoning, and tool calling through Meta Model API. Part of the framework-agnostic [PHP AI SDK](https://phpaisdk.com).

## Installation

```bash
composer require aisdk/meta
```

Set `MODEL_API_KEY` in your environment.

## Usage

```php
use AiSdk\Generate;
use AiSdk\Meta;

$result = Generate::text('Explain closures in PHP in one sentence.')
    ->model(Meta::model('muse-wonder-1.0'))
    ->run();

echo $result->text;
```

## Documentation

[Read the documentation](https://phpaisdk.com/docs/0.x/providers/meta) for configuration, supported features, and more examples.

[Contributing](https://github.com/phpaisdk/.github/blob/main/CONTRIBUTING.md) · [Support](https://github.com/phpaisdk/.github/blob/main/SUPPORT.md)

## License

The MIT License (MIT). See [LICENSE](LICENSE) for details.
