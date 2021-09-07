<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

#[Route('/api/files')]
class FileController extends AbstractController
{

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
     *     type="string",
     *     property="file",
     *     format= "binary",
     *     description="file to upload"
     * )
     *     )
     *     )
     * )
     **/

    public function uploadFile(): File
    {
    }
}