<?php

namespace Macellan\Iys\Tests;

use Macellan\Iys\Drivers\Permission\Enums\PermissionTypes;
use Macellan\Iys\Drivers\Permission\Enums\RecipientTypes;
use Macellan\Iys\Drivers\Permission\Enums\ConsentSourceTypes;
use Macellan\Iys\Drivers\Permission\Enums\StatusTypes;
use Macellan\Iys\Drivers\Permission\Models\Permission;

class PermissionModelTest extends TestCase
{
    public function test_create()
    {
        $recipient = 'test@example.com';

        $consentDate = '2020-07-08 07:07:07';

        $permission = Permission::make()
            ->setConsentDate($consentDate)
            ->setSource(ConsentSourceTypes::MOBILE)
            ->setRecipient($recipient)
            ->setRecipientType(RecipientTypes::INDIVIDUAL)
            ->setStatus(StatusTypes::APPROVE)
            ->setType(PermissionTypes::EMAIL);

        $this->assertEquals([
            'consentDate' => $consentDate,
            'source' => ConsentSourceTypes::MOBILE->value,
            'recipient' => $recipient,
            'recipientType' => RecipientTypes::INDIVIDUAL->value,
            'status' => StatusTypes::APPROVE->value,
            'type' => PermissionTypes::EMAIL->value,
        ], $permission->toArray());
    }

    public function test_create_optional_parameters()
    {
        $recipient = 'test@example.com';

        $consentDate = '2020-07-08 07:07:07';

        $retailerCode = 55550127;

        $retailerAccess = [
            58795354,
            30143398,
        ];

        $permission = Permission::make()
            ->setConsentDate($consentDate)
            ->setSource(ConsentSourceTypes::MOBILE)
            ->setRecipient($recipient)
            ->setRecipientType(RecipientTypes::INDIVIDUAL)
            ->setStatus(StatusTypes::APPROVE)
            ->setType(PermissionTypes::EMAIL)
            ->setRetailerCode($retailerCode)
            ->setRetailerAccess($retailerAccess);

        $this->assertEquals([
            'consentDate' => $consentDate,
            'source' => ConsentSourceTypes::MOBILE->value,
            'recipient' => $recipient,
            'recipientType' => RecipientTypes::INDIVIDUAL->value,
            'status' => StatusTypes::APPROVE->value,
            'type' => PermissionTypes::EMAIL->value,
            'retailerCode' => $retailerCode,
            'retailerAccess' => $retailerAccess,
        ], $permission->toArray());
    }
}
