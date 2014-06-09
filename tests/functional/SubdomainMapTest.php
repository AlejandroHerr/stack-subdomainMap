<?php

namespace functional;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AlejandroHerr\Stack\SubdomainMap;

class SubdomainMapTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $app = new Application();
        $app->get('/',function () use ($app) {
            return 'I am the main app';
        });

        $appOne = new Application();
        $appOne->get('/',function () use ($appOne) {
            return 'I am appOne';
        });

        $appTwo = new Application();
        $appTwo->get('/',function () use ($appTwo) {
            return 'I am appTwo';
        });

        $map = array(
            'one' => $appOne,
            'two' => $appTwo
        );

        $app = new SubdomainMap($app,$map);

        $request = Request::create('http://localhost/');
        $response = $app->handle($request);

        $this->assertEquals('I am the main app', $response->getContent());
    }

    public function testOne()
    {
        $app = new Application();
        $app->get('/',function () use ($app) {
            return 'I am the main app';
        });

        $appOne = new Application();
        $appOne->get('/',function () use ($appOne) {
            return 'I am appOne';
        });

        $appTwo = new Application();
        $appTwo->get('/',function () use ($appTwo) {
            return 'I am appTwo';
        });

        $map = array(
            'one' => $appOne,
            'two' => $appTwo
        );

        $app = new SubdomainMap($app,$map);

        $request = Request::create('http://one.localhost/');
        $response = $app->handle($request);

        $this->assertEquals('I am appOne', $response->getContent());
    }

    public function testTwo()
    {
        $app = new Application();
        $app->get('/',function () use ($app) {
            return 'I am the main app';
        });

        $appOne = new Application();
        $appOne->get('/',function () use ($appOne) {
            return 'I am appOne';
        });

        $appTwo = new Application();
        $appTwo->get('/',function () use ($appTwo) {
            return 'I am appTwo';
        });

        $map = array(
            'one' => $appOne,
            'two' => $appTwo
        );

        $app = new SubdomainMap($app,$map);

        $request = Request::create('http://two.localhost/');
        $response = $app->handle($request);

        $this->assertEquals('I am appTwo', $response->getContent());
    }
    public function testRegex()
    {
        $app = new Application();
        $app->get('/',function () use ($app) {
            return 'I am the main app';
        });

        $appOne = new Application();
        $appOne->get('/',function () use ($appOne) {
            return 'I am appOne';
        });

        $appTwo = new Application();
        $appTwo->get('/',function () use ($appTwo) {
            return 'I am using a regex';
        });

        $map = array(
            'one' => $appOne,
            '/sub\w*/' => $appTwo
        );

        $app = new SubdomainMap($app,$map);

        $request = Request::create('http://subdomain.localhost/');
        $response = $app->handle($request);

        $this->assertEquals('I am using a regex', $response->getContent());
    }
}
