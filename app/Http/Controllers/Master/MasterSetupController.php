<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Concerns\TestsInstallDatabase;
use App\Http\Controllers\Controller;
use App\Services\Installer\DatabaseProvisioner;
use App\Services\Installer\InstallerException;
use App\Services\Installer\InstallerValidation;
use App\Services\Master\MasterSettings;
use App\Services\Master\SiteProvisioner;
use App\Services\Master\SiteRegistry;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class MasterSetupController extends Controller
{
    use TestsInstallDatabase;

    private const SENSITIVE_INPUT = ['db_password', 'admin_password', 'admin_password_confirmation', 'company_logo'];

    public function __construct(
        private MasterSettings $settings,
        private DatabaseProvisioner $database,
        private SiteProvisioner $provisioner,
        private SiteRegistry $registry,
    ) {
    }

    public function show(Request $request): View
    {
        $checks = $this->settings->checks($request);

        return view('install.wizard', [
            'checks' => $checks,
            'requirementsPass' => collect($checks)->every(fn (array $check): bool => $check['ok']),
            'suggestedAppUrl' => '',
            'master' => [
                'baseUrl' => $this->settings->targetBaseUrl($request),
                'targetRoot' => $this->settings->targetRoot(),
                'sites' => $this->registry->all(),
            ],
        ]);
    }

    public function testDatabase(Request $request): JsonResponse
    {
        return $this->databaseTestResponse($request, $this->database);
    }

    public function provision(Request $request): Response|RedirectResponse
    {
        $rules = InstallerValidation::rules(confirmPassword: true);
        unset($rules['app_url']);
        $rules['folder'] = ['required', 'string', 'max:40', 'regex:' . MasterSettings::FOLDER_PATTERN, $this->notReservedRule()];

        $validated = $request->validate($rules, [
            'folder.regex' => 'Use 2–40 lower-case letters, numbers or dashes (no dash at the start or end).',
        ]);

        try {
            $appUrl = $this->settings->appUrl($request, $validated['folder']);
            $site = $this->provisioner->provision(
                $validated,
                $appUrl,
                $this->settings->urlPrefix($request, $validated['folder']),
                $request->file('company_logo'),
            );
        } catch (InstallerException $e) {
            return $this->backWithError($e->getMessage());
        } catch (Throwable $e) {
            Log::error('Master setup failed', ['exception' => $e]);

            return $this->backWithError('Setup failed unexpectedly. Check storage/logs/laravel.log for details.');
        }

        // Carries the one-time handoff token: never cache this page.
        return response()
            ->view('master.handoff', [
                'action' => $site['app_url'] . '/install/handoff',
                'token' => $site['token'],
                'appUrl' => $site['app_url'],
                'path' => $site['path'],
                'company' => trim($validated['company_name']),
            ])
            ->header('Cache-Control', 'no-store, max-age=0');
    }

    private function notReservedRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (is_string($value) && $this->settings->isReserved($value)) {
                $fail('This folder name is reserved. Choose another one.');
            }
        };
    }

    private function backWithError(string $message): RedirectResponse
    {
        return back()
            ->withInput(request()->except(self::SENSITIVE_INPUT))
            ->withErrors(['install' => $message]);
    }
}
