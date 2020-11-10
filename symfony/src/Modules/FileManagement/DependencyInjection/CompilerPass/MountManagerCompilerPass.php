<?php


namespace App\Modules\FileManagement\DependencyInjection\CompilerPass;

use App\Modules\FileManagement\Enum\FileContext;
use League\Flysystem\MountManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class MountManagerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $enumValues = FileContext::values();

        $manager = $container->setDefinition('app.file_management.flysystem.mount_manager', (new Definition(MountManager::class))->setPublic(false));
        $container->setAlias(MountManager::class, 'app.file_management.flysystem.mount_manager');

        $definitions = array_keys($container->findTaggedServiceIds('flysystem.storage'));

        foreach($enumValues as $value) {
            if (!in_array($value.'.storage', $definitions) || !in_array($value.'.cache', $definitions)) {
                throw new \CompileError("Missing storage or cache for ".$value);
            }
        }

        foreach($definitions as $name) {
            $manager->addMethodCall('mountFilesystem', [$name, new Reference($name)]);
        }
    }

}