# Laravel AJAX JSON API

Turn your Laravel application into an AJAX JSON API.


## Installation

Install using Composer:

```bash
composer require cyberpunkcodes/laravel-ajax-json-api
```

This package will automatically be injected to your `api` middleware thanks to the Laravel
ServiceProvider via:

```php
$kernel->prependMiddlewareToGroup('api', ForceJson::class);
$kernel->prependMiddlewareToGroup('api', AjaxOnly::class);

$kernel->prependToMiddlewarePriority(ForceJson::class);
$kernel->prependToMiddlewarePriority(AjaxOnly::class);
```

You do not have to do anything other than use/apply the `api` middleware. The above lines are
just to show you how the magic happens, by prepending `ForceJson` and `AjaxOnly` to the
`api` middleware group, and adjusting their priority to be first.

Other than installing with Composer, all you have to do is use the `api` middleware. This could
be in your Controller or wherever you need.

Ideally, in a sole AJAX JSON API, it would be applied globally to the entire application. This
would be done in your `app/Providers/RouteServiceProvider.php`.


Example `RouteServiceProvider.php`:

```php
<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * @var string
     */
    protected string $ApiNamespace = 'App\Http\Controllers\Api';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::prefix('v1')
                ->middleware('api')
                ->namespace($this->ApiNamespace)
                ->group(base_path('routes/api/v1.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
```

The default for a Laravel 10 application is:

```php
Route::middleware('api')
    ->prefix('api')
    ->group(base_path('routes/api.php'));
```

Which we change to:

```php
Route::prefix('v1')
    ->middleware('api')
    ->namespace($this->ApiNamespace)
    ->group(base_path('routes/api/v1.php'));
```

This package will work with the default implementation since it also uses the `api` 
middleware. However, it is recommended to use versioning so you can create a `v2`, `v3`, 
and so on as needed for future upgrades. So lets setup versioning.

You should use the example `RouteServiceProvider` as shown above. 

Then create an `api` directory in the `routes` folder. Move and rename the `/routes/api.php` file to `/routes/api/v1.php`.

`/routes/api/v1.php` should look like:

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Controller;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::get('/', function (Request $request) {
    return ['home'];
});

Route::get('/test', [Controller::class, 'test']);
```

Notice the `/` path returns an array containing `home`. This path would be: 
`https://example.com/v1/` which would return a JSON response:

```bash
[
    "home"
]
```

This is just to get you started so you can make your first request with Postman or 
whatever you use, and to confirm you are receiving JSON responses back.

You should be making an AJAX request by including the following headers:

```bash
X-Requested-With: XMLHttpRequest
Accept: application/json
```

You also need to create the API Controller. Create an `Api` directory in `app/Controllers`.
You can delete the default `app/Controllers/Controller.php` if you aren't going to handle
frontend web requests (ie: if it is solely an API).

Create a controller at `app/Controllers/Api/Controller.php`:

```php
<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function test(Request $request)
    {
        return ['foo' => 'bar'];
    }
}
```

Making an AJAX request to `https://example.com/v1/test` will return a JSON response:

```bash
{
    "foo": "bar"
}
```

The two built in routes/functions/responses are just examples to get you started.

## Final Notes

If you return a string, it will return an html response. This is just how Laravel works. You
do not have to specifically return a json response. Just return an array and it will return 
a json response for you.

I leave the `/` web route (in `routes/web.php`) and just have it return blank:

```php
Route::get('/', function () {
    return '';
});
```

This is so if the API is accessed via the web, it just returns a blank page and not an error.