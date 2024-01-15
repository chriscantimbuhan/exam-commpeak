<?php

namespace App\Message;

use App\Entity\CustomerCallUploads;
use Doctrine\Common\Collections\ArrayCollection;

final class UpsertCallMessage
{
    /*
     * Add whatever properties and methods you need
     * to hold the data for this message class.
     */

     /**
      * @var string
      */
    private $filePath;

     /**
      * @var \App\Entity\CustomerCallUploads
      */
      private $uploadedFile;

    /**
     * @var string
     */
    private $rawData;

    public function __construct(string $filePath, CustomerCallUploads $uploadedFile)
    {
        $this->filePath = $filePath;
        $this->uploadedFile = $uploadedFile;
    }

    public function getPath() : string
    {
        return $this->filePath;
    }

    public function getRawData(): string
    {
        $this->rawData = file_get_contents('public/' . $this->filePath);

        return $this->rawData;
    }

    public function getDataForProcess(int $startingKey, int $totalRecords): array
    {
        return (new ArrayCollection(explode("\n", $this->rawData)))
            ->filter(function ($row) {
                return strlen($row) > 0;
            })->slice($startingKey, $totalRecords);
    }

    public function getTotalCount(): int
    {
        return (new ArrayCollection(explode("\n", $this->rawData)))
            ->filter(function ($row) {
                return strlen($row) > 0;
            })->count();
    }

    public function getUploadedFile(): CustomerCallUploads
    {
        return $this->uploadedFile;
    }
}
