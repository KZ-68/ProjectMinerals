<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileDownloader {
    
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function download($imageName) {

        $projectRoot = $this->getTargetDirectory();

        $file = new File($projectRoot.'/'.$imageName);

        $response = new BinaryFileResponse($file);

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $imageName
        );

        return $response;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}