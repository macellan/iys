<?php

namespace Macellan\Iys\Drivers\Permission\Models;

use JetBrains\PhpStorm\ArrayShape;
use Macellan\Iys\Drivers\Permission\Enums\PermissionTypes;
use Macellan\Iys\Drivers\Permission\Enums\RecipientTypes;
use Macellan\Iys\Drivers\Permission\Enums\ConsentSourceTypes;
use Macellan\Iys\Drivers\Permission\Enums\StatusTypes;

class Permission implements InterfaceModel
{
    private string $consentDate;

    private string $recipient;

    private ConsentSourceTypes $source;

    private RecipientTypes $recipientType;

    private StatusTypes $status;

    private PermissionTypes $type;

    private ?int $retailerCode = null;

    private ?array $retailerAccess = null;

    /**
     * @param string $consentDate
     * @return Permission
     */
    public function setConsentDate(string $consentDate): self
    {
        $this->consentDate = $consentDate;

        return $this;
    }

    /**
     * @param ConsentSourceTypes $source
     * @return Permission
     */
    public function setSource(ConsentSourceTypes $source): self
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @param string $recipient
     * @return Permission
     */
    public function setRecipient(string $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * @param StatusTypes $status
     * @return Permission
     */
    public function setStatus(StatusTypes $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param RecipientTypes $recipientType
     * @return Permission
     */
    public function setRecipientType(RecipientTypes $recipientType): self
    {
        $this->recipientType = $recipientType;

        return $this;
    }

    /**
     * @param PermissionTypes $type
     * @return Permission
     */
    public function setType(PermissionTypes $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param int $retailerCode
     * @return Permission
     */
    public function setRetailerCode(int $retailerCode): self
    {
        $this->retailerCode = $retailerCode;

        return $this;
    }

    /**
     * @param array $retailerAccess
     * @return Permission
     */
    public function setRetailerAccess(array $retailerAccess): self
    {
        $this->retailerAccess = $retailerAccess;

        return $this;
    }

    /**
     * @return array
     */
    #[ArrayShape(
        [
            'consentDate' => "string",
            'source' => "string",
            'recipient' => "string",
            'recipientType' => "string",
            'status' => "string",
            'type' => "string",
            'retailerAccess' => "array|null",
            'retailerCode' => "int|null"
        ]
    )]
    public function toArray(): array
    {
        $array = [
           'consentDate' => $this->consentDate,
           'source' => $this->source->value,
           'recipient' => $this->recipient,
           'recipientType' => $this->recipientType->value,
           'status' => $this->status->value,
           'type' => $this->type->value,
        ];

        if ($this->retailerCode) {
            $array['retailerCode'] = $this->retailerCode;
        }

        if ($this->retailerAccess) {
            $array['retailerAccess'] = $this->retailerAccess;
        }

        return $array;
    }

    public static function make(): Permission
    {
        return new static();
    }
}
