<?php

namespace Spwa\Events;

class FileEvent
{
    /**
     * @param UploadedFile[] $files
     */
    public function __construct(
        public readonly array $files = [],
    ) {}

    public static function from(mixed $data): self
    {
        if (!isset($_FILES['files'])) return new self();

        $uploaded = $_FILES['files'];
        $files = [];

        // $_FILES['files'] uses the array format: name[], type[], tmp_name[], etc.
        $count = is_array($uploaded['name']) ? count($uploaded['name']) : 1;

        for ($i = 0; $i < $count; $i++) {
            $name = is_array($uploaded['name']) ? $uploaded['name'][$i] : $uploaded['name'];
            $size = is_array($uploaded['size']) ? $uploaded['size'][$i] : $uploaded['size'];
            $type = is_array($uploaded['type']) ? $uploaded['type'][$i] : $uploaded['type'];
            $tmpName = is_array($uploaded['tmp_name']) ? $uploaded['tmp_name'][$i] : $uploaded['tmp_name'];
            $error = is_array($uploaded['error']) ? $uploaded['error'][$i] : $uploaded['error'];

            if ($error === UPLOAD_ERR_OK) {
                $files[] = new UploadedFile(
                    name: $name,
                    size: (int)$size,
                    type: $type,
                    tmpName: $tmpName,
                    error: $error,
                );
            }
        }

        return new self($files);
    }
}
