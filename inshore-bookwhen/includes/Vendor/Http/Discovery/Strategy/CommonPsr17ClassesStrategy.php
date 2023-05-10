<?php

namespace InShore\Bookwhen\Vendor\Http\Discovery\Strategy;

use InShore\Bookwhen\Vendor\Psr\Http\Message\RequestFactoryInterface;
use InShore\Bookwhen\Vendor\Psr\Http\Message\ResponseFactoryInterface;
use InShore\Bookwhen\Vendor\Psr\Http\Message\ServerRequestFactoryInterface;
use InShore\Bookwhen\Vendor\Psr\Http\Message\StreamFactoryInterface;
use InShore\Bookwhen\Vendor\Psr\Http\Message\UploadedFileFactoryInterface;
use InShore\Bookwhen\Vendor\Psr\Http\Message\UriFactoryInterface;
/**
 * @internal
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 *
 * Don't miss updating src/Composer/Plugin.php when adding a new supported class.
 */
final class CommonPsr17ClassesStrategy implements DiscoveryStrategy
{
    /**
     * @var array
     */
    private static $classes = [RequestFactoryInterface::class => ['InShore\\Bookwhen\\Vendor\\Phalcon\\Http\\Message\\RequestFactory', 'InShore\\Bookwhen\\Vendor\\Nyholm\\Psr7\\Factory\\Psr17Factory', 'InShore\\Bookwhen\\Vendor\\GuzzleHttp\\Psr7\\HttpFactory', 'InShore\\Bookwhen\\Vendor\\Http\\Factory\\Diactoros\\RequestFactory', 'InShore\\Bookwhen\\Vendor\\Http\\Factory\\Guzzle\\RequestFactory', 'InShore\\Bookwhen\\Vendor\\Http\\Factory\\Slim\\RequestFactory', 'InShore\\Bookwhen\\Vendor\\Laminas\\Diactoros\\RequestFactory', 'InShore\\Bookwhen\\Vendor\\Slim\\Psr7\\Factory\\RequestFactory'], ResponseFactoryInterface::class => ['InShore\\Bookwhen\\Vendor\\Phalcon\\Http\\Message\\ResponseFactory', 'InShore\\Bookwhen\\Vendor\\Nyholm\\Psr7\\Factory\\Psr17Factory', 'InShore\\Bookwhen\\Vendor\\GuzzleHttp\\Psr7\\HttpFactory', 'InShore\\Bookwhen\\Vendor\\Http\\Factory\\Diactoros\\ResponseFactory', 'InShore\\Bookwhen\\Vendor\\Http\\Factory\\Guzzle\\ResponseFactory', 'InShore\\Bookwhen\\Vendor\\Http\\Factory\\Slim\\ResponseFactory', 'InShore\\Bookwhen\\Vendor\\Laminas\\Diactoros\\ResponseFactory', 'InShore\\Bookwhen\\Vendor\\Slim\\Psr7\\Factory\\ResponseFactory'], ServerRequestFactoryInterface::class => ['InShore\\Bookwhen\\Vendor\\Phalcon\\Http\\Message\\ServerRequestFactory', 'InShore\\Bookwhen\\Vendor\\Nyholm\\Psr7\\Factory\\Psr17Factory', 'InShore\\Bookwhen\\Vendor\\GuzzleHttp\\Psr7\\HttpFactory', 'InShore\\Bookwhen\\Vendor\\Http\\Factory\\Diactoros\\ServerRequestFactory', 'InShore\\Bookwhen\\Vendor\\Http\\Factory\\Guzzle\\ServerRequestFactory', 'InShore\\Bookwhen\\Vendor\\Http\\Factory\\Slim\\ServerRequestFactory', 'InShore\\Bookwhen\\Vendor\\Laminas\\Diactoros\\ServerRequestFactory', 'InShore\\Bookwhen\\Vendor\\Slim\\Psr7\\Factory\\ServerRequestFactory'], StreamFactoryInterface::class => ['InShore\\Bookwhen\\Vendor\\Phalcon\\Http\\Message\\StreamFactory', 'InShore\\Bookwhen\\Vendor\\Nyholm\\Psr7\\Factory\\Psr17Factory', 'InShore\\Bookwhen\\Vendor\\GuzzleHttp\\Psr7\\HttpFactory', 'InShore\\Bookwhen\\Vendor\\Http\\Factory\\Diactoros\\StreamFactory', 'InShore\\Bookwhen\\Vendor\\Http\\Factory\\Guzzle\\StreamFactory', 'InShore\\Bookwhen\\Vendor\\Http\\Factory\\Slim\\StreamFactory', 'InShore\\Bookwhen\\Vendor\\Laminas\\Diactoros\\StreamFactory', 'InShore\\Bookwhen\\Vendor\\Slim\\Psr7\\Factory\\StreamFactory'], UploadedFileFactoryInterface::class => ['InShore\\Bookwhen\\Vendor\\Phalcon\\Http\\Message\\UploadedFileFactory', 'InShore\\Bookwhen\\Vendor\\Nyholm\\Psr7\\Factory\\Psr17Factory', 'InShore\\Bookwhen\\Vendor\\GuzzleHttp\\Psr7\\HttpFactory', 'InShore\\Bookwhen\\Vendor\\Http\\Factory\\Diactoros\\UploadedFileFactory', 'InShore\\Bookwhen\\Vendor\\Http\\Factory\\Guzzle\\UploadedFileFactory', 'InShore\\Bookwhen\\Vendor\\Http\\Factory\\Slim\\UploadedFileFactory', 'InShore\\Bookwhen\\Vendor\\Laminas\\Diactoros\\UploadedFileFactory', 'InShore\\Bookwhen\\Vendor\\Slim\\Psr7\\Factory\\UploadedFileFactory'], UriFactoryInterface::class => ['InShore\\Bookwhen\\Vendor\\Phalcon\\Http\\Message\\UriFactory', 'InShore\\Bookwhen\\Vendor\\Nyholm\\Psr7\\Factory\\Psr17Factory', 'InShore\\Bookwhen\\Vendor\\GuzzleHttp\\Psr7\\HttpFactory', 'InShore\\Bookwhen\\Vendor\\Http\\Factory\\Diactoros\\UriFactory', 'InShore\\Bookwhen\\Vendor\\Http\\Factory\\Guzzle\\UriFactory', 'InShore\\Bookwhen\\Vendor\\Http\\Factory\\Slim\\UriFactory', 'InShore\\Bookwhen\\Vendor\\Laminas\\Diactoros\\UriFactory', 'InShore\\Bookwhen\\Vendor\\Slim\\Psr7\\Factory\\UriFactory']];
    /**
     * {@inheritdoc}
     */
    public static function getCandidates($type)
    {
        $candidates = [];
        if (isset(self::$classes[$type])) {
            foreach (self::$classes[$type] as $class) {
                $candidates[] = ['class' => $class, 'condition' => [$class]];
            }
        }
        return $candidates;
    }
}
