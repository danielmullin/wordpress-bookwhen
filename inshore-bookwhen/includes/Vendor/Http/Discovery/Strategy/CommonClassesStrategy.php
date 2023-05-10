<?php

namespace InShore\Bookwhen\Vendor\Http\Discovery\Strategy;

use InShore\Bookwhen\Vendor\GuzzleHttp\Client as GuzzleHttp;
use InShore\Bookwhen\Vendor\GuzzleHttp\Promise\Promise;
use InShore\Bookwhen\Vendor\GuzzleHttp\Psr7\Request as GuzzleRequest;
use InShore\Bookwhen\Vendor\Http\Adapter\Artax\Client as Artax;
use InShore\Bookwhen\Vendor\Http\Adapter\Buzz\Client as Buzz;
use InShore\Bookwhen\Vendor\Http\Adapter\Cake\Client as Cake;
use InShore\Bookwhen\Vendor\Http\Adapter\Guzzle5\Client as Guzzle5;
use InShore\Bookwhen\Vendor\Http\Adapter\Guzzle6\Client as Guzzle6;
use InShore\Bookwhen\Vendor\Http\Adapter\Guzzle7\Client as Guzzle7;
use InShore\Bookwhen\Vendor\Http\Adapter\React\Client as React;
use InShore\Bookwhen\Vendor\Http\Client\Curl\Client as Curl;
use InShore\Bookwhen\Vendor\Http\Client\HttpAsyncClient;
use InShore\Bookwhen\Vendor\Http\Client\HttpClient;
use InShore\Bookwhen\Vendor\Http\Client\Socket\Client as Socket;
use InShore\Bookwhen\Vendor\Http\Discovery\ClassDiscovery;
use InShore\Bookwhen\Vendor\Http\Discovery\Exception\NotFoundException;
use InShore\Bookwhen\Vendor\Http\Discovery\MessageFactoryDiscovery;
use InShore\Bookwhen\Vendor\Http\Discovery\Psr17FactoryDiscovery;
use InShore\Bookwhen\Vendor\Http\Message\MessageFactory;
use InShore\Bookwhen\Vendor\Http\Message\MessageFactory\DiactorosMessageFactory;
use InShore\Bookwhen\Vendor\Http\Message\MessageFactory\GuzzleMessageFactory;
use InShore\Bookwhen\Vendor\Http\Message\MessageFactory\SlimMessageFactory;
use InShore\Bookwhen\Vendor\Http\Message\StreamFactory;
use InShore\Bookwhen\Vendor\Http\Message\StreamFactory\DiactorosStreamFactory;
use InShore\Bookwhen\Vendor\Http\Message\StreamFactory\GuzzleStreamFactory;
use InShore\Bookwhen\Vendor\Http\Message\StreamFactory\SlimStreamFactory;
use InShore\Bookwhen\Vendor\Http\Message\UriFactory;
use InShore\Bookwhen\Vendor\Http\Message\UriFactory\DiactorosUriFactory;
use InShore\Bookwhen\Vendor\Http\Message\UriFactory\GuzzleUriFactory;
use InShore\Bookwhen\Vendor\Http\Message\UriFactory\SlimUriFactory;
use InShore\Bookwhen\Vendor\Laminas\Diactoros\Request as DiactorosRequest;
use InShore\Bookwhen\Vendor\Nyholm\Psr7\Factory\HttplugFactory as NyholmHttplugFactory;
use InShore\Bookwhen\Vendor\Psr\Http\Client\ClientInterface as Psr18Client;
use InShore\Bookwhen\Vendor\Psr\Http\Message\RequestFactoryInterface as Psr17RequestFactory;
use InShore\Bookwhen\Vendor\Slim\Http\Request as SlimRequest;
use InShore\Bookwhen\Vendor\Symfony\Component\HttpClient\HttplugClient as SymfonyHttplug;
use InShore\Bookwhen\Vendor\Symfony\Component\HttpClient\Psr18Client as SymfonyPsr18;
/**
 * @internal
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 *
 * Don't miss updating src/Composer/Plugin.php when adding a new supported class.
 */
