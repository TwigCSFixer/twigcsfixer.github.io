<?php

$finder = PhpCsFixer\Finder::create()
    ->files()
    ->in(__DIR__)
    ->exclude('vendor')
    ->name('*.php');

return (new PhpCsFixer\Config())
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PHP84Migration' => true,
        '@PHP82Migration:risky' => true, // There is no PHP84Migration::risky or PHP83Migration::risky.
        '@Symfony' => true,
        '@Symfony:risky' => true,

        // Override Symfony config
        'method_argument_space' => [
            'after_heredoc' => true,
            'on_multiline' => 'ensure_fully_multiline',
            'attribute_placement' => 'same_line',
        ],
        'no_superfluous_phpdoc_tags' => ['allow_mixed' => true, 'remove_inheritdoc' => true],
        'single_line_throw' => false,
    ])
    ->setFinder($finder);
