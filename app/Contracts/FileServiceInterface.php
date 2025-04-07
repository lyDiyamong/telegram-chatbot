<?php

namespace App\Contracts;

interface FileServiceInterface
{
    public function uploadFromUrl(string $url, string $directory = '', ?string $filename = null): string;
    public function delete(string $path): bool;
}