final class CommonClassesStrategy implements DiscoveryStrategy
{
    /**
     * @var array
     */
    private static $classes = [MessageFactory::class => [['class' => NyholmHttplugFactory::class, 'condition' => [NyholmHttplugFactory::class]], ['class' => GuzzleMessageFactory::class, 'condition' => [GuzzleRequest::class, GuzzleMessageFactory::class]], ['class' => DiactorosMessageFactory::class, 'condition' => [DiactorosRequest::class, DiactorosMessageFactory::class]], ['class' => SlimMessageFactory::class, 'condition' => [SlimRequest::class, SlimMessageFactory::class]]], StreamFactory::class => [['class' => NyholmHttplugFactory::class, 'condition' => [NyholmHttplugFactory::class]], ['class' => GuzzleStreamFactory::class, 'condition' => [GuzzleRequest::class, GuzzleStreamFactory::class]], ['class' => DiactorosStreamFactory::class, 'condition' => [DiactorosRequest::class, DiactorosStreamFactory::class]], ['class' => SlimStreamFactory::class, 'condition' => [SlimRequest::class, SlimStreamFactory::class]]], UriFactory::class => [['class' => NyholmHttplugFactory::class, 'condition' => [NyholmHttplugFactory::class]], ['class' => GuzzleUriFactory::class, 'condition' => [GuzzleRequest::class, GuzzleUriFactory::class]], ['class' => DiactorosUriFactory::class, 'condition' => [DiactorosRequest::class, DiactorosUriFactory::class]], ['class' => SlimUriFactory::class, 'condition' => [SlimRequest::class, SlimUriFactory::class]]], HttpAsyncClient::class => [['class' => SymfonyHttplug::class, 'condition' => [SymfonyHttplug::class, Promise::class, [self::class, 'isPsr17FactoryInstalled']]], ['class' => Guzzle7::class, 'condition' => Guzzle7::class], ['class' => Guzzle6::class, 'condition' => Guzzle6::class], ['class' => Curl::class, 'condition' => Curl::class], ['class' => React::class, 'condition' => React::class]], HttpClient::class => [['class' => SymfonyHttplug::class, 'condition' => [SymfonyHttplug::class, [self::class, 'isPsr17FactoryInstalled']]], ['class' => Guzzle7::class, 'condition' => Guzzle7::class], ['class' => Guzzle6::class, 'condition' => Guzzle6::class], ['class' => Guzzle5::class, 'condition' => Guzzle5::class], ['class' => Curl::class, 'condition' => Curl::class], ['class' => Socket::class, 'condition' => Socket::class], ['class' => Buzz::class, 'condition' => Buzz::class], ['class' => React::class, 'condition' => React::class], ['class' => Cake::class, 'condition' => Cake::class], ['class' => Artax::class, 'condition' => Artax::class], ['class' => [self::class, 'buzzInstantiate'], 'condition' => [\InShore\Bookwhen\Vendor\Buzz\Client\FileGetContents::class, \InShore\Bookwhen\Vendor\Buzz\Message\ResponseBuilder::class]]], Psr18Client::class => [['class' => [self::class, 'symfonyPsr18Instantiate'], 'condition' => [SymfonyPsr18::class, Psr17RequestFactory::class]], ['class' => GuzzleHttp::class, 'condition' => [self::class, 'isGuzzleImplementingPsr18']], ['class' => [self::class, 'buzzInstantiate'], 'condition' => [\InShore\Bookwhen\Vendor\Buzz\Client\FileGetContents::class, \InShore\Bookwhen\Vendor\Buzz\Message\ResponseBuilder::class]]]];
    /**
     * {@inheritdoc}
     */
    public static function getCandidates($type)
    {
        if (Psr18Client::class === $type) {
            return self::getPsr18Candidates();
        }
        return self::$classes[$type] ?? [];
    }
    /**
     * @return array The return value is always an array with zero or more elements. Each
     *               element is an array with two keys ['class' => string, 'condition' => mixed].
     */
    private static function getPsr18Candidates()
    {
        $candidates = self::$classes[Psr18Client::class];
        // HTTPlug 2.0 clients implements PSR18Client too.
        foreach (self::$classes[HttpClient::class] as $c) {
            if (!\is_string($c['class'])) {
                continue;
            }
            try {
                if (ClassDiscovery::safeClassExists($c['class']) && \is_subclass_of($c['class'], Psr18Client::class)) {
                    $candidates[] = $c;
                }
            } catch (\Throwable $e) {
                \trigger_error(\sprintf('Got exception "%s (%s)" while checking if a PSR-18 Client is available', \get_class($e), $e->getMessage()), \E_USER_WARNING);
            }
        }
        return $candidates;
    }
    public static function buzzInstantiate()
    {
        return new \InShore\Bookwhen\Vendor\Buzz\Client\FileGetContents(MessageFactoryDiscovery::find());
    }
    public static function symfonyPsr18Instantiate()
    {
        return new SymfonyPsr18(null, Psr17FactoryDiscovery::findResponseFactory(), Psr17FactoryDiscovery::findStreamFactory());
    }
    public static function isGuzzleImplementingPsr18()
    {
        return \defined('InShore\\Bookwhen\\Vendor\\GuzzleHttp\\ClientInterface::MAJOR_VERSION');
    }
    /**
     * Can be used as a condition.
     *
     * @return bool
     */
    public static function isPsr17FactoryInstalled()
    {
        try {
            Psr17FactoryDiscovery::findResponseFactory();
        } catch (NotFoundException $e) {
            return \false;
        } catch (\Throwable $e) {
            \trigger_error(\sprintf('Got exception "%s (%s)" while checking if a PSR-17 ResponseFactory is available', \get_class($e), $e->getMessage()), \E_USER_WARNING);
            return \false;
        }
        return \true;
    }
}
