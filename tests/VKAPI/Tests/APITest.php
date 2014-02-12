<?php

namespace VKAPI\Tests;

use VKAPI\API;
use VKAPI\Request;

class APITest extends \PHPUnit_Framework_TestCase
{

    public function testInstance()
    {
        $api = new API();

        $this->assertInstanceOf('\\VKAPI\\API', $api);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionCode 10
     */
    public function testGetInvalidScope()
    {
        $api = new API();
        $api->foo;
    }

    public function testGetValidScope()
    {
        $api   = new API();
        $users = $api->users;

        $this->assertInstanceOf('\\VKAPI\\Scope\\Base', $users);
    }

    public function testLazyArguments()
    {
        $api       = new API();
        $arguments = ['v' => '3.0'];

        $api->setArguments($arguments);

        $this->assertEquals($arguments, $api->getArguments());
    }

    public function testLazyRequest()
    {
        $args1 = ['v' => '3.0'];
        $args2 = ['v' => '5.7'];

        $api     = new API($args1);
        $request = new Request();
        $request->setArguments($args2);

        $api->setRequest($request);

        $this->assertNotEquals($args1, $api->getRequest()->getArguments());
        $this->assertEquals($args2, $api->getRequest()->getArguments());
        $this->assertInstanceOf('\\VKAPI\\Request', $api->getRequest());
    }

}