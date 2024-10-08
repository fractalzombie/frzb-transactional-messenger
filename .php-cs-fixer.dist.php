<?php

/** @noinspection DuplicatedCode */

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;


$owner = 'Mykhailo Shtanko';
$email = 'fractalzombie@gmail.com';
$year = date('Y');
$projectDirectory = __DIR__;

$header = <<<'EOF'
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.

Copyright (c) {{YEAR}} {{OWNER}} {{EMAIL}}

For the full copyright and license information, please view the LICENSE.MD
file that was distributed with this source code.
EOF;

$finder = Finder::create()
    ->exclude([
        'var',
        'vendor',
        '.github',
        '.docker',
        'Documentation',
        'Resources',
    ])
    ->notName('*Configuration.*')
    ->ignoreDotFiles(true)
    ->in(__DIR__);

$rules = [
    '@PSR2' => true,
    '@PSR12' => true,
    '@Symfony' => true,
    '@Symfony:risky' => true,
    '@PhpCsFixer' => true,
    '@PHP80Migration' => true,
    '@PHP81Migration' => true,
    '@PHP80Migration:risky' => true,
    '@PHPUnit84Migration:risky' => true,
    'date_time_immutable' => true,
    'single_line_throw' => true,
    'php_unit_internal_class' => false,
    'phpdoc_align' => ['align' => 'left'],
    'php_unit_test_case_static_method_calls' => false,
    'php_unit_test_class_requires_covers' => false,
    'phpdoc_line_span' => ['const' => 'single', 'property' => 'single', 'method' => 'single'],
    'nullable_type_declaration_for_default_null_value' => ['use_nullable_type_declaration' => true],
    'header_comment' => [
        'comment_type' => 'PHPDoc',
        'header' => str_replace(
            ['{{YEAR}}', '{{OWNER}}', '{{EMAIL}}'],
            [$year, $owner, $email],
            $header,
        ),
        'location' => 'after_declare_strict',
        'separate' => 'top',
    ],
];

return (new Config())
    ->setRiskyAllowed(true)
    ->setRules($rules)
    ->setFinder($finder);
