<?php 
namespace App\Services;

class PostFilesTypeSetService
{
    public static function setType($file)
    {
        $fileType = str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'image';
        return $fileType;
    }
}
