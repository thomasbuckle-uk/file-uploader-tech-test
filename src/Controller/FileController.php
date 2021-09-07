<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\FileManager;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function listFiles(
        Request $request
    ): Response {

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

        try {
            $filename = $this->fileManager->upload($request->files->get('file'));
            return new Response($filename, 200);
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage(), $e->getPrevious());
        }





    }
}