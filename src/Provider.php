<?php

namespace Blankogmbh\Oauth;

class Provider
{
    private $name = null;
    private $id = null;
    private $provider = null;
    public $data = [];

    public function __construct($id, array $config)
    {
        $this->id = $id;
        $this->name = !empty($config['name']) ? $config['name'] : ucwords($this->id);
        unset($config['name']);

        $class = "League\OAuth2\Client\Provider\GenericProvider";
        if (!empty($config['class']) && class_exists($config['class'])) {
            $class = $config['class'];
        }

        $this->provider = new $class($config);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->provider, $method], $args);
    }
}
