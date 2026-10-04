<?php

namespace App\Console\Commands;

use App\Services\Installer\EnvironmentWriter;
use App\Services\Installer\InstallerException;
use App\Support\Installation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class MasterPasswordCommand extends Command
{
    protected $signature = 'master:password';

    protected $description = 'Set the password of the master setup (only in a copy with APP_MODE=master)';

    public function handle(EnvironmentWriter $environment): int
    {
        if (! Installation::isMaster()) {
            $this->error('This is not a master setup copy. Set APP_MODE=master in its .env first.');

            return self::FAILURE;
        }

        $input = [
            'master_password' => (string) $this->secret('New master password (min 12, upper + lower case + number)'),
            'master_password_confirmation' => (string) $this->secret('Confirm master password'),
        ];

        $validator = Validator::make($input, [
            'master_password' => ['required', 'string', 'max:255', 'confirmed', Password::min(12)->mixedCase()->numbers()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        try {
            $environment->set(['MASTER_PASSWORD_HASH' => Hash::make($input['master_password'])]);
        } catch (InstallerException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->callSilently('config:clear');
        $this->info('Master password saved. Log in at <your master URL>/master.');

        return self::SUCCESS;
    }
}
