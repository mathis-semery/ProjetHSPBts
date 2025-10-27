<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class SimpleUpload
{
    /** @var UploadedFile[] */
    public array $attachments = [];
}
