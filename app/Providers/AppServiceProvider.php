<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
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
            \App\Repositories\Contracts\CustomerAddressRepositoryInterface::class,
            \App\Repositories\CustomerAddressRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\CustomerInvoiceDetailRepositoryInterface::class,
            \App\Repositories\CustomerInvoiceDetailRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\CustomerResponsibleRepositoryInterface::class,
            \App\Repositories\CustomerResponsibleRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\CustomerSopRepositoryInterface::class,
            \App\Repositories\CustomerSopRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\CustomerNotificationSettingRepositoryInterface::class,
            \App\Repositories\CustomerNotificationSettingRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\CustomerDocumentRepositoryInterface::class,
            \App\Repositories\CustomerDocumentRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\CustomerVesselRepositoryInterface::class,
            \App\Repositories\CustomerVesselRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\CustomerGroupRepositoryInterface::class,
            \App\Repositories\CustomerGroupRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\OfficeRepositoryInterface::class,
            \App\Repositories\OfficeRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\ContactRepositoryInterface::class,
            \App\Repositories\ContactRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\AgentUserRepositoryInterface::class,
            \App\Repositories\AgentUserRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\AgentDocumentRepositoryInterface::class,
            \App\Repositories\AgentDocumentRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\HubDocumentRepositoryInterface::class,
            \App\Repositories\HubDocumentRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\HubUserRepositoryInterface::class,
            \App\Repositories\HubUserRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            \App\Repositories\UserRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\OfficeBankAccountRepositoryInterface::class,
            \App\Repositories\OfficeBankAccountRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\ShipmentRepositoryInterface::class,
            \App\Repositories\ShipmentRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\ProformaInvoiceRepositoryInterface::class,
            \App\Repositories\ProformaInvoiceRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\CrrRepositoryInterface::class,
            \App\Repositories\CrrRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\UserLoginActivityRepositoryInterface::class,
            \App\Repositories\UserLoginActivityRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\ShipmentManifestRepositoryInterface::class,
            \App\Repositories\ShipmentManifestRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\ShipmentPreAlertRepositoryInterface::class,
            \App\Repositories\ShipmentPreAlertRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\CrrDocumentRepositoryInterface::class,
            \App\Repositories\CrrDocumentRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\CountryRepositoryInterface::class,
            \App\Repositories\CountryRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\PortRepositoryInterface::class,
            \App\Repositories\PortRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\PartyLookupRepositoryInterface::class,
            \App\Repositories\PartyLookupRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\UserNotificationRepositoryInterface::class,
            \App\Repositories\UserNotificationRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\OperationsDashboardRepositoryInterface::class,
            \App\Repositories\OperationsDashboardRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\AdministrationChangeLogRepositoryInterface::class,
            \App\Repositories\AdministrationChangeLogRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\ShipmentStockSnapshotRepositoryInterface::class,
            \App\Repositories\ShipmentStockSnapshotRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Contracts\ShipmentTransitStockRepositoryInterface::class,
            \App\Repositories\ShipmentTransitStockRepository::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Never allow migrate:fresh / db:wipe / schema:drop on production,
        // marinecaddie hosts, or any non-local MySQL (e.g. Hostinger).
        if ($this->shouldProhibitDestructiveDatabaseCommands()) {
            DB::prohibitDestructiveCommands();
        }

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
            $user = auth()->user();
            $view->with('canWriteAdministration', $user?->canWriteAdministration() ?? true);
            $view->with('canWriteOutsideBilling', $user?->canWriteOutsideBilling() ?? true);
        });

        \Illuminate\Pagination\Paginator::useBootstrapFive();
    }

    /**
     * Block Artisan destructive DB commands outside disposable local DBs.
     */
    private function shouldProhibitDestructiveDatabaseCommands(): bool
    {
        if ($this->app->environment('production', 'staging')) {
            return true;
        }

        $appUrl = strtolower((string) config('app.url'));
        if (str_contains($appUrl, 'marinecaddie.com')) {
            return true;
        }

        $connection = (string) config('database.default');
        $driver = (string) config("database.connections.{$connection}.driver");
        $host = strtolower((string) config("database.connections.{$connection}.host", ''));

        if (in_array($driver, ['mysql', 'mariadb'], true)
            && $host !== ''
            && ! in_array($host, ['127.0.0.1', 'localhost', '::1'], true)
        ) {
            return true;
        }

        return false;
    }
}
