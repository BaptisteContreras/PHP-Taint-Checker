# PHP-Taint-Checker

It's a POC of a tool to try to detect tainted variables.

For the moment, this very first version try to detect when a value is fetched from the `Symfony\Component\HttpFoundation\Request` object using the `get()` method.
All subsequent assignations using this variable (or a tainted one) is also considered tainted.

In our case, a "tainted variable" is a variable controlled by the user. It's important to sanitize and validate them well, to prevent a malicious user from manipulate it to harm your application.

The analysis works in 4 steps:

- **Prepare**: Analyze the AST and add hints or useful information for the next steps
- **Mark**: Mark as "tainted" all assignation expression using a dangerous variable
- **Propagate**: All variables, functions or method calls using a tainted variable are flagged and the variable storing the result is also marked tainted
- **Classify**: Add extra information and perform extra analysis on tainted variables (checking for sanitization or validation)

Here is an example : 

```php

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

```