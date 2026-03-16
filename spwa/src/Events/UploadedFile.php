<?php

namespace Spwa\Events;

class UploadedFile
{
    public function __construct(
        public readonly string $name,
        public readonly int    $size,
        public readonly string $type,
        public readonly string $tmpName,
        public readonly int    $error,
    ) {}

    /**
     * Read the uploaded file contents.
     */
    public function contents(): string
    {
        return file_get_contents($this->tmpName);
    }

    /**
     * Move the uploaded file to a permanent location.
     */
    public function moveTo(string $destination): bool
    {
        return move_uploaded_file($this->tmpName, $destination);
    }
}
