<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in(['src', 'tests'])
;

return (new PhpCsFixer\Config())
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRules([
        '@PER-CS' => true,
        // disabling PER rule for typecast indentation
        'cast_spaces' => false,
        // disabling PER rule for spaces around concatenation
        'concat_space' => false,
        // disabling PER rule for setting trailing comma for arguments, etc.
        'trailing_comma_in_multiline' => ['elements' => ['arrays']],
        // disabling PER rule for putting attribute at the separate line with related argument
        'method_argument_space' => ['attribute_placement' => 'ignore'],
        'no_unused_imports' => true,
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__ . '/var/.php-cs-fixer.cache')
;
