<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BackupDownloadController extends Controller
{
    /**
     * Stream (or signed-redirect) a backup archive. Hardened against
     * arbitrary-file access:
     *  - super_admin only,
     *  - disk must be one of the configured backup disks (no arbitrary disk),
     *  - file is reduced to a basename and must match the backup prefix + .zip
     *    (no path traversal),
     *  - the file must actually exist on that disk.
     * The route itself is also `signed` + `auth`, so params can't be tampered.
     */
    public function download(Request $request)
    {
        abort_unless(auth()->check() && auth()->user()->hasRole('super_admin'), 403);

        $disk = (string) $request->query('disk', '');
        $file = basename((string) $request->query('file', '')); // strip any path

        $allowedDisks = config('backup.backup.destination.disks', ['local']);
        abort_unless(in_array($disk, $allowedDisks, true), 404);

        abort_unless((bool) preg_match('/^[A-Za-z0-9._-]+\.zip$/', $file), 404);

        $path = config('backup.backup.name') . '/' . $file;
        $storage = Storage::disk($disk);
        abort_unless($storage->exists($path), 404);

        // S3 (and compatibles) → short-lived signed URL; local → stream.
        if (config("filesystems.disks.{$disk}.driver") === 's3') {
            return redirect($storage->temporaryUrl($path, now()->addMinutes(5)));
        }

        return $storage->download($path);
    }
}
