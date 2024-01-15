<?php

namespace App\Controller;

use App\Entity\CustomerCallUploads;
use App\Repository\CustomerCallUploadsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CustomerCallUploadController extends AbstractController
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    public function index(CustomerCallUploadsRepository $repository)
    {
        $data = $repository->findAllOrderedByIdDesc();

        return new JsonResponse(['result' => $data], JsonResponse::HTTP_OK);
    }
}
