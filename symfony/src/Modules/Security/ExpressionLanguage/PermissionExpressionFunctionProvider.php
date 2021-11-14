<?php

namespace App\Modules\Security\ExpressionLanguage;

use App\Contracts\Security\Enum\Permission;
use App\Modules\Common\Traits\Logger;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

final class PermissionExpressionFunctionProvider implements ExpressionFunctionProviderInterface
{
    use Logger;
    /**
     * @return ExpressionFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new ExpressionFunction(
                'permission',
                static function (): void {
                },
                function ($params, string $permission): string {
                    $constant = \sprintf('%s::%s', Permission::class, $permission);

                    try {
                        return \constant($constant);
                        // @codeCoverageIgnoreStart
                    } catch (\Throwable $throwable) {
                        $this->logger->error(\sprintf('Constant "%s" not found', $constant));
                        throw $throwable;
                    }
                    // @codeCoverageIgnoreEnd
                }
            ),
        ];
    }
}
