<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\TestsInstallDatabase;
use App\Services\Installer\DatabaseCredentials;
use App\Services\Installer\DatabaseProvisioner;
use App\Services\Installer\InstallHandoff;
use App\Services\Installer\InstallerException;
use App\Services\Installer\InstallerService;
use App\Services\Installer\InstallerValidation;
use App\Services\Installer\RequirementsChecker;
use App\Support\Installation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class InstallerController extends Controller
{
    use TestsInstallDatabase;

    private const SENSITIVE_INPUT = ['db_password', 'admin_password', 'admin_password_confirmation', 'company_logo'];

    public function __construct(
        private RequirementsChecker $requirements,
        private DatabaseProvisioner $database,
        private InstallerService $installer,
    ) {
    }

    public function show(Request $request): View
    {
        $checks = $this->requirements->check();

        return view('install.wizard', [
            'checks' => $checks,
            'requirementsPass' => collect($checks)->every(fn (array $check): bool => $check['ok']),
            'suggestedAppUrl' => rtrim(Installation::detectAppUrl($request), '/'),
        ]);
    }

    public function testDatabase(Request $request): JsonResponse
    {
        return $this->databaseTestResponse($request, $this->database);
    }

    public function install(Request $request): RedirectResponse
    {
        $validated = $request->validate(InstallerValidation::rules(confirmPassword: true));

        try {
            $this->installer->install(
                DatabaseCredentials::fromInput($validated),
                trim($validated['company_name']),
                [
                    'name' => trim($validated['admin_name']),
                    'email' => $validated['admin_email'],
                    'password' => $validated['admin_password'],
                ],
                $validated['app_url'],
                $request->file('company_logo'),
            );
        } catch (InstallerException $e) {
            return $this->backWithError($e->getMessage());
        } catch (Throwable $e) {
            Log::error('Installer failed', ['exception' => $e]);

            return $this->backWithError('Installation failed unexpectedly. Check storage/logs/laravel.log for details.');
        }

        // Same host as the wizard; the session store switches to the database after install, so use a query flag.
        return redirect()->to(url('/login') . '?installed=1');
    }

    /** Install with the data the master setup left in this new folder (see InstallHandoff). */
    public function handoff(Request $request, InstallHandoff $handoff): RedirectResponse
    {
        try {
            $data = $handoff->read((string) $request->input('token', ''));

            $this->installer->install(
                DatabaseCredentials::fromInput($data['db']),
                $data['company_name'],
                $data['admin'],
                $data['app_url'],
                $handoff->logoFile($data),
            );
        } catch (InstallerException $e) {
            return redirect()->to(url('/install'))->withErrors(['handoff' => $e->getMessage()]);
        } catch (Throwable $e) {
            Log::error('Installer handoff failed', ['exception' => $e]);

            return redirect()->to(url('/install'))
                ->withErrors(['handoff' => 'Installation failed unexpectedly. Check storage/logs/laravel.log for details.']);
        }

        $handoff->forget();

        return redirect()->to(url('/login') . '?installed=1');
    }

    private function backWithError(string $message): RedirectResponse
    {
        return back()
            ->withInput(request()->except(self::SENSITIVE_INPUT))
            ->withErrors(['install' => $message]);
    }
}
