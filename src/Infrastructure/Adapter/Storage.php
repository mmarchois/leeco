<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter;

use App\Application\StorageInterface;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class Storage implements StorageInterface
{
    public function __construct(
        private readonly FilesystemOperator $storage,
    ) {
    }

    public function write(string $folder, string $fileName, UploadedFile $file): string
    {
        $path = sprintf('%s/%s.%s', $folder, $fileName, $file->getClientOriginalExtension());
        $this->storage->write($path, $file->getContent(), ['visibility' => 'public']);

        return $path;
    }

    public function delete(string $path): void
    {
        if (!$this->storage->has($path)) {
            return;
        }

        $this->storage->delete($path);
    }
}
