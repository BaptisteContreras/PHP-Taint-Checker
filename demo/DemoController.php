<?php


namespace BaptisteContreras\TaintCheckerDemo;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DemoController extends AbstractApiController
{
    public function index(Request $request, ServiceDemo $service): Response
    {
        $a = $request->get('a'); // tainted
        $b = 1;
        $c = $a; // tainted
        $d = $service->demo($b);
        $e = $service->demo($a);
        $f = $c; // tainted
        $a = 'rrr';
        $z = $a;
        $a = $c; // tainted
        $xx = $a; // tainted

        return new Response('');
    }

    public function toto(Symfony\Component\HttpFoundation\Request $request): Response
    {
        $a = $request->get('t'); // tainted
        $b = 1;
        $c = $a; // tainted
        $a = new Toto();

        return new Response('');
    }
}