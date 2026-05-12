<?php

namespace Thathoff\Oauth;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessTokenInterface;

/**
 * @method string getState()
 * @method AccessTokenInterface getAccessToken(string $grant, array<string, mixed> $options = [])
 * @method ResourceOwnerInterface getResourceOwner(AccessTokenInterface $token)
 */
class Provider
{
    private string $name;
    private string $id;
    private AbstractProvider $provider;
    private ?string $state = null;
    private ?string $icon = null;
    private ?string $theme = null;
    private ?string $scope = null;
    private string $emailField = 'email';
    private ?bool $emailVerified = null;
    /** @var array<string, mixed>|null */
    private ?array $getAuthorizationUrlOptions = null;
    /** @var array<string, mixed> */
    public array $data = [];

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(string $id, array $config)
    {
        $this->id = $id;

        $this->icon = !empty($config['icon']) ? $config['icon'] : null;
        unset($config['icon']);

        $this->theme = !empty($config['theme']) ? $config['theme'] : null;
        unset($config['theme']);

        $this->name = !empty($config['name']) ? $config['name'] : ucwords($this->id);
        unset($config['name']);

        $this->state = !empty($config['state']) ? $config['state'] : null;
        unset($config['state']);

        $this->scope = !empty($config['scope']) ? $config['scope'] : null;
        unset($config['scope']);

        $this->emailField = !empty($config['emailField']) ? $config['emailField'] : 'email';
        unset($config['emailField']);

        if (array_key_exists('emailVerified', $config)) {
            $this->emailVerified = $config['emailVerified'];
        }
        unset($config['emailVerified']);

        $class = "League\OAuth2\Client\Provider\GenericProvider";
        if (!empty($config['class']) && class_exists($config['class'])) {
            $class = $config['class'];
        }

        $this->getAuthorizationUrlOptions = !empty($config['getAuthorizationUrlOptions']) ? $config['getAuthorizationUrlOptions'] : null;
        unset($config['getAuthorizationUrlOptions']);

        $this->provider = new $class($config);
    }

    /**
     * @param array<string, mixed>|null $options
     */
    public function getAuthorizationUrl(?array $options = null): string
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function getEmailField(): string
    {
        return $this->emailField;
    }

    public function getEmailVerified(): ?bool
    {
        return $this->emailVerified;
    }

    /**
     * @param array<int, mixed> $args
     */
    public function __call(string $method, array $args): mixed
    {
        return call_user_func_array([$this->provider, $method], $args);
    }
}
