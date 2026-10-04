<?php

namespace Tests\Feature\Master;

use App\Services\Installer\DatabaseProvisioner;
use App\Services\Installer\EnvironmentWriter;
use App\Services\Installer\InstallerException;
use App\Services\Installer\InstallerService;
use App\Services\Installer\InstallHandoff;
use App\Services\Master\ProjectCopier;
use App\Services\Master\SiteProvisioner;
use App\Services\Master\SiteRegistry;
use App\Support\Installation;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Mockery;
use Tests\TestCase;

class MasterSetupTest extends TestCase
{
    private const PASSWORD = 'Master-Secret-123';

    private string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();

        // Pre-install requests use the file cache, so throttle hits would carry over between runs.
        $this->withoutMiddleware(ThrottleRequests::class);

        $this->tempDir = sys_get_temp_dir() . '/master-test-' . bin2hex(random_bytes(6));
        mkdir($this->tempDir . '/sites', 0700, true);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->deleteDirectory($this->tempDir);

        parent::tearDown();
    }

    public function test_master_routes_do_not_exist_in_a_normal_portal(): void
    {
        $this->assertFalse(Installation::isMaster());
        $this->assertFalse(Route::has('master.show'));

        $this->get('/master')->assertNotFound();
        $this->post('/master/provision')->assertNotFound();
    }

    public function test_master_mode_serves_only_master_pages_and_requires_login(): void
    {
        $this->enableMasterMode();

        $this->get('/login')->assertRedirect('/master');
        $this->get('/install')->assertRedirect('/master');
        $this->get('/master')->assertRedirect('/master/login');
        $this->get('/master/login')->assertOk()->assertSee('Master password');
    }

    public function test_master_login_rejects_wrong_password_and_accepts_correct_one(): void
    {
        $this->enableMasterMode();

        $this->from('/master/login')->post('/master/login', ['master_password' => 'wrong-password'])
            ->assertRedirect('/master/login')
            ->assertSessionHasErrors('master_password');
        $this->assertNull(session('master.authenticated_at'));

        $this->post('/master/login', ['master_password' => self::PASSWORD])->assertRedirect('/master');

        $this->get('/master')
            ->assertOk()
            ->assertSee('Set up a new company')
            ->assertSee('name="folder"', false)
            ->assertDontSee('name="app_url"', false);
    }

    public function test_master_login_is_disabled_without_a_password_hash(): void
    {
        $this->enableMasterMode();
        config(['master.password_hash' => '']);

        $this->get('/master/login')->assertOk()->assertSee('php artisan master:password');
        $this->post('/master/login', ['master_password' => self::PASSWORD])->assertSessionHasErrors('master_password');
    }

    public function test_provision_rejects_invalid_and_reserved_folder_names(): void
    {
        $this->enableMasterMode();
        $this->post('/master/login', ['master_password' => self::PASSWORD])->assertRedirect('/master');

        foreach (['Bad_Folder', '-acme', 'a', 'setup', 'laravel'] as $folder) {
            $this->from('/master')->post('/master/provision', $this->validInput(['folder' => $folder]))
                ->assertRedirect('/master')
                ->assertSessionHasErrors('folder');
        }
    }

    public function test_provisioner_copies_only_the_app_patches_urls_and_leaves_an_encrypted_handoff(): void
    {
        $source = $this->makeSourceProject();
        $this->enableMasterMode($source);

        $result = $this->provisioner()->provision(
            $this->validInput(['folder' => 'acme']),
            'http://localhost/sites/acme',
            '/sites/acme',
            UploadedFile::fake()->image('logo.png', 40, 40),
        );

        $target = $this->tempDir . '/sites/acme';
        $this->assertSame($target, $result['path']);
        $this->assertDirectoryExists($target . '/vendor');
        $this->assertFileExists($target . '/public/files/app.css');

        // Allow-listed copy: no secrets, history, tests, logs, caches or runtime links.
        $this->assertStringNotContainsString('SOURCE_SECRET', (string) file_get_contents($target . '/.env'));
        $this->assertDirectoryDoesNotExist($target . '/tests');
        $this->assertFileDoesNotExist($target . '/storage/logs/old.log');
        $this->assertFileDoesNotExist($target . '/bootstrap/cache/services.php');
        $this->assertFileDoesNotExist($target . '/public/storage');
        foreach (ProjectCopier::RUNTIME_DIRECTORIES as $directory) {
            $this->assertDirectoryExists($target . '/' . $directory);
        }

        $env = new EnvironmentWriter($target . '/.env');
        $this->assertStringStartsWith('base64:', (string) $env->get('APP_KEY'));
        $this->assertSame('http://localhost/sites/acme', $env->get('APP_URL'));
        $this->assertSame('Acme Shipping', $env->get('APP_NAME'));
        $this->assertSame('app', $env->get('APP_MODE'));
        $this->assertSame('false', $env->get('APP_DEBUG'));

        $this->assertStringContainsString("preg_replace('#^/sites/acme#'", (string) file_get_contents($target . '/index.php'));
        $this->assertStringContainsString("preg_replace('#^/sites/acme#'", (string) file_get_contents($target . '/public/index.php'));

        $data = app(InstallHandoff::class)->read($result['token'], $target);
        $this->assertSame('Acme Shipping', $data['company_name']);
        $this->assertSame('acme_db', $data['db']['db_database']);
        $this->assertSame('admin@acme.test', $data['admin']['email']);
        $this->assertSame('png', $data['logo']['extension']);
        $this->assertFileExists($target . '/storage/app/install-handoff-logo.png');

        $this->assertSame('acme', app(SiteRegistry::class)->all()[0]['folder']);
        $this->assertSame([], glob($this->tempDir . '/sites/.acme.partial-*') ?: []);
    }

    public function test_provisioner_refuses_an_existing_folder_without_touching_it(): void
    {
        $source = $this->makeSourceProject();
        $this->enableMasterMode($source);
        mkdir($this->tempDir . '/sites/acme');
        file_put_contents($this->tempDir . '/sites/acme/keep.txt', 'existing');

        try {
            $this->provisioner()->provision($this->validInput(['folder' => 'acme']), 'http://localhost/sites/acme', '/sites/acme');
            $this->fail('Expected an existing folder to be rejected.');
        } catch (InstallerException $e) {
            $this->assertStringContainsString('already exists', $e->getMessage());
        }

        $this->assertSame('existing', file_get_contents($this->tempDir . '/sites/acme/keep.txt'));
    }

    public function test_failed_copy_removes_only_its_staging_folder(): void
    {
        $source = $this->makeSourceProject();
        unlink($source . '/.htaccess');
        $this->enableMasterMode($source);

        try {
            $this->provisioner()->provision($this->validInput(['folder' => 'acme']), 'http://localhost/sites/acme', '/sites/acme');
            $this->fail('Expected an incomplete source to fail.');
        } catch (InstallerException $e) {
            $this->assertStringContainsString('.htaccess', $e->getMessage());
        }

        $this->assertDirectoryDoesNotExist($this->tempDir . '/sites/acme');
        $this->assertSame([], glob($this->tempDir . '/sites/.acme.partial-*') ?: []);
    }

    public function test_front_controller_patch_works_on_the_real_project_files(): void
    {
        $copy = $this->tempDir . '/patch';
        mkdir($copy . '/public', 0700, true);
        copy(base_path('index.php'), $copy . '/index.php');
        copy(base_path('public/index.php'), $copy . '/public/index.php');
        copy(base_path('.htaccess'), $copy . '/.htaccess');

        $this->provisioner()->patchFrontControllers($copy, '/clients/acme');

        $this->assertStringContainsString("preg_replace('#^/clients/acme#'", (string) file_get_contents($copy . '/index.php'));
        $this->assertStringContainsString("preg_replace('#^/clients/acme#'", (string) file_get_contents($copy . '/public/index.php'));

        $htaccess = (string) file_get_contents($copy . '/.htaccess');
        $this->assertSame(2, substr_count($htaccess, '%{DOCUMENT_ROOT}/clients/acme/public/$1'));
        $this->assertStringContainsString('!^/clients/acme/public/', $htaccess);
        $this->assertStringNotContainsString('/laravel/', $htaccess);
    }

    public function test_handoff_rejects_wrong_token_tampering_and_expiry(): void
    {
        $project = $this->tempDir . '/project';
        mkdir($project . '/storage/app', 0700, true);
        $handoff = app(InstallHandoff::class);

        $token = $handoff->create($project, [
            'company_name' => 'Acme',
            'app_url' => 'http://localhost/acme',
            'db' => ['db_host' => '127.0.0.1', 'db_port' => 3306, 'db_database' => 'acme', 'db_username' => 'root', 'db_password' => 'p'],
            'admin' => ['name' => 'A', 'email' => 'a@acme.test', 'password' => 'Secret123'],
        ]);

        $raw = (string) file_get_contents($project . '/' . InstallHandoff::FILE);
        $this->assertStringNotContainsString('Secret123', $raw);
        $this->assertStringNotContainsString('a@acme.test', $raw);
        $this->assertSame('Acme', $handoff->read($token, $project)['company_name']);

        foreach ([str_repeat('a', 64), 'not-a-token', ''] as $badToken) {
            try {
                $handoff->read($badToken, $project);
                $this->fail('Expected an invalid token to be rejected.');
            } catch (InstallerException) {
                $this->addToAssertionCount(1);
            }
        }

        $this->travel(InstallHandoff::TTL_MINUTES + 1)->minutes();
        $this->expectException(InstallerException::class);
        $handoff->read($token, $project);
    }

    public function test_handoff_endpoint_installs_and_forgets_the_handoff(): void
    {
        $this->markNotInstalled();
        $data = [
            'company_name' => 'Acme Shipping',
            'app_url' => 'http://localhost/acme',
            'db' => ['db_host' => '127.0.0.1', 'db_port' => 3306, 'db_database' => 'acme_db', 'db_username' => 'root', 'db_password' => ''],
            'admin' => ['name' => 'Acme Admin', 'email' => 'admin@acme.test', 'password' => 'Secret123'],
            'logo' => null,
        ];

        $handoff = Mockery::mock(InstallHandoff::class);
        $handoff->shouldReceive('read')->once()->with(str_repeat('b', 64))->andReturn($data);
        $handoff->shouldReceive('logoFile')->once()->andReturn(null);
        $handoff->shouldReceive('forget')->once();
        $this->app->instance(InstallHandoff::class, $handoff);

        $installer = Mockery::mock(InstallerService::class);
        $installer->shouldReceive('install')->once()->withArgs(
            fn ($credentials, $company, $admin, $appUrl, $logo): bool => $credentials->database === 'acme_db'
                && $company === 'Acme Shipping'
                && $admin['email'] === 'admin@acme.test'
                && $appUrl === 'http://localhost/acme'
                && $logo === null
        );
        $this->app->instance(InstallerService::class, $installer);

        $this->post('/install/handoff', ['token' => str_repeat('b', 64)])
            ->assertRedirect('/login?installed=1');
    }

    public function test_handoff_endpoint_falls_back_to_the_wizard_on_a_bad_token(): void
    {
        $this->markNotInstalled();

        $this->post('/install/handoff', ['token' => 'nope'])
            ->assertRedirect('/install')
            ->assertSessionHasErrors('handoff');
    }

    public function test_handoff_endpoint_is_hidden_once_installed(): void
    {
        $this->assertTrue(Installation::isInstalled());

        $this->post('/install/handoff', ['token' => str_repeat('b', 64)])->assertNotFound();
    }

    public function test_master_password_command_refuses_outside_master_mode(): void
    {
        $this->artisan('master:password')->assertFailed();
    }

    private function enableMasterMode(?string $source = null): void
    {
        $this->assertNotSame('', trim((string) config('app.key')));

        config([
            'app.mode' => 'master',
            'app.install_lock' => $this->tempDir . '/installed.lock',
            'master.password_hash' => Hash::make(self::PASSWORD),
            'master.source_path' => $source ?? base_path(),
            'master.target_root' => $this->tempDir . '/sites',
            'master.target_url' => 'http://localhost/sites',
        ]);

        $this->app->instance(SiteRegistry::class, new SiteRegistry($this->tempDir . '/sites.json'));

        // Routes were registered at boot in normal mode; add the master group like a master copy would.
        Route::middleware('web')->group(base_path('routes/master.php'));
        $routes = app('router')->getRoutes();
        $routes->refreshNameLookups();
        $routes->refreshActionLookups();
    }

    private function markNotInstalled(): void
    {
        $this->assertNotSame('', trim((string) config('app.key')));
        config(['app.install_lock' => $this->tempDir . '/installed.lock']);
    }

    private function provisioner(): SiteProvisioner
    {
        $database = Mockery::mock(DatabaseProvisioner::class);
        $database->shouldReceive('prepare')->andReturn(['created' => false]);
        $this->app->instance(DatabaseProvisioner::class, $database);
        $this->app->instance(SiteRegistry::class, new SiteRegistry($this->tempDir . '/sites.json'));

        return app(SiteProvisioner::class);
    }

    /** Minimal project tree with the required entries plus things that must never be copied. */
    private function makeSourceProject(): string
    {
        $source = $this->tempDir . '/source';
        $files = [
            'app/Models/User.php' => '<?php',
            'bootstrap/app.php' => '<?php',
            'bootstrap/cache/services.php' => '<?php return [];',
            'config/app.php' => '<?php return [];',
            'database/migrations/.gitkeep' => '',
            'resources/views/welcome.blade.php' => 'hi',
            'routes/web.php' => '<?php',
            'vendor/autoload.php' => '<?php',
            'public/files/app.css' => 'body{}',
            'public/index.php' => (string) file_get_contents(base_path('public/index.php')),
            'index.php' => (string) file_get_contents(base_path('index.php')),
            '.htaccess' => (string) file_get_contents(base_path('.htaccess')),
            'artisan' => '#!/usr/bin/env php',
            '.env.example' => "APP_NAME=Laravel\nAPP_KEY=\nAPP_URL=http://localhost\n",
            '.env' => "APP_KEY=SOURCE_SECRET\nDB_PASSWORD=SOURCE_SECRET\n",
            'tests/ExampleTest.php' => '<?php',
            'storage/logs/old.log' => 'log',
        ];

        foreach ($files as $path => $contents) {
            $full = $source . '/' . $path;
            if (! is_dir(dirname($full))) {
                mkdir(dirname($full), 0700, true);
            }
            file_put_contents($full, $contents);
        }

        mkdir($source . '/storage/app/public', 0700, true);
        symlink($source . '/storage/app/public', $source . '/public/storage');

        return $source;
    }

    private function validInput(array $overrides = []): array
    {
        return $overrides + [
            'company_name' => 'Acme Shipping',
            'folder' => 'acme',
            'db_host' => '127.0.0.1',
            'db_port' => '3306',
            'db_database' => 'acme_db',
            'db_username' => 'root',
            'db_password' => '',
            'admin_name' => 'Acme Admin',
            'admin_email' => 'admin@acme.test',
            'admin_password' => 'Secret123',
            'admin_password_confirmation' => 'Secret123',
        ];
    }
}
