<?php

namespace Macellan\Iys\Tests;

use Macellan\Iys\Drivers\Permission\Enums\PermissionTypes;
use Macellan\Iys\Drivers\Permission\Enums\RecipientTypes;
use Macellan\Iys\Drivers\Permission\Enums\ConsentSourceTypes;
use Macellan\Iys\Drivers\Permission\Enums\StatusTypes;
use Macellan\Iys\Drivers\Permission\Models\Permission;
use Macellan\Iys\Drivers\Permission\Models\PermissionList;

class PermissionListModelTest extends TestCase
{
    public function test_create()
    {
        $recipientFirst = 'test@example1.com';

        $consentDateFirst = '2020-07-08 07:07:07';

        $permissionFirst = Permission::make()
            ->setConsentDate($consentDateFirst)
            ->setSource(ConsentSourceTypes::MOBILE)
            ->setRecipientType(RecipientTypes::INDIVIDUAL)
            ->setRecipient($recipientFirst)
            ->setStatus(StatusTypes::APPROVE)
            ->setType(PermissionTypes::EMAIL);


        $recipientSecond = 'test@example1.com';

        $consentDateSecond = '2020-09-01 07:06:07';

        $retailerCode = 55550127;

        $retailerAccess = [
            58795354,
            30143398,
        ];

        $permissionSecond = Permission::make()
            ->setConsentDate($consentDateSecond)
            ->setSource(ConsentSourceTypes::EMAIL)
            ->setRecipientType(RecipientTypes::TRADER)
            ->setRecipient($recipientSecond)
            ->setStatus(StatusTypes::REJECT)
            ->setType(PermissionTypes::CALL)
            ->setRetailerCode($retailerCode)
            ->setRetailerAccess($retailerAccess);

        $permissionList = PermissionList::make()
            ->addPermission($permissionFirst)
            ->addPermission($permissionSecond);

        $this->assertEquals([
            [
                'consentDate' => $consentDateFirst,
                'source' => ConsentSourceTypes::MOBILE->value,
                'recipient' => $recipientFirst,
                'recipientType' => RecipientTypes::INDIVIDUAL->value,
                'status' => StatusTypes::APPROVE->value,
                'type' => PermissionTypes::EMAIL->value,
            ],
            [
                'consentDate' => $consentDateSecond,
                'source' => ConsentSourceTypes::EMAIL->value,
                'recipient' => $recipientSecond,
                'recipientType' => RecipientTypes::TRADER->value,
                'status' => StatusTypes::REJECT->value,
                'type' => PermissionTypes::CALL->value,
                'retailerCode' => $retailerCode,
                'retailerAccess' => $retailerAccess,
            ]
        ], $permissionList->toArray());
    }
}
