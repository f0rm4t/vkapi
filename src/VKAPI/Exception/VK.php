<?php

namespace VKAPI\Exception;

class VK extends \Exception
{

    protected $data;

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

}