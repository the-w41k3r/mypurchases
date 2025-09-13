<?php

namespace Azuriom\Plugin\MyPurchases\Providers;

use Azuriom\Extensions\Plugin\BasePluginServiceProvider;

class MyPurchasesServiceProvider extends BasePluginServiceProvider
{
    /**
     * The plugin's global HTTP middleware stack.
     */
    protected array $middleware = [
        // \Azuriom\Plugin\MyPurchases\Middleware\ExampleMiddleware::class,
    ];

    /**
     * The plugin's route middleware groups.
     */
    protected array $middlewareGroups = [];

    /**
     * The plugin's route middleware.
     */
    protected array $routeMiddleware = [
        // 'example' => \Azuriom\Plugin\MyPurchases\Middleware\ExampleRouteMiddleware::class,
    ];

    /**
     * The policy mappings for this plugin.
     *
     * @var array<string, string>
     */
    protected array $policies = [
        // User::class => UserPolicy::class,
    ];

    /**
     * Register any plugin services.
     */
    public function register(): void
    {
        // $this->registerMiddleware();

        //
    }

    /**
     * Bootstrap any plugin services.
     */
    public function boot(): void
    {
        // $this->registerPolicies();

        $this->loadViews();

        $this->loadTranslations();

        $this->loadMigrations();

        $this->registerRouteDescriptions();

        $this->registerAdminNavigation();

        $this->registerUserNavigation();

        //
    }

    /**
     * Returns the routes that should be able to be added to the navbar.
     *
     * @return array<string, string>
     */
    protected function routeDescriptions(): array
    {
        return [
            'mypurchases.index' => trans('mypurchases::messages.title')
        ];
    }


    /**
     * Return the admin navigations routes to register in the dashboard.
     *
     * @return array<string, array<string, string>>
     */
    protected function adminNavigation(): array
    {
        return [
            'mypurchases' => [
                'name' => trans('mypurchases::messages.title'), // Text shown in sidebar
                'type' => 'single',   // Single button, not dropdown
                'icon' => 'bi bi-bag-heart-fill', // Bootstrap icon
                'route' => 'mypurchases.admin.index', // Route name
            ],
        ];
    }

    /**
     * Return the user navigations routes to register in the user menu.
     *
     * @return array<string, array<string, string>>
     */

    protected function userNavigation(): array
    {
        return [
            'mypurchases' => [
                'name' => 'My Purchases',
                'icon' => 'bi bi-bag-heart-fill',
                'route' => 'mypurchases.index',
            ],
        ];
    }

}
