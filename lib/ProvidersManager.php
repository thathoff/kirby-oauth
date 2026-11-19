<?php

namespace Thathoff\Oauth;

use Kirby\Cms\App;
use Kirby\Toolkit\Collection;

/**
 * @extends Collection<Provider>
 */
class ProvidersManager extends Collection
{
    private App $kirby;

    public function __construct(App $kirby)
    {
        $this->kirby = $kirby;
        $providers = $this->kirby->option('thathoff.oauth.providers');

        if (!is_array($providers)) {
            return;
        }

        foreach ($providers as $id => $provider) {
            if (empty($provider['redirectUri'])) {
                $provider['redirectUri'] = static::loginUrl($this->kirby, $id);
            }

            $this->set($id, new Provider($id, $provider));
        }
    }

    /**
     * Returns the URL of the login route for a provider,
     * which is also used as the default redirect URL
     */
    public static function loginUrl(App $kirby, string $id): string
    {
        return $kirby->url() . '/oauth/login/' . $id;
    }

    /**
     * Returns the data needed for a Panel login button
     *
     * @return array<string, string|null>
     */
    public static function toPanelItem(App $kirby, Provider $provider): array
    {
        return [
            'id'    => $provider->getId(),
            'name'  => $provider->getName(),
            'href'  => static::loginUrl($kirby, $provider->getId()),
            'icon'  => $provider->getIcon(),
            'theme' => $provider->getTheme(),
        ];
    }

    /**
     * Returns the data needed for the Panel login buttons
     *
     * @return array<int, array<string, string|null>>
     */
    public function toPanel(): array
    {
        return array_values($this->toArray(
            fn (Provider $provider) => static::toPanelItem($this->kirby, $provider)
        ));
    }
}
