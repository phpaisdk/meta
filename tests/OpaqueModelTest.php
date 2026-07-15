<?php

declare(strict_types=1);

use AiSdk\Generate;
use AiSdk\Meta;

afterEach(function () {
    Generate::reset();
    Meta::reset();
});

it('uses adapter capabilities for opaque Meta model ids', function () {
    Meta::create(['apiKey' => 'meta-test']);
    $model = Meta::model('vendor/private-model');

    expect($model->modelId())->toBe('vendor/private-model');
});
