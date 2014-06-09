<?php

namespace AlejandroHerr\Stack;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;

class SubdomainMap implements HttpKernelInterface
{
    protected $map = array();
    protected $app;
    const DEFAULT_HTTPKERNEL_PATTERN =   '/^$/';

    public function __construct(HttpKernelInterface $app, array $map = array())
    {
        $this->app = $app;
        if ($map) {
            $this->setMap($map);
        }
    }

    public function setMap(array $map)
    {
        $newMap=array();
        foreach ($map as $key => $value) {
            if (!preg_match('/^\/.*\/$/', $key)) {
                $key='/^'.$key.'$/';
            }
            $newMap[$key]=$value;
        }
        $this->map = $newMap;
    }

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $uri = rawurldecode($request->getUri());

        #Broken into lines to be compatible with php5.3
        $subDomain = explode('/', $uri);
        $subDomain = $subDomain[2];
        $subDomain = explode('.',$subDomain);
        $subDomain = $subDomain[0];

        foreach ($this->map as $pattern => $app) {
            if (preg_match($pattern, $subDomain)) {
                $newRequest = $request->duplicate();

                return $app->handle($newRequest, $type, $catch);
            }
        }

        return $this->app->handle($request, $type, $catch);
    }
}
