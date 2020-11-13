<?php

namespace App\Tests\Modules\FileManagement\DependencyInjection\CompilerPass;

use App\Modules\FileManagement\DependencyInjection\CompilerPass\FileRetrieverCompilerPass;
use App\Modules\FileManagement\FileStorage\FileRetriever;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FileRetrieverCompilerPassTest extends TestCase
{
    public function testProcess()
    {
        $container = new ContainerBuilder();
        $container->register(FileRetriever::class, FileRetriever::class);

        $container->register('test.retriever')->addTag('app.file_management.file_retriever_concrete');


        (new FileRetrieverCompilerPass())->process($container);

        $definition = $container->findDefinition(FileRetriever::class);

        $this->assertCount(1, $definition->getMethodCalls());
        $this->assertEquals(['addRetriever', [new Reference('test.retriever')]], $definition->getMethodCalls()[0]);
    }
}
