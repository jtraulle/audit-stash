<?php

namespace AuditStash\Test\Persister;

use AuditStash\Event\AuditDeleteEvent;
use AuditStash\Meta\RequestMetadata;
use Cake\Event\EventDispatcherTrait;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

class RequestMetadataTest extends TestCase
{

    use EventDispatcherTrait;

    /**
     * Tests that request metadata is added to the audit log objects.
     *
     * @return void
     */
    public function testRequestDataIsAdded()
    {
        $request = $this->getMockBuilder(ServerRequest::class)
                        ->setConstructorArgs(['clientIp', 'here'])
                        ->getMock();
        $listener = new RequestMetadata($request, 'jose');
        $this->getEventManager()->on($listener);

        $request->expects($this->once())->method('clientIp')->will($this->returnValue('12345'));
        $request->expects($this->once())->method('here')->will($this->returnValue('/things?a=b'));
        $logs[] = new AuditDeleteEvent(1234, 1, 'articles');
        $event = $this->dispatchEvent('AuditStash.beforeLog', ['logs' => $logs]);

        $expected = [
            'ip' => '12345',
            'url' => '/things?a=b',
            'user' => 'jose'
        ];
        $this->assertEquals($expected, $logs[0]->getMetaInfo());
    }
}
