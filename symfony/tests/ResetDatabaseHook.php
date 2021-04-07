<?php

namespace App\Tests;

use PHPUnit\Runner\BeforeFirstTestHook;

class ResetDatabaseHook implements BeforeFirstTestHook
{
    public function executeBeforeFirstTest(): void
    {
        passthru('php '.__DIR__.'/../bin/console doctrine:database:drop --force --env=test');
        passthru('php '.__DIR__.'/../bin/console doctrine:database:create --env=test');
        passthru('php '.__DIR__.'/../bin/console doctrine:migrations:migrate -n --env=test');
        passthru('php '.__DIR__.'/../bin/console doctrine:fixtures:load -n --append --env=test');
    }

}