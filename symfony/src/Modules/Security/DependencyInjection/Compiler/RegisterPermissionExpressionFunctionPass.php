<?php

namespace App\Modules\Security\DependencyInjection\Compiler;

use App\Modules\Security\ExpressionLanguage\PermissionExpressionFunctionProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

// @codeCoverageIgnoreStart
final class RegisterPermissionExpressionFunctionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $exprLangId = 'security.expression_language';

        if ($container->has($exprLangId) === false) {
            return;
        }

        $providerClass = PermissionExpressionFunctionProvider::class;
        $providerDef = new Definition($providerClass);

        $exprLangDef = $container->getDefinition($exprLangId);
        $exprLangDef->addMethodCall('registerProvider', [new Reference($providerClass)]);

        if ($container->hasDefinition('logger')) {
            $providerDef
                ->setArgument('$logger', new Reference('logger'));
        }
    }
}
// @codeCoverageIgnoreEnd

