<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\File;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\FormSizeFileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileManager
{
    private LoggerInterface $logger;
    private string $targetDir;
    private SluggerInterface $slugger;

    public function __construct(
        LoggerInterface $logger,
        string $targetDir,
        SluggerInterface $slugger,
        EntityManagerInterface $entityManager
    ) {
        $this->logger = $logger;
        $this->targetDir = $targetDir;
        $this->slugger = $slugger;
        $this->em = $entityManager;
    }

    public function listFiles(): array
    {

    }

    public function upload(UploadedFile $file): File
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid('', true).'.'.$file->guessExtension();
        if ($file->getSize() > 1000000) {
            $sizeMb = number_format($file->getSize() / 1000000, 2);
            throw new FormSizeFileException("File of size {$sizeMb}MB is over the 1MB File Limit");
        }
        try {
            $file->move($this->getTargetDir(), $fileName);
        } catch (FileException $e) {
            $this->logger->error("Failed to move file to target directory", ["exception" => $e]);
            throw new FileException($e->getMessage(), 500, $e->getPrevious());
        }

        $fileEntity = new File();
        $fileEntity->setFileName($fileName);
        $fileEntity->setOriginalFilename($originalFilename);
        $this->em->persist($fileEntity);
        $this->em->flush();

        return $fileEntity;
    }

    private function getTargetDir()
    {
        return $this->targetDir;
    }
}