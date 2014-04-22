<?php

namespace VKAPI;

use VKAPI\Exception\CURL as CURLException;
use VKAPI\Exception\JSON as JSONException;
use VKAPI\Exception\VK as VKException;

/**
 * Обработчик взаимодействия с api.vk.com
 */
class Request
{

    /**
     * @var array Базовые аргументы, которые будут добавляться ко всем запросам, отправляемым на api.vk.com
     */
    protected $arguments = [
        'v' => '5.9'
    ];

    /**
     * @param array $arguments Базовые аргументы, которые будут добавляться ко всем запросам, 
     *                         отправляемым на api.vk.com
     * @see Request::setArguments()
     */
    public function __construct(array $arguments = array())
    {
        if ($arguments) {
            $this->setArguments($arguments);
        }
    }

    /**
     * Дополнить указанные ранее базовые аргументы. 
     * Обратите внимание, что этот метод не заменяет, а расширяет переданный ранее список аргументов
     * 
     * @param array $arguments
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = array_merge($this->arguments, $arguments);
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
     * Выполнить запрос на api.vk.com и вернуть рассериализованный объект ответа
     * 
     * @param string $method Имя метода, определенного на api.vk.com
     * @param array $arguments Аргументы, которые будут объеденены с базовыми, для передачи их в рапросе к api.vk.com
     * @return mixed Рассериализованный объект ответа
     * @throws \VKAPI\Exception\VK Если произошла ошибка на стороне API vk.com
     * @see https://vk.com/dev/methods
     */
    public function execute($method, array $arguments = array())
    {
        $arguments = array_merge($this->getArguments(), $arguments);
        $url       = sprintf('https://api.vk.com/method/%s?%s', $method, http_build_query($arguments));
        $response  = $this->sendRequest($url);
        $data      = $this->decodeResponse($response);

        if (isset($data->error)) {
            $errors = $data->error;
        } elseif (isset($data->execute_errors))  {
            $errors = reset($data->execute_errors);
        }

        if ( ! empty($errors)) {
            $exception = new VKException($errors->error_msg, $errors->error_code);
            $exception->setData($errors);

            throw $exception;
        }

        return $data->response;
    }

    /**
     * Выполнение запроса
     * 
     * @param string $url
     * @return mixed
     * @throws \VKAPI\Exception\CURL Если произошла ошибка взаимодействия с api.vk.com
     */
    protected function sendRequest($url)
    {
        $curl = $this->getCurl();
        curl_setopt($curl, CURLOPT_URL, $url);

        $data = curl_exec($curl);
        if ($curl_errno = curl_errno($curl)) {
            throw new CURLException(curl_error($curl), $curl_errno);
        }

        curl_close($curl);

        return $data;
    }

    /**
     * Создание экземпляра cURL для выполнения запроса
     * 
     * @return resource
     */
    protected function getCurl()
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FAILONERROR    => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT 	   => 5
        ]);

        return $curl;
    }

    /**
     * Десериализация ответа api.vk.com
     * 
     * @param string $response
     * @return mixed
     * @throws \VKAPI\Exception\JSON Если произошла ошибка десериализации или был получен пустой ответ
     */
    protected function decodeResponse($response)
    {
        $data = json_decode($response);

        if ($error = json_last_error()) {
            throw new JSONException($error);
        }

        if ($data === null) {
            throw new JSONException('Empty VK response');
        }

        return $data;
    }

}
