<?php

namespace Tests\Feature\Install;

use App\Services\Installer\DatabaseCredentials;
use App\Services\Installer\DatabaseProvisioner;
use App\Services\Installer\EnvironmentWriter;
use App\Services\Installer\InstallerException;
use App\Support\Installation;
use Dotenv\Dotenv;
use Tests\TestCase;

class InstallerTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tempDir = sys_get_temp_dir() . '/installer-test-' . bin2hex(random_bytes(6));
        mkdir($this->tempDir, 0700, true);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->tempDir . '/{,.}*', GLOB_BRACE) ?: [] as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        @rmdir($this->tempDir);

        parent::tearDown();
    }

    private function markNotInstalled(): void
    {
        // A missing key would make the middleware write the real project .env.
        $this->assertNotSame('', trim((string) config('app.key')));
        config(['app.install_lock' => $this->tempDir . '/installed.lock']);
        $this->assertFalse(Installation::isInstalled());
    }

    public function test_pages_redirect_to_installer_until_installed(): void
    {
        $this->markNotInstalled();

        $this->get('/login')->assertRedirect('/install');
        $this->get('/dashboard')->assertRedirect('/install');
    }

    public function test_installer_wizard_renders_when_not_installed(): void
    {
        $this->markNotInstalled();

        $this->get('/install')
            ->assertOk()
            ->assertSee('Server requirements')
            ->assertSee('Test connection')
            ->assertDontSee('MarineCaddie');
    }

    public function test_installer_is_hidden_once_installed(): void
    {
        $this->assertTrue(Installation::isInstalled());

        $this->get('/install')->assertNotFound();
        $this->post('/install/test-database')->assertNotFound();
        $this->post('/install')->assertNotFound();
    }

    public function test_install_validates_input_and_does_not_flash_passwords(): void
    {
        $this->markNotInstalled();

        $response = $this->from('/install')->post('/install', [
            'company_name' => '',
            'db_host' => '127.0.0.1',
            'db_port' => '3306',
            'db_database' => 'bad-name; DROP DATABASE x',
            'db_username' => 'root',
            'db_password' => 'super-secret',
            'admin_name' => 'Admin',
            'admin_email' => 'not-an-email',
            'admin_password' => 'weak',
            'admin_password_confirmation' => 'weak',
            'app_url' => 'javascript:alert(1)',
        ]);

        $response->assertRedirect('/install');
        $response->assertSessionHasErrors(['company_name', 'db_database', 'admin_email', 'admin_password', 'app_url']);
        $this->assertNull(session()->getOldInput('db_password'));
        $this->assertNull(session()->getOldInput('admin_password'));
        $this->assertFalse(Installation::isInstalled());
    }

    public function test_install_rejects_svg_logo(): void
    {
        $this->markNotInstalled();

        $svg = \Illuminate\Http\UploadedFile::fake()->createWithContent('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');

        $this->from('/install')
            ->post('/install', ['company_name' => 'Acme', 'company_logo' => $svg])
            ->assertSessionHasErrors('company_logo');
    }

    public function test_test_database_endpoint_validates_database_name(): void
    {
        $this->markNotInstalled();

        $this->postJson('/install/test-database', [
            'db_host' => '127.0.0.1',
            'db_port' => 3306,
            'db_database' => 'x`; DROP DATABASE saf; --',
            'db_username' => 'root',
        ])->assertUnprocessable()->assertJsonValidationErrors('db_database');
    }

    public function test_provisioner_rejects_unsafe_identifiers_before_connecting(): void
    {
        $this->expectException(InstallerException::class);

        app(DatabaseProvisioner::class)->prepare(new DatabaseCredentials('127.0.0.1', 3306, 'bad name', 'root', ''));
    }

    public function test_database_name_is_suggested_from_company_name(): void
    {
        $provisioner = app(DatabaseProvisioner::class);

        $this->assertSame('acme_shipping_llc', $provisioner->suggestName('Acme Shipping, LLC'));
        $this->assertSame('portal', $provisioner->suggestName('!!!'));
        $this->assertSame(64, strlen($provisioner->suggestName(str_repeat('a', 100))));
    }

    public function test_environment_writer_round_trips_special_characters(): void
    {
        $path = $this->tempDir . '/.env';
        file_put_contents($path, "APP_NAME=Laravel\n# DB_HOST=127.0.0.1\nDB_PASSWORD=\n");

        $writer = new EnvironmentWriter($path);
        $writer->set([
            'APP_NAME' => 'Acme "Ships" & Co',
            'DB_HOST' => 'db.internal',
            'DB_PASSWORD' => 'p@ss$word\\with#hash',
            'NEW_KEY' => 'value',
        ]);

        $contents = (string) file_get_contents($path);
        $parsed = Dotenv::parse($contents);

        $this->assertStringStartsWith("APP_NAME=\"Acme \\\"Ships\\\" & Co\"\nDB_HOST=db.internal\n", $contents);
        $this->assertSame('Acme "Ships" & Co', $parsed['APP_NAME']);
        $this->assertSame('p@ss$word\\with#hash', $parsed['DB_PASSWORD']);
        $this->assertSame('value', $parsed['NEW_KEY']);
    }

    public function test_environment_writer_rejects_newline_injection(): void
    {
        $path = $this->tempDir . '/.env';
        file_put_contents($path, "APP_NAME=Laravel\n");

        $this->expectException(InstallerException::class);

        (new EnvironmentWriter($path))->set(['APP_NAME' => "Acme\nAPP_DEBUG=true"]);
    }

    public function test_environment_writer_keeps_existing_file_permissions(): void
    {
        $path = $this->tempDir . '/.env';
        file_put_contents($path, "APP_NAME=Laravel\n");
        chmod($path, 0644);

        (new EnvironmentWriter($path))->set(['APP_NAME' => 'Acme Shipping']);
        clearstatcache(true, $path);

        $this->assertSame(0644, fileperms($path) & 0777);
    }

    public function test_environment_writer_updates_writable_env_inside_read_only_folder(): void
    {
        $dir = $this->tempDir . '/locked';
        mkdir($dir);
        $path = $dir . '/.env';
        file_put_contents($path, "APP_NAME=Laravel\n");
        chmod($dir, 0555);

        try {
            if (is_writable($dir)) {
                $this->markTestSkipped('Folder permissions are not enforced for this user.');
            }

            (new EnvironmentWriter($path))->set(['APP_NAME' => 'Acme Shipping']);

            $this->assertSame('Acme Shipping', Dotenv::parse((string) file_get_contents($path))['APP_NAME']);
            $this->assertSame(['.env'], array_values(array_diff((array) scandir($dir), ['.', '..'])));
        } finally {
            chmod($dir, 0755);
            @unlink($path);
            @rmdir($dir);
        }
    }
}
