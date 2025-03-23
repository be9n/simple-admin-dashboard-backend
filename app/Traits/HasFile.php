<?php

namespace App\Traits;
use Illuminate\Http\UploadedFile;

trait HasFile
{
    public function updateFile(UploadedFile $newFile, string $collection = 'images')
    {
        $this->deleteFirstFileFromCollectionIfExisted($collection);
        $this->storeFile($newFile, $collection);
    }

    public function storeFile(UploadedFile $file, string $collection = 'images')
    {
        $this->addMedia($file)->toMediaCollection($collection);
    }

    public function storeMultipleFiles(array $files, string $collection = 'images')
    {
        foreach ($files as $file) {
            $this->storeFile($file, $collection);
        }
    }

    public function storeFileWithPreserving(UploadedFile $file, string $collection = 'images')
    {
        $this->addMedia($file)->preservingOriginal()->toMediaCollection($collection);
    }

    public function storeMultipleFilesWithPreserving(array $files, string $collection = 'images')
    {
        foreach ($files as $file) {
            $this->storeFileWithPreserving($file, $collection);
        }
    }
    
    public function deleteFirstFileFromCollectionIfExisted(string $collection)
    {
        if ($file = $this->getFirstMedia($collection)) {
            $file->delete();
        }
    }
}
