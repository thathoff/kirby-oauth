<?php

namespace Thathoff\Oauth;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

/**
 * OAuth user restored from the session (see PendingLogin);
 * only provides the generic `getId()` and `toArray()` methods
 * instead of the provider-specific resource owner class
 */
class SessionResourceOwner implements ResourceOwnerInterface
{
    /**
     * @param array<mixed> $data
     */
    public function __construct(
        private mixed $id,
        private array $data
    ) {
    }

    public function getId(): mixed
    {
        return $this->id;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
