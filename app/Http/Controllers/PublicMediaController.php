<?php

namespace App\Http\Controllers;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PublicMediaController extends Controller
{
    public function show(string $path): BinaryFileResponse
    {
        $path = trim(str_replace('\\', '/', $path), '/');

        abort_if($path === '', 404);
        abort_if(str_contains($path, '..'), 404);

        $roots = array_filter([
            storage_path('app/public'),
            public_path('storage'),
        ]);

        foreach ($roots as $root) {
            $candidate = $this->resolveExactMatch($root, $path);

            if ($candidate !== null) {
                return $this->fileResponse($candidate);
            }
        }

        foreach ($roots as $root) {
            $candidate = $this->resolveByBasename($root, basename($path));

            if ($candidate !== null) {
                return $this->fileResponse($candidate);
            }
        }

        abort(404);
    }

    protected function resolveExactMatch(string $root, string $path): ?string
    {
        $rootPath = realpath($root);

        if ($rootPath === false) {
            return null;
        }

        $candidate = realpath($rootPath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path));

        if ($candidate === false || !is_file($candidate)) {
            return null;
        }

        if (!str_starts_with($candidate, $rootPath . DIRECTORY_SEPARATOR) && $candidate !== $rootPath) {
            return null;
        }

        return $candidate;
    }

    protected function resolveByBasename(string $root, string $filename): ?string
    {
        $rootPath = realpath($root);

        if ($rootPath === false || $filename === '') {
            return null;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }

            if ($file->getFilename() === $filename) {
                return $file->getRealPath() ?: null;
            }
        }

        return null;
    }

    protected function fileResponse(string $path): BinaryFileResponse
    {
        return response()->file($path, [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
