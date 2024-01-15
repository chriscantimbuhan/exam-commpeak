<?php

namespace App\Entity;

use App\Repository\CustomerCallsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CustomerCallsRepository::class)
 */
class CustomerCalls
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $call_datetime;

    /**
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $dialed_phone_number;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $customer_ip;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $customer_continent_code;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $customer_continent_name;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $dialed_phone_continent_code;

    /**
     * @ORM\Column(type="integer")
     */
    private $import_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCallDatetime(): ?\DateTimeInterface
    {
        return $this->call_datetime;
    }

    public function setCallDatetime(\DateTimeInterface $call_datetime): self
    {
        $this->call_datetime = $call_datetime;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getDialedPhoneNumber(): ?string
    {
        return $this->dialed_phone_number;
    }

    public function setDialedPhoneNumber(string $dialed_phone_number): self
    {
        $this->dialed_phone_number = $dialed_phone_number;

        return $this;
    }

    public function getCustomerIp(): ?string
    {
        return $this->customer_ip;
    }

    public function setCustomerIp(string $customer_ip): self
    {
        $this->customer_ip = $customer_ip;

        return $this;
    }

    public function getCustomerContinentCode(): ?string
    {
        return $this->customer_continent_code;
    }

    public function setCustomerContinentCode(string $customer_continent_code): self
    {
        $this->customer_continent_code = $customer_continent_code;

        return $this;
    }

    public function getCustomerContinentName(): ?string
    {
        return $this->customer_continent_name;
    }

    public function setCustomerContinentName(string $customer_continent_name): self
    {
        $this->customer_continent_name = $customer_continent_name;

        return $this;
    }

    public function getDialedPhoneContinentCode(): ?string
    {
        return $this->dialed_phone_continent_code;
    }

    public function setDialedPhoneContinentCode(?string $dialed_phone_continent_code): self
    {
        $this->dialed_phone_continent_code = $dialed_phone_continent_code;

        return $this;
    }

    public function getImportId(): ?int
    {
        return $this->import_id;
    }

    public function setImportId(int $import_id): self
    {
        $this->import_id = $import_id;

        return $this;
    }
}
