<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class PublicMediaController extends Controller
{
    public function show(string $path): Response
    {
        $path = trim(str_replace('\\', '/', $path), '/');

        abort_if($path === '', 404);
        abort_if(str_contains($path, '..'), 404);

        $roots = array_filter([
            storage_path('app/public'),
            public_path('storage'),
        ]);

        foreach ($roots as $root) {
            $rootPath = realpath($root);

            if ($rootPath === false) {
                continue;
            }

            $candidate = realpath($rootPath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path));

            if ($candidate === false || !is_file($candidate)) {
                continue;
            }

            if (!str_starts_with($candidate, $rootPath . DIRECTORY_SEPARATOR) && $candidate !== $rootPath) {
                continue;
            }

            return response()->file($candidate, [
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        abort(404);
    }
}
