<?php

namespace App\Tests\Modules\Common\EventSubscriber;

use App\Modules\Common\EventSubscriber\LocaleSubscriber;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use function Symfony\Component\Translation\t;

class LocaleSubscriberTest extends TestCase
{
    use ProphecyTrait;

    public function testItSetsTheLocaleIfProvided()
    {
        $event = $this->prophesize(RequestEvent::class);

        $request = new Request();
        $request->headers->set('x-locale', 'de');
        $request->setLocale('fr');

        $event->getRequest()->shouldBeCalled()->willReturn($request);

        $subscriber = new LocaleSubscriber();
        $subscriber->onKernelRequest($event->reveal());

        $this->assertEquals('de', $request->getLocale());
    }
}
