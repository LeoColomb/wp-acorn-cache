<?php

use Illuminate\Support\Facades\Cache;
use LeoColomb\WPAcornCache\Caches\ObjectCache;

test('build a valid key', function () {
    Cache::shouldReceive('get')
        ->once()
        ->withSomeOfArgs('default:test')
        ->andReturn('value');

    $cache = new ObjectCache();
    expect($cache->get('test'))->toBe('value');
});

test('build a valid group key', function () {
    Cache::shouldReceive('get')
         ->once()
         ->withSomeOfArgs('group:test')
         ->andReturn('value');

    $cache = new ObjectCache();
    expect($cache->get('test', 'group'))->toBe('value');
});

test('build a valid multi-site key', function () {
    global $is_ms;
    $is_ms = true;

    Cache::shouldReceive('get')
         ->once()
         ->withSomeOfArgs('1337:group:test')
         ->andReturn('value');

    $cache = new ObjectCache();
    expect($cache->get('test', 'group'))->toBe('value');

    $is_ms = false;
});

test('build a valid multi-site global key', function () {
    global $is_ms;
    $is_ms = true;

    Cache::shouldReceive('get')
         ->once()
         ->withSomeOfArgs('sites:test')
         ->andReturn('value');

    $cache = new ObjectCache();
    expect($cache->get('test', 'sites'))->toBe('value');

    $is_ms = false;
});

test('add a global group', function () {
    $cache = new ObjectCache();
    $cache->addGlobalGroups('test');
    expect($cache->global_groups)->toContain('test');
    expect($cache->non_persistent_groups)->not()->toContain('test');
});

test('add a non-persistent group', function () {
    $cache = new ObjectCache();
    $cache->addNonPersistentGroups('test');
    expect($cache->non_persistent_groups)->toContain('test');
    expect($cache->global_groups)->not()->toContain('test');
});
