<?php

namespace VKAPI\Tests;

use VKAPI\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
{

    public function testInstance()
    {
        $request = new Request();

        $this->assertInstanceOf('\\VKAPI\\Request', $request);
    }

    public function testExecute()
    {
        $request = new Request();
        $user    = $request->execute('users.get', ['user_ids' => VK_USER_ID]);

        $this->assertInternalType('array', $user);
        $this->assertNotEmpty($user);
    }

    public function testLazyArguments()
    {
        $request   = new Request();
        $arguments = ['v' => '3.0'];

        $request->setArguments($arguments);

        $this->assertEquals($arguments, $request->getArguments());
    }

    /**
     * @expectedException \VKAPI\Exception\CURL
     */
    public function testCurlError()
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_HTTPPROXYTUNNEL => true,
            CURLOPT_PROXY           => 'http://256.256.256.256:1111/'
        ]);

        $stub = $this->getMock('\\VKAPI\\Request', ['getCurl']);
        $stub->expects($this->any())
             ->method('getCurl')
             ->will($this->returnValue($curl));

        $stub->execute('users.get', ['user_ids' => VK_USER_ID]);
    }

    /**
     * @expectedException \VKAPI\Exception\JSON
     */
    public function testJsonError()
    {
        $stub = $this->getMock('\\VKAPI\\Request', ['sendRequest']);
        $stub->expects($this->any())
             ->method('sendRequest')
             ->with($this->anything())
             ->will($this->returnValue(null));

        $stub->execute('users.get', ['user_ids' => VK_USER_ID]);
    }

    /**
     * @expectedException \VKAPI\Exception\VK
     */
    public function testVKError()
    {
        $error = '{"error":{"error_code":14,"error_msg":"Captcha needed","request_params":[{"key":"oauth","value":"1"},' 
               . '{"key":"method","value":"captcha.force"},{"key":"uids","value":"66748"},{"key":"access_token","value"' 
               . ':"b9b5151856dcc745d785a6b604295d30888a827a37763198888d8b7f5271a4d8a049fefbaeed791b2882"}],"captcha_si'
               . 'd":"239633676097","captcha_img":"http:\/\/api.vk.com\/captcha.php?sid=239633676097&s=1"}}';

        $stub  = $this->getMock('\\VKAPI\\Request', ['decodeResponse']);
        $stub->expects($this->any())
             ->method('decodeResponse')
             ->with($this->anything())
             ->will($this->returnValue(json_decode($error)));

        $stub->execute('users.get', ['user_ids' => VK_USER_ID]);
    }

}