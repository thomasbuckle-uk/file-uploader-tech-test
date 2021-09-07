<?php
declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileManager
{
    private LoggerInterface $logger;
    private string $targetDir;
    private SluggerInterface $slugger;

    public function __construct(LoggerInterface $logger, string $targetDir, SluggerInterface $slugger)
    {
        $this->logger = $logger;
        $this->targetDir = $targetDir;
        $this->slugger = $slugger;
    }
    public function listFiles() :array {

    }

    public function upload(UploadedFile $file) :string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid('', true).'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDir(), $fileName);
        } catch (FileException $e) {
            $this->logger->error("Failed to move file to target directory", ["exception" => $e]);
            throw new FileException($e->getMessage(), 500, $e->getPrevious());
        }

        return $fileName;
    }

    private function getTargetDir()
    {
        return $this->targetDir;
    }
}