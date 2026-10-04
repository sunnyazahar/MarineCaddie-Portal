<?php

namespace App\Services\Installer;

use RuntimeException;

/** Installer failure with a message that is safe to show to the person installing. */
class InstallerException extends RuntimeException
{
}
