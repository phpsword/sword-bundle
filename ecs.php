<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Basic\NoTrailingCommaInSinglelineFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTypesOrderFixer;
use PhpCsFixer\Fixer\Whitespace\ArrayIndentationFixer;
use PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer;
use PhpCsFixerCustomFixers\Fixer\NoDuplicatedImportsFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\CodingStandard\Fixer\Spacing\MethodChainingNewlineFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->parallel();

    $ecsConfig->paths([
        __DIR__ . '/src',
    ]);

    $ecsConfig->skip([
        __DIR__ . '/ecs.php',
        __DIR__ . '/src/DependencyInjection/Configuration.php',
    ]);

    $ecsConfig->sets([
        SetList::SYMPLIFY,
        SetList::PSR_12,
        SetList::DOCTRINE_ANNOTATIONS,
        SetList::CLEAN_CODE,
    ]);

    $ecsConfig->ruleWithConfiguration(YodaStyleFixer::class, [
        'equal' => false,
        'identical' => false,
        'less_and_greater' => false,
    ]);

    $ecsConfig->ruleWithConfiguration(PhpdocTypesOrderFixer::class, [
        'null_adjustment' => 'always_last',
        'sort_algorithm' => 'none',
    ]);

    $ecsConfig->ruleWithConfiguration(OrderedImportsFixer::class, [
        'imports_order' => ['class', 'function', 'const'],
    ]);

    $ecsConfig->ruleWithConfiguration(ConcatSpaceFixer::class, [
        'spacing' => 'one',
    ]);

    $ecsConfig->ruleWithConfiguration(LineLengthFixer::class, [
        LineLengthFixer::LINE_LENGTH => 120,
    ]);

    $ecsConfig->rule(NativeFunctionInvocationFixer::class);
    $ecsConfig->rule(NoTrailingCommaInSinglelineFixer::class);
    $ecsConfig->rule(MethodChainingNewlineFixer::class);
    $ecsConfig->rule(MethodChainingIndentationFixer::class);
    $ecsConfig->rule(StandaloneLineInMultilineArrayFixer::class);
    $ecsConfig->rule(ArrayIndentationFixer::class);

    $ecsConfig->rule(NoUnusedImportsFixer::class);
    $ecsConfig->rule(NoDuplicatedImportsFixer::class);
};
