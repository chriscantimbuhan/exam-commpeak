<?php

namespace App\MessageHandler;

use App\Entity\CustomerCalls;
use App\Entity\CustomerCallUploads;
use App\Message\UpsertCallMessage;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use stdClass;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class UpsertCallMessageHandler implements MessageHandlerInterface
{
    /**
     * @var \App\Message\UpsertCallMessage
     */
    protected $messasgeInstance;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var int
     */
    protected $totalCount;

    /**
     * @var \App\Entity\CustomerCallUploads
     */
    protected $uploadedFile;

    /**
     * Initialize Handler
     */
    public function __construct( EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Invoke Handler
     */
    public function __invoke(UpsertCallMessage $message): void
    {
        if ($message instanceof UpsertCallMessage) {
            $message->getPath();
            $message->getRawData();

            $this->messasgeInstance = $message;
            $this->totalCount = $message->getTotalCount();
            $this->uploadedFile = $message->getUploadedFile();

            $this->execute();
        }
    }

    /**
     * Execute the process
     */
    protected function execute(): void
    {
        $recordKey = 0;
        $start = 1;
        $end = 10;

        $fields = [
            'id', 'call_datetime', 'duration',
            'dialed_phone_number', 'customer_ip'
        ];

        $rowArray = [];

        while (count($forProcess = $this->messasgeInstance->getDataForProcess($recordKey, 10)) > 0) {
            $uploadedFile = $this->entityManager
                ->getRepository(CustomerCallUploads::class)->find($this->uploadedFile->getId());

            echo 'Processing ' . $start . ' to ' . $end . ' of ' . $this->totalCount. "\n";

            foreach ($forProcess as $processKey => $row) {
                $row = explode(',', $row);
                foreach ($row as $key => $val) {
                    $rowArray[$processKey][$fields[$key]] = $val;
                }

                $continentData = $this->getIpContinentData($rowArray[$processKey]['customer_ip']);

                $rowArray[$processKey]['customer_continent_code'] = $continentData->continent_code;
                $rowArray[$processKey]['customer_continent_name'] = $continentData->continent_name;
                $rowArray[$processKey]['dialed_phone_continent_code'] = $this->getPhoneContinent(
                        $rowArray[$processKey]['dialed_phone_number']
                    );
                
                $this->upsertData($rowArray[$processKey]);
            }

            $uploadedFile->setProcessedCount($end);

            $start = $end + 1;
            $recordKey += 10;
            $end += 10;

            if ($end > $this->totalCount) {
                $end = $this->totalCount;
            }

            if ($this->totalCount == $uploadedFile->getProcessedCount()) {
                $uploadedFile->setStatus(CustomerCallUploads::STATUS_COMPLETED);

                echo 'Completed';
            }

            $this->entityManager->flush();
        }
    }

    /**
     * Upsert given data
     */
    protected function upsertData(array $forUpsert): void
    {
        if ($forUpsert['id']) {
            $persist = true;

            $customerCalls = new CustomerCalls;

            $entity = $this->entityManager->createQueryBuilder()
                ->select('customer_calls')
                ->from('App\Entity\CustomerCalls', 'customer_calls')
                ->where('customer_calls.import_id = :import_id')
                ->andWhere('customer_calls.call_datetime = :call_datetime')
                ->andWhere('customer_calls.duration = :duration')
                ->setParameters([
                    'import_id' => $forUpsert['id'],
                    'call_datetime' => $forUpsert['call_datetime'],
                    'duration' => $forUpsert['duration']
                ])
                ->getQuery()
                ->getOneOrNullResult();

            if ($entity) {
                $customerCalls = $entity;
                $persist = false;
            }

            $this->upsertCustomerCall($customerCalls, $forUpsert, $persist);
        }
    }


    /**
     * Store Customer Call from CSV
     */
    protected function upsertCustomerCall(CustomerCalls $customerCalls, array $forUpsert, bool $persist): void
    {
        $dateTime = new DateTime($forUpsert['call_datetime']);

        $customerCalls->setImportId($forUpsert['id']);
        $customerCalls->setCallDatetime($dateTime);
        $customerCalls->setDuration($forUpsert['duration']);
        $customerCalls->setDialedPhoneNumber($forUpsert['dialed_phone_number']);
        $customerCalls->setCustomerIp($forUpsert['customer_ip']);
        $customerCalls->setCustomerContinentCode($forUpsert['customer_continent_code']);
        $customerCalls->setCustomerContinentName($forUpsert['customer_continent_name']);
        $customerCalls->setDialedPhoneContinentCode($forUpsert['dialed_phone_continent_code']);

        if ($persist) {
            $this->entityManager->persist($customerCalls);
        }

        $this->entityManager->flush();
    }

    /**
     * Get Continent Data based on IP
     */
    protected function getIpContinentData(string $ip): object
    {
        $entity = $this->entityManager->createQueryBuilder()
            ->select('customer_calls')
            ->from('App\Entity\CustomerCalls', 'customer_calls')
            ->where('customer_calls.customer_ip = :customer_ip')
            ->setParameters([
                'customer_ip' => $ip
            ])
            ->getQuery()
            ->getResult();

        if (count($entity) == 0) {
            $fields = 'continent_code,continent_name';

            $apiEndpoint = $_ENV['IP_GEO_HOST_URL'] .  '/ipgeo?apiKey=';
            $apiEndpoint .= $_ENV['IP_GEO_API_KEY'] . '&ip=' . $ip . '&fields=' . $fields;
    
            $httpClient = HttpClient::create();
    
            try {    
                $response = $httpClient->request('GET', $apiEndpoint);
    
                if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                     return (object) $response->toArray();
                } 
                else {
                    return new Response('API request failed', $response->getStatusCode());
                }
            } catch (\Exception $e) {
                return new Response($e->getMessage(), 500);
            }
        } else {
            $entity = reset($entity);

            $continentData = new stdClass;
            $continentData->continent_code = $entity->getCustomerContinentCode();
            $continentData->continent_name = $entity->getCustomerContinentName();

            return $continentData;
        }
    }

    /**
     * Get continent based on phone number
     */
    protected function getPhoneContinent(string $phoneNumber): string
    {
        foreach ($this->parsePhoneInfo() as $row) {
            foreach ($row as $column => $value) {
                if ($column == 'Phone' && strlen($value) > 0) {
                    if (strpos($value, 'and') === false) {
                        if (substr($phoneNumber, 0,  strlen($value)) == $value) {
                            return $row['Continent'];
                        }
                    } else {
                        $withAnd = explode(' and ', $value);

                        foreach ($withAnd as $value) {
                            if (substr($phoneNumber, 0,  strlen($value)) == $value) {
                                return $row['Continent'];
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Get phone information for Continent data
     */
    protected function parsePhoneInfo(): array
    {
        $fileUrl = $_ENV['PHONE_INFO_URL'];

        $httpClient = HttpClient::create();
        
        $targetString = '#ISO';
        $startLine = 0;
        $columns = [];
        $tableData = [];
        $formattedData = [];

        try {
            $response = $httpClient->request('GET', $fileUrl);

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                $fileContents = $response->getContent();

                foreach (explode(PHP_EOL, $fileContents) as $key => $line) {
                    if (strpos($line, $targetString) !== false) {
                        $startLine = $key;
                        $columns = explode("\t", str_replace("\r", '', $line));
                    }

                    if ($key != $startLine && $startLine > 0) {
                        $tableData[] = explode("\t", str_replace("\r", '', $line));
                    }
                }
                
                foreach ($tableData as $key => $row) {
                    if (count($row) == count($columns)) {
                        foreach ($row as $rowKey => $rowValue) {
                            $formattedData[$key][$columns[$rowKey]] = $rowValue;
                        }
                    }
                }

                return $formattedData;
            } else {
                return new Response('File request failed', $response->getStatusCode());
            }
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }
    }
}
