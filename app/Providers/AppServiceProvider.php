<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\AgentRepositoryInterface::class,
            \App\Repositories\AgentRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\SupplierRepositoryInterface::class,
            \App\Repositories\SupplierRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\OtherCompanyRepositoryInterface::class,
            \App\Repositories\OtherCompanyRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\VesselRepositoryInterface::class,
            \App\Repositories\VesselRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\HubRepositoryInterface::class,
            \App\Repositories\HubRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\CustomerRepositoryInterface::class,
            \App\Repositories\CustomerRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\OfficeRepositoryInterface::class,
            \App\Repositories\OfficeRepository::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $tmp = storage_path('framework/tmp');
        if (! is_dir($tmp)) {
            @mkdir($tmp, 0777, true);
        }
        if (is_dir($tmp) && is_writable($tmp)) {
            putenv('TMPDIR=' . $tmp);
            putenv('TMP=' . $tmp);
            putenv('TEMP=' . $tmp);
        }

        $appUrl = rtrim((string) config('app.url'), '/');
        if ($appUrl !== '') {
            URL::forceRootUrl($appUrl);
        }

        /** @var Request $request */
        $request = request();
        $forwardedProto = strtolower((string) $request->header('X-Forwarded-Proto', ''));
        $appUrlScheme = parse_url($appUrl, PHP_URL_SCHEME);
        $shouldForceHttps = in_array($forwardedProto, ['https', 'wss'], true)
            || $request->isSecure()
            || $appUrlScheme === 'https';

        if ($shouldForceHttps) {
            URL::forceScheme('https');
        }

        // Static theme assets live in public/files. On hosts where the
        // document root is the project folder, those URLs need /public.
        $assetUrl = config('app.asset_url') ?: env('ASSET_URL') ?: $appUrl;
        $assetUrl = rtrim((string) $assetUrl, '/');

        if ($assetUrl !== '' && ! str_ends_with(strtolower($assetUrl), '/public')) {
            $assetUrl .= '/public';
        }

        config(['app.asset_url' => $assetUrl]);
        URL::useAssetOrigin($assetUrl);

        View::composer('*', function ($view) {
            $view->with('canWriteAdministration', auth()->user()?->canWriteAdministration() ?? true);
        });
    }
}
