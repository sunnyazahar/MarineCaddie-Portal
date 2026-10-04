<?php

namespace App\Http\Controllers\Concerns;

use App\Services\Installer\DatabaseCredentials;
use App\Services\Installer\DatabaseProvisioner;
use App\Services\Installer\InstallerException;
use App\Services\Installer\InstallerValidation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** "Test connection" button of the install and master setup wizards. */
trait TestsInstallDatabase
{
    protected function databaseTestResponse(Request $request, DatabaseProvisioner $database): JsonResponse
    {
        $validated = $request->validate(InstallerValidation::databaseRules());

        try {
            $result = $database->prepare(DatabaseCredentials::fromInput($validated));
        } catch (InstallerException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'ok' => true,
            'created' => $result['created'],
            'message' => $result['created']
                ? 'Connected. Database "' . $validated['db_database'] . '" was created and is ready.'
                : 'Connected. Database "' . $validated['db_database'] . '" is empty and ready.',
        ]);
    }
}
