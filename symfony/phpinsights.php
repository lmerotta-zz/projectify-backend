<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Preset
    |--------------------------------------------------------------------------
    |
    | This option controls the default preset that will be used by PHP Insights
    | to make your code reliable, simple, and clean. However, you can always
    | adjust the `Metrics` and `Insights` below in this configuration file.
    |
    | Supported: "default", "laravel", "symfony", "magento2", "drupal"
    |
    */

    'preset' => 'symfony',
    /*
    |--------------------------------------------------------------------------
    | IDE
    |--------------------------------------------------------------------------
    |
    | This options allow to add hyperlinks in your terminal to quickly open
    | files in your favorite IDE while browsing your PhpInsights report.
    |
    | Supported: "textmate", "macvim", "emacs", "sublime", "phpstorm",
    | "atom", "vscode".
    |
    | If you have another IDE that is not in this list but which provide an
    | url-handler, you could fill this config with a pattern like this:
    |
    | myide://open?url=file://%f&line=%l
    |
    */

    'ide' => null,
    /*
    |--------------------------------------------------------------------------
    | Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may adjust all the various `Insights` that will be used by PHP
    | Insights. You can either add, remove or configure `Insights`. Keep in
    | mind, that all added `Insights` must belong to a specific `Metric`.
    |
    */

    'exclude' => [
        //  'path/to/directory-or-file'
        'src/Kernel.php',
        'src/Entity',
        'migrations',
        'phpinsights.php',
        'tests',
        'vendor',
        'bin',
    ],

    'add' => [
        //  ExampleMetric::class => [
        //      ExampleInsight::class,
        //  ]
    ],

    'remove' => [
        \NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff::class,
        \ObjectCalisthenics\Sniffs\NamingConventions\ElementNameMinimalLengthSniff::class,
        \ObjectCalisthenics\Sniffs\Metrics\MaxNestingLevelSniff::class,
        \SlevomatCodingStandard\Sniffs\ControlStructures\DisallowEmptySniff::class,
        \SlevomatCodingStandard\Sniffs\TypeHints\DisallowArrayTypeHintSyntaxSniff::class,
        \SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSniff::class,
        \SlevomatCodingStandard\Sniffs\TypeHints\PropertyTypeHintSniff::class,
        \SlevomatCodingStandard\Sniffs\TypeHints\DeclareStrictTypesSniff::class,
        \SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff::class,
        \SlevomatCodingStandard\Sniffs\Functions\UnusedParameterSniff::class,
        \NunoMaduro\PhpInsights\Domain\Insights\ForbiddenNormalClasses::class,
        \ObjectCalisthenics\Sniffs\Metrics\MethodPerClassLimitSniff::class,
        \SlevomatCodingStandard\Sniffs\Classes\SuperfluousInterfaceNamingSniff::class,
        \SlevomatCodingStandard\Sniffs\Classes\SuperfluousExceptionNamingSniff::class,
        \NunoMaduro\PhpInsights\Domain\Insights\Composer\ComposerMustBeValid::class,
        \PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterNotSniff::class,
        \ObjectCalisthenics\Sniffs\Files\FunctionLengthSniff::class,
        \SlevomatCodingStandard\Sniffs\Commenting\DocCommentSpacingSniff::class,
        \PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer::class,
        \PhpCsFixer\Fixer\Phpdoc\AlignMultilineCommentFixer::class,
        \PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting\TodoSniff::class,
        \SlevomatCodingStandard\Sniffs\ControlStructures\DisallowYodaComparisonSniff::class,
        \NunoMaduro\PhpInsights\Domain\Insights\ForbiddenGlobals::class,
        \PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\DiscourageGotoSniff::class,
        \SlevomatCodingStandard\Sniffs\TypeHints\NullableTypeForNullDefaultValueSniff::class,
        \SlevomatCodingStandard\Sniffs\Namespaces\UnusedUsesSniff::class,
        \SlevomatCodingStandard\Sniffs\PHP\UselessSemicolonSniff::class,
        \NunoMaduro\PhpInsights\Domain\Insights\ForbiddenTraits::class,
        \SlevomatCodingStandard\Sniffs\Classes\ForbiddenPublicPropertySniff::class,
        \SlevomatCodingStandard\Sniffs\Functions\FunctionLengthSniff::class,
        

    ],

    'config' => [
        \PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff::class => [
            'lineLimit' => 120,
            'absoluteLineLimit' => 160
        ],
        \PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer::class => [
            'closure_function_spacing' => 'none'
        ],
        \NunoMaduro\PhpInsights\Domain\Insights\CyclomaticComplexityIsHigh::class => [
            'exclude' => [
                'src/Modules/UserManagement/Security/Authorization/UserVoter.php',
                'src/Modules/UserManagement/ApiPlatform/ResolveUserProfilePictureFieldSubscriber.php'
            ]
        ],
        \PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer::class => [
            'order' => ['use_trait']
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Requirements
    |--------------------------------------------------------------------------
    |
    | Here you may define a level you want to reach per `Insights` category.
    | When a score is lower than the minimum level defined, then an error
    | code will be returned. This is optional and individually defined.
    |
    */

    'requirements' => [
        'min-quality' => 99,
        'min-complexity' => 80,
        'min-architecture' => 100,
        'min-style' => 100,
//        'disable-security-check' => false,
    ],

];
