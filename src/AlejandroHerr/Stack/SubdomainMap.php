<?php

namespace Alejandroherr\Stack;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;

class SubdomainMap implements HttpKernelInterface
{
    protected $map = array();
    protected $app;

    public function __construct(HttpKernelInterface $app, array $map = array())
    {
        $this->app = $app;
        if ($map) {
            $this->setMap($map);
        }
    }

    public function setMap(array $map)
    {
        $lengths = array_map('strlen', array_keys($map));
        array_multisort($lengths, SORT_DESC, $map);

        $this->map = $map;
    }

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $uri = rawurldecode($request->getUri());
        $subDomain = explode('.',explode('/', $uri)[2])[0];
        foreach ($this->map as $path => $app) {
            if (0 === strpos($subDomain, $path)) {
                $newRequest = $request->duplicate();

                return $app->handle($newRequest, $type, $catch);
            }
        }
        if (array_key_exists('*', $this->map)) {
            $app=$this->map['*'];
            $newRequest = $request->duplicate();

            return $app->handle($newRequest, $type, $catch);
        }

        return $this->app->handle($request, $type, $catch);
    }
}
