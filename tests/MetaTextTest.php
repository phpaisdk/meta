<?php

declare(strict_types=1);

use AiSdk\Generate;
use AiSdk\Meta;
use AiSdk\Meta\Tests\Fakes\FakeHttpClient;
use AiSdk\Schema;
use AiSdk\Support\Sdk;

afterEach(function () {
    Generate::reset();
    Meta::reset();
    putenv('MODEL_API_KEY');
});

function configureMetaWith(FakeHttpClient $client): void
{
    $factory = new \Nyholm\Psr7\Factory\Psr17Factory();
    Generate::configure(new Sdk(
        httpClient: $client,
        requestFactory: $factory,
        streamFactory: $factory,
    ));
}

it('generates text through Meta Chat Completions', function () {
    $modelId = 'muse-spark-1.1';
    $client = new FakeHttpClient(200, json_encode([
        'id' => 'chatcmpl_meta',
        'object' => 'chat.completion',
        'created' => 1710000000,
        'model' => $modelId,
        'system_fingerprint' => 'fp_meta',
        'choices' => [['index' => 0, 'message' => ['content' => 'Hello from Meta'], 'finish_reason' => 'stop']],
        'usage' => ['prompt_tokens' => 7, 'completion_tokens' => 3],
    ]));
    configureMetaWith($client);
    Meta::create(['apiKey' => 'meta-test']);

    $result = Generate::text('Hi')->model(Meta::model($modelId))->run();

    expect($result->text)->toBe('Hello from Meta')
        ->and($result->usage->inputTokens)->toBe(7)
        ->and($result->providerMetadata['meta']['id'])->toBe('chatcmpl_meta')
        ->and($result->providerMetadata['meta']['model'])->toBe($modelId)
        ->and($result->providerMetadata['meta']['choice_finish_reason'])->toBe('stop');

    $body = $client->sentBody();
    expect($body['model'])->toBe($modelId)
        ->and($body['messages'][0]['role'])->toBe('user')
        ->and($body['max_completion_tokens'])->toBe(1024)
        ->and($body['stream'])->toBeFalse()
        ->and($client->lastRequest->getHeaderLine('Authorization'))->toBe('Bearer meta-test')
        ->and((string) $client->lastRequest->getUri())->toBe('https://api.meta.ai/v1/chat/completions');
});

it('reads the documented MODEL_API_KEY environment variable', function () {
    $client = new FakeHttpClient(200, json_encode([
        'choices' => [['message' => ['content' => 'Hello from Meta'], 'finish_reason' => 'stop']],
        'usage' => ['prompt_tokens' => 1, 'completion_tokens' => 1],
    ]));
    configureMetaWith($client);
    putenv('MODEL_API_KEY=meta-from-environment');

    Generate::text('Hi')->model(Meta::model('muse-spark-1.1'))->run();

    expect($client->lastRequest->getHeaderLine('Authorization'))->toBe('Bearer meta-from-environment');
});

it('normalizes provider-neutral text usage fields', function () {
    $client = new FakeHttpClient(200, json_encode([
        'choices' => [['index' => 0, 'message' => ['content' => 'Hello from Meta'], 'finish_reason' => 'stop']],
        'usage' => ['input_tokens' => 13, 'output_tokens' => 6, 'total_tokens' => 19],
    ]));
    configureMetaWith($client);
    Meta::create(['apiKey' => 'meta-test']);

    $result = Generate::text('Hi')->model(Meta::model('muse-spark-1.1'))->run();

    expect($result->usage->inputTokens)->toBe(13)
        ->and($result->usage->outputTokens)->toBe(6)
        ->and($result->usage->totalTokens)->toBe(19);
});

it('maps a 429 to a rate limit exception', function () {
    $client = new FakeHttpClient(429, json_encode(['error' => ['message' => 'slow down']]));
    configureMetaWith($client);
    Meta::create(['apiKey' => 'meta-test']);

    Generate::text('Hi')->model(Meta::model('muse-spark-1.1'))->run();
})->throws(\AiSdk\Exceptions\RateLimitException::class);

it('sends native json_schema structured output', function () {
    $client = new FakeHttpClient(200, json_encode([
        'choices' => [['message' => ['content' => '{"city":"Lahore","country":"Pakistan"}'], 'finish_reason' => 'stop']],
        'usage' => ['prompt_tokens' => 10, 'completion_tokens' => 7],
    ]));
    configureMetaWith($client);
    Meta::create(['apiKey' => 'meta-test']);

    $result = Generate::text('Extract the city and country from: Lahore, Pakistan.')
        ->model(Meta::model('muse-spark-1.1'))
        ->output(Schema::object(
            name: 'address',
            properties: [
                Schema::string(name: 'city')->required(),
                Schema::string(name: 'country')->required(),
            ],
        ))
        ->run();

    $body = $client->sentBody();

    expect($body['response_format']['type'])->toBe('json_schema')
        ->and($body['response_format']['json_schema']['name'])->toBe('address')
        ->and($body['response_format']['json_schema']['strict'])->toBeTrue()
        ->and($result->output)->toBe(['city' => 'Lahore', 'country' => 'Pakistan']);
});

it('accepts opaque text model ids without a model inventory', function () {
    Meta::create(['apiKey' => 'meta-test']);

    expect(Meta::model('future-private-model')->modelId())->toBe('future-private-model');
});
