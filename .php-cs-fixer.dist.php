<?php

declare(strict_types=1);

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'no_alias_functions' => [
            'sets' => ['@all']
        ],
        'no_alias_language_construct_call' => true,
        'modernize_types_casting' => true,
        'no_php4_constructor' => true,
        'ordered_class_elements' => [
            'order' => [
                'use_trait',
                'case',
                'constant_public',
                'constant_protected',
                'constant_private',
                'property_public_static',
                'property_protected_static',
                'property_private_static',
                'property_public',
                'property_protected',
                'property_private',
                'construct',
                'destruct',
                'magic',
                'phpunit',
                'method_public_static',
                'method_protected_static',
                'method_private_static',
                'method_public',
                'method_protected',
                'method_private'
            ],
            'sort_algorithm' => 'none',
        ],
        'self_accessor' => true,
        'multiline_comment_opening_closing' => true,
        'no_useless_else' => true,
        'simplified_if_return' => false,
        'concat_space' => [
            'spacing' => 'one'
        ],
        'increment_style' => [
            'style' => 'post'
        ],
        'ordered_imports' => [
            'sort_algorithm' => 'alpha'
        ],
        'phpdoc_summary' => false,
        'binary_operator_spaces' => [
            'default' => 'single_space',
            'operators' => [
                '=' => 'align_single_space',
                '+=' => 'align_single_space',
                '-=' => 'align_single_space',
                '=>' => 'align_single_space',
            ],
        ],
        'blank_line_before_statement' => [
            'statements' => [
                'return',
                'continue',
                'exit',
                'if',
                'switch',
                'throw',
                'try',
            ]
        ],
        'operator_linebreak' => [
            'only_booleans' => true,
        ],
        'phpdoc_order' => true,
        'return_assignment' => false,
        'phpdoc_to_comment' => [
            'ignored_tags' => [
                'psalm-suppress',
                'var'
            ],
        ],
        'single_line_throw' => false,
        'types_spaces' => [
            'space' => 'single',
        ],
        'yoda_style' => [
            'equal' => null,
            'identical' => null,
            'less_and_greater' => null
        ],
        'native_function_invocation' => [
            'include' => ['@compiler_optimized'],
            'scope' => 'namespaced',
            'strict' => true
        ],
    ])
    ->setRiskyAllowed(true)
    ->setFinder(
        (new PhpCsFixer\Finder())
            ->in(__DIR__."/src")
    )
    ->setCacheFile(".php-cs-fixer.cache");
