<?php

namespace Francerz\WebappMigration;

use Fig\Http\Message\RequestMethodInterface;
use Francerz\Http\HttpFactory;
use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

use function Francerz\Http\Utils\siteUrl;

class WebappHandle
{
    private static function createRequest($uri, $get, $post, $files, $method)
    {
        $http = HttpFactory::getHelper();
        $request = $http->getCurrentRequest();
        if (isset($uri)) {
            if (is_string($uri)) {
                $factory = $http->getHttpFactoryManager();
                $uri = $factory->getUriFactory()->createUri($uri);
            }
            if (!$uri instanceof UriInterface) {
                throw new InvalidArgumentException("Uri MUST be type string or UriInterface object.");
            }
            $uri = $uri
                ->withQuery($request->getUri()->getQuery())
                ->withFragment($request->getUri()->getFragment());
            $request = $request->withUri($uri);
        }
        if (isset($get)) {
            $request = $request->withQueryParams($get);
        }
        if (isset($post)) {
            $request = $request->withParsedBody($post);
        }
        if (isset($files)) {
            $request = $request->withUploadedFiles($files);
        }
        if (isset($method)) {
            $request = $request->withMethod($method);
        }
        return $request;
    }

    /**
     * @param ApplicationInterface $app
     * @param UriInterface|string $uri
     * @param array|null $get
     * @param mixed $post
     * @param array|null $files
     * @param string $method
     * @return void
     */
    public static function handle(
        ApplicationInterface $app,
        $uri = null,
        ?array $get = null,
        $post = null,
        ?array $files = null,
        ?string $method = null
    ) {
        if (strpos((string)$uri, siteUrl()) !== 0) {
            $uri = siteUrl((string)$uri, [], false);
        }
        $method = $method ?? $_SERVER['REQUEST_METHOD'] ?? RequestMethodInterface::METHOD_GET;
        $request = static::createRequest($uri, $get, $post, $files, $method);
        return $app->run($request);
    }

    public static function get(
        ApplicationInterface $app,
        $uri = null,
        ?array $get = null,
        $post = null,
        ?array $files = null
    ) {
        static::handle($app, $uri, $get, $post, $files, RequestMethodInterface::METHOD_GET);
    }

    public static function post(
        ApplicationInterface $app,
        $uri = null,
        ?array $get = null,
        $post = null,
        ?array $files = null
    ) {
        static::handle($app, $uri, $get, $post, $files, RequestMethodInterface::METHOD_POST);
    }
}
