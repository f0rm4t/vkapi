<?php

namespace VKAPI;

use VKAPI\Request;

/**
 * Базовый класс взаимодействия с API VK.com
 */
class API
{

    /** @integer Запрошено не корректное пространство методов */
    const ERROR_INVALID_SCOPE = 10;

    /** @var array */
    protected $arguments = [];
    /** @var Request */
    protected $request;
    /** @var array */
    protected $scope = [];

    /**
     * @param array $arguments Указанный список аргументов будет добавляться ко всем запросам, 
     *                         отправляемым на api.vk.com. Например, аргумент access_token может быть обязателен для 
     *                         выполнения некоторых типов запросов
     */
    public function __construct(array $arguments = array())
    {
        if ($arguments) {
            $this->setArguments($arguments);
        }
    }

    /**
     * @param string $key
     * @return \VKAPI\Scope\Base
     * @throws \InvalidArgumentException Если пространство методов не найдено
     */
    public function __get($key)
    {
        if ( ! isset($this->scope[$key])) {
            $scope = '\\VKAPI\\Scope\\' . ucfirst($key);

            if ( ! class_exists($scope)) {
                throw new \InvalidArgumentException(
                    sprintf('Commands scope `%s` not found', $key),
                    self::ERROR_INVALID_SCOPE
                );
            }

            $this->scope[$key] = new $scope($this->getRequest());
            $this->scope[$key]->setPrefix($key);
        }

        return $this->scope[$key];
    }

    /**
     * Выставить базовые аргументы. Переданные аргументы будут добавляться ко всем запросам, отправляемым на api.vk.com
     * 
     * @param array $arguments
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * Получить список базовых аргументов
     * 
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Выставить обработчик, взаимодействующий с api.vk.com
     * 
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Получить обработчик, взаимодействующий с api.vk.com. 
     * Если он не был инициализирован ранее, будет создан экземпляр обработчика по умолчанию.
     * 
     * @return Request
     */
    public function getRequest()
    {
        if ( ! $this->request) {
            $this->setRequest(new Request($this->getArguments()));
        }

        return $this->request;
    }

}