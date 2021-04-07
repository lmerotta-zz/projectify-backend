<?php


namespace App\Modules\UserManagement\ApiPlatform;

use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Core\GraphQl\Resolver\Stage\SerializeStageInterface;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use App\Entity\Security\User;
use Composer\EventDispatcher\EventSubscriberInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Service\Attribute\Required;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ResolveUserProfilePictureFieldSubscriber implements EventSubscriberInterface, SerializeStageInterface
{
    private UploaderHelper $storage;
    private CacheManager $cache;
    private SerializeStageInterface $stage;

    public function __invoke($itemOrCollection, string $resourceClass, string $operationName, array $context): ?array
    {
        if (is_a($resourceClass, User::class, true)) {
            $this->assignContentUrl($itemOrCollection);
        }

        return ($this->stage)($itemOrCollection, $resourceClass, $operationName, $context);
    }

    public function onPreSerialize(ViewEvent $event): void
    {

        $controllerResult = $event->getControllerResult();
        $request = $event->getRequest();

        if ($controllerResult instanceof Response || !$request->attributes->getBoolean('_api_respond', true)) {
            return;
        }

        if (!($attributes = RequestAttributesExtractor::extractAttributes($request)) || !\is_a($attributes['resource_class'], User::class, true)) {
            return;
        }

        $this->assignContentUrl($controllerResult);
    }

    private function assignContentUrl($itemOrCollection): void
    {
        $users = $itemOrCollection;

        if (!is_iterable($users)) {
            $users = [$users];
        }

        foreach ($users as $user) {
            if (!$user instanceof User) {
                continue;
            }

            $picturePath = $this->storage->asset($user, 'profilePictureFile');
            if ($picturePath) {
                $user->profilePictureUrl = $this->cache->getBrowserPath($picturePath, 'user_profile_picture');
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['onPreSerialize', EventPriorities::PRE_SERIALIZE],
        ];
    }

    #[Required]
    public function setStorage(UploaderHelper $storage)
    {
        $this->storage = $storage;
    }

    #[Required]
    public function setCache(CacheManager $cache)
    {
        $this->cache = $cache;
    }

    #[Required]
    public function setStage(SerializeStageInterface $stage)
    {
        $this->stage = $stage;
    }

}