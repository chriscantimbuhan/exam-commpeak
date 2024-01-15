<?php

namespace App\Controller;

use App\Entity\CustomerCalls;
use App\Entity\CustomerCallUploads;
use App\Form\CustomerCallsFileUploadType;
use App\Message\UpsertCallMessage;
use App\Repository\CustomerCallsRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerCallController extends AbstractController
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    public function __construct( EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function index(): Response
    {
        return new Response('Available Calls goes here');
    }

    public function processUpload(Request $request, ValidatorInterface $validator, MessageBusInterface $bus): JsonResponse
    {
        $file = $request->files->get('customer_call_file');

        if ($this->validateUpload($file, $validator)) {
            return $this->validateUpload($file, $validator);
        }

        $this->makeDirectory();

        $date = date('Y-m-d_H-i-s');

        $originalFilename = $file->getClientOriginalName();
        $fileName = 'customer_calls_' . $date . '.' . $file->getClientOriginalExtension();

        $uploaded = $file->move(
            'customer_calls',
            $fileName
        );

        if ($uploaded && (new Filesystem)->exists('customer_calls/' . $fileName)) {
            $storedFile = $this->storeFile($originalFilename, $fileName);

            if ($storedFile) {
                $message = new UpsertCallMessage('customer_calls/' . $fileName, $storedFile);
            }

            $bus->dispatch($message);
        }

        return new JsonResponse(['sucess' => true], JsonResponse::HTTP_OK);
    }

    protected function validateUpload($file, ValidatorInterface $validator)
    {
        $violations = $validator->validate($file, [
            new File([
                'mimeTypes' => [
                    'text/csv',
                    'text/plain'
                ],
                'mimeTypesMessage' => 'File input field type is invalid.'
            ]),
            new NotBlank(['message' => 'File input field is required.'])
        ]);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors['customer_call_file'] = $violation->getMessage();
            }

            return new JsonResponse(['errors' => $errors], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Store data to Customer Call Uploads
     */
    protected function storeFile(string $originalFilename, string $fileName)
    {
        $rawData = file_get_contents('customer_calls/' . $fileName);

        $totalCount = (new ArrayCollection(explode("\n", $rawData)))
        ->filter(function ($row) {
            return strlen($row) > 0;
        })->count();

        $uploadedFile = new CustomerCallUploads;

        $uploadedFile->setOriginalFilename($originalFilename);
        $uploadedFile->setFilename($fileName);
        $uploadedFile->setStatus(CustomerCallUploads::STATUS_PROCESSING);
        $uploadedFile->setDisk('public/customer_calls');
        $uploadedFile->setTotalCount($totalCount);
        $uploadedFile->setProcessedCount(0);
        $uploadedFile->setCreatedAt((new DateTime()));

        $this->entityManager->persist($uploadedFile);
        $this->entityManager->flush();

        return $uploadedFile;
    }

    /**
     * Get Statistics Data
     */
    public function getStatistics(CustomerCallsRepository $customerCallsRepository, EntityManagerInterface $entityManagerInterface): JsonResponse
    {
        $data = $customerCallsRepository->findStatisticsData($entityManagerInterface);

        return new JsonResponse(['result' => $data], JsonResponse::HTTP_OK);
    }

    /**
     * Make directory if not available
     */
    protected function makeDirectory(): void
    {
        $directory = 'customer_calls';
        $fileSystem = new Filesystem;

        if (! $fileSystem->exists($directory)) {
            $fileSystem->mkdir($directory);
        }
    }
}
