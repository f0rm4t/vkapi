<?php

namespace VKAPI\Tests\Scope;

use VKAPI\Request;
use VKAPI\Scope\Base;

class BaseTest extends \PHPUnit_Framework_TestCase
{

    public function testInstance()
    {
        $request = new Request();
        $scope   = new Base($request);

        $this->assertInstanceOf('\\VKAPI\\Scope\\Base', $scope);
    }

    public function testMethodGet()
    {
        $request = new Request();
        $scope   = new Base($request);
        $scope->setPrefix('users');

        $user = $scope->get(['user_ids' => VK_USER_ID]);

        $this->assertInternalType('array', $user);
        $this->assertNotEmpty($user);
    }

    public function testLazyRequest()
    {
        $scope   = new Base();
        $request = new Request();

        $scope->setRequest($request);

        $this->assertInstanceOf('\\VKAPI\\Request', $scope->getRequest());
    }

    public function testLazyPrefix()
    {
        $scope = new Base();
        $scope->setPrefix('users');

        $this->assertEquals('users', $scope->getPrefix());
    }

}