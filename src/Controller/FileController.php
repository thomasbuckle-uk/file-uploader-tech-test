<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\File;
use App\Service\FileManager;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

#[Route('/api/files')]
class FileController extends AbstractController
{
    private LoggerInterface $logger;
    private FileManager $fileManager;

    public function __construct(LoggerInterface $logger, FileManager $fileManager)
    {
        $this->logger = $logger;
        $this->fileManager = $fileManager;
    }

    #[Route('/', methods: ['GET'])]
    /**
     * List Currently Available Files available for Download
     * @OA\Tag(name="Files Managment")
     * @OA\Response(
     *     response=200,
     *     description="Returns list of files available for download",
     * )
     **/
    public function files(
        Request $request
    ): Response {
        $fileRepository = $this->getDoctrine()->getRepository(File::class);
        $fileList = $fileRepository->findAll();

        return $this->json($fileList);
    }

    #[Route('/downloadable/', methods: ['GET'])]
    /**
     * Return list of File Download Paths
     * @OA\Tag(name="Files Managment")
     * @OA\Response(
     *     response=200,
     *     description="Return list of File Download Paths",
     * )
     **/
    public function listFileDownloadUrls(Request $request): JsonResponse
    {
        $fileRepository = $this->getDoctrine()->getRepository(File::class);

        $files = $fileRepository->findAll();
        $fileUrls = [];
        foreach ($files as $file) {
            $fileUrls += [$file->getOriginalFilename() => $file->getDownloadPath($this->getParameter('app.target_dir'))];
        }
        return $this->json($fileUrls);
    }


    #[Route('/download/{fileName}', methods: ['GET'])]
    /**
     * Downloads File Based from Slug URI of matching filename in Database
     * @OA\Tag(name="Files Managment")
     * @OA\Response(
     *     response=200,
     *     description="Requested File as a BinaryFileResponse",
     * )
     **/
    public function downloadFile(File $file): Response
    {
        $response = new BinaryFileResponse( $file->getDownloadPath($this->getParameter('app.target_dir')));
        $response->headers->set ( 'Content-Type', 'text/plain' );
        $response->setContentDisposition ( ResponseHeaderBag::DISPOSITION_ATTACHMENT, $file->getFileName());
        return $response;

    }

//  Using POST not PUT for file upload = means this is non-idempotent
    #[Route('/upload', methods: ['POST'])]
    /**
     * File upload
     * @OA\Tag(name="Files Managment")
     * @OA\Response(
     *     response=200,
     *     description="Successfully uploaded provided file & Provide Persistant download URI"
     * )
     * @OA\RequestBody(
     *     required=true,
     *     description="File to be uploaded",
     *     @OA\MediaType(
     *      mediaType="multipart/form-data",
     *     @OA\Schema(
     *     @OA\Property(
     *     title="file",
     *     type="string",
     *     property="file",
     *     format= "binary",
     *     description="file to upload"
     * )
     *     )
     *     )
     * )
     **/

    public function uploadFile(Request $request): Response
    {
//        No file in request so lets exit early
        if (!$request->files) {
            return new Response("error file not in request body", 500);
        }
        $this->logger->debug(json_encode($_FILES, JSON_THROW_ON_ERROR));
        $file = $request->files->get('file');
        try {
            $filename = $this->fileManager->upload($file);

            return new Response(json_encode(['storedFilename' => $filename], JSON_THROW_ON_ERROR), 200);
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage(), $e->getPrevious());
        }


    }
}