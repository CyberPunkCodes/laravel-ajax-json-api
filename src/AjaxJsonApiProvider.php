<?php

namespace CyberPunkCodes\LaravelAjaxJsonApi;

use CyberPunkCodes\LaravelAjaxJsonApi\Http\Middleware\Api\AjaxOnly;
use CyberPunkCodes\LaravelAjaxJsonApi\Http\Middleware\Api\ForceJson;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class AjaxJsonApiProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Kernel $kernel, Router $router)
    {
        $kernel->prependMiddlewareToGroup('api', ForceJson::class);
        $kernel->prependMiddlewareToGroup('api', AjaxOnly::class);

        $kernel->prependToMiddlewarePriority(ForceJson::class);
        $kernel->prependToMiddlewarePriority(AjaxOnly::class);

        $router->aliasMiddleware('force-json', ForceJson::class);
        $router->aliasMiddleware('ajax-only', AjaxOnly::class);
    }
}
