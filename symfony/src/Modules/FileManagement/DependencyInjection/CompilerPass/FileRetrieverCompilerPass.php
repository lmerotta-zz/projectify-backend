<?php


namespace App\Modules\FileManagement\DependencyInjection\CompilerPass;

use App\Contracts\FileManagement\Enum\FileContext;
use App\Modules\FileManagement\FileStorage\FileRetriever;
use League\Flysystem\MountManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class FileRetrieverCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(FileRetriever::class)) {
            // @codeCoverageIgnoreStart
            return;
            // @codeCoverageIgnoreEnd
        }

        $definition = $container->findDefinition(FileRetriever::class);

        $tagged = $container->findTaggedServiceIds('app.file_management.file_retriever_concrete');

        foreach ($tagged as $id => $tags) {
            $definition->addMethodCall('addRetriever', [new Reference($id)]);
        }
    }
}
