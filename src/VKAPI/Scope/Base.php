<?php

namespace VKAPI\Scope;

use VKAPI\Request;

/**
 * Базовое пространство методов, доступных через API vk.com
 * @see https://vk.com/dev/methods
 */
class Base
{

    /** @var Request */
    protected $request;
    /** @var string Префикс, добавляемый к имени метода, для формирования корректного запроса к API */
    protected $scope_prefix;

    /**
     * @param Request $request
     */
    public function __construct(Request $request = null)
    {
        if ($request) {
            $this->setRequest($request);
        }
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, array $arguments = array())
    {
        $method = $this->getPrefix() . '.' . $method;
        $result = $this->getRequest()->execute($method, reset($arguments));

        return $result;
    }

    /**
     * Выставить обработчик запросов к api.vk.com
     * 
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Получить обработчик запросов к api.vk.com
     * 
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Выставить префикс, добавляемый к имени метода для формирования корректного запроса
     * 
     * @param string $scope_prefix
     */
    public function setPrefix($scope_prefix)
    {
        $this->scope_prefix = $scope_prefix;
    }

    /**
     * Получить префикс, добавляемый к имени метода для формирования корректного запроса
     * 
     * @return string
     */
    public function getPrefix()
    {
        return $this->scope_prefix;
    }

}