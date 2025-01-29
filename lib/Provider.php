<?php

namespace Thathoff\Oauth;

class Provider
{
    private $name = null;
    private $id = null;
    private $provider = null;
    private $state = null;
    private $icon = null;
    private $scope = null;
    private $getAuthorizationUrlOptions = null;
    public $data = [];

    public function __construct($id, array $config)
    {
        $this->id = $id;

        $this->icon = !empty($config['icon']) ? $config['icon'] : null;
        unset($config['icon']);

        $this->name = !empty($config['name']) ? $config['name'] : ucwords($this->id);
        unset($config['name']);

        $this->state = !empty($config['state']) ? $config['state'] : null;
        unset($config['state']);

        $this->scope = !empty($config['scope']) ? $config['scope'] : null;
        unset($config['scope']);

        $class = "League\OAuth2\Client\Provider\GenericProvider";
        if (!empty($config['class']) && class_exists($config['class'])) {
            $class = $config['class'];
        }

        $this->getAuthorizationUrlOptions = !empty($config['getAuthorizationUrlOptions']) ? $config['getAuthorizationUrlOptions'] : null;
        unset($config['getAuthorizationUrlOptions']);

        $this->provider = new $class($config);
    }

    public function getAuthorizationUrl($options = null)
    {
        if (!is_array($options)) {
            $options = $this->getAuthorizationUrlOptions;
        }

        if (!is_array($options)) {
            $options  = [];
        }

        if (!isset($options['state']) && $this->state) {
            $options['state'] = $this->state;
        }

        if (!isset($options['scope']) && $this->scope) {
            $options['scope'] = $this->scope;
        }

        return $this->__call("getAuthorizationUrl", [$options]);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->provider, $method], $args);
    }
}
