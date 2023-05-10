<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper6af4d594edb1\Symfony\Component\HttpClient;

use _PhpScoper6af4d594edb1\Symfony\Contracts\HttpClient\HttpClientInterface;
use _PhpScoper6af4d594edb1\Symfony\Contracts\HttpClient\ResponseInterface;
use _PhpScoper6af4d594edb1\Symfony\Contracts\Service\ResetInterface;
class UriTemplateHttpClient implements HttpClientInterface, ResetInterface
{
    use DecoratorTrait;
    /**
     * @param (\Closure(string $url, array $vars): string)|null $expander
     */
    public function __construct(HttpClientInterface $client = null, private ?\Closure $expander = null, private array $defaultVars = [])
    {
        $this->client = $client ?? HttpClient::create();
    }
    public function request(string $method, string $url, array $options = []) : ResponseInterface
    {
        $vars = $this->defaultVars;
        if (\array_key_exists('vars', $options)) {
            if (!\is_array($options['vars'])) {
                throw new \InvalidArgumentException('The "vars" option must be an array.');
            }
            $vars = [...$vars, ...$options['vars']];
            unset($options['vars']);
        }
        if ($vars) {
            $url = ($this->expander ??= $this->createExpanderFromPopularVendors())($url, $vars);
        }
        return $this->client->request($method, $url, $options);
    }
    public function withOptions(array $options) : static
    {
        if (!\is_array($options['vars'] ?? [])) {
            throw new \InvalidArgumentException('The "vars" option must be an array.');
        }
        $clone = clone $this;
        $clone->defaultVars = [...$clone->defaultVars, ...$options['vars'] ?? []];
        unset($options['vars']);
        $clone->client = $this->client->withOptions($options);
        return $clone;
    }
    /**
     * @return \Closure(string $url, array $vars): string
     */
    private function createExpanderFromPopularVendors() : \Closure
    {
        if (\class_exists(\_PhpScoper6af4d594edb1\GuzzleHttp\UriTemplate\UriTemplate::class)) {
            return \_PhpScoper6af4d594edb1\GuzzleHttp\UriTemplate\UriTemplate::expand(...);
        }
        if (\class_exists(\_PhpScoper6af4d594edb1\League\Uri\UriTemplate::class)) {
            return static fn(string $url, array $vars): string => (new \_PhpScoper6af4d594edb1\League\Uri\UriTemplate($url))->expand($vars);
        }
        if (\class_exists(\_PhpScoper6af4d594edb1\Rize\UriTemplate::class)) {
            return (new \_PhpScoper6af4d594edb1\Rize\UriTemplate())->expand(...);
        }
        throw new \LogicException('Support for URI template requires a vendor to expand the URI. Run "composer require guzzlehttp/uri-template" or pass your own expander \\Closure implementation.');
    }
}
