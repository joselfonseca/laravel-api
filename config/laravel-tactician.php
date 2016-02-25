<?php


return [
    // The locator to bind
    'locator' => 'Joselfonseca\LaravelTactician\Locator\LaravelLocator',
    // The inflector to bind
    'inflector' => 'League\Tactician\Handler\MethodNameInflector\HandleInflector',
    // The extractor to bind
    'extractor' => 'League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor',
    // The bus to bind
    'bus' => 'Joselfonseca\LaravelTactician\Bus'
];
