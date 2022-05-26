<?php

namespace Macellan\Iys\Tests;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Macellan\Iys\Drivers\Permission\Enums\PermissionTypes;
use Macellan\Iys\Drivers\Permission\Enums\RecipientTypes;
use Macellan\Iys\Drivers\Permission\Enums\ConsentSourceTypes;
use Macellan\Iys\Drivers\Permission\Enums\StatusTypes;
use Macellan\Iys\Drivers\Permission\Enums\SourceTypes;
use Macellan\Iys\Drivers\Permission\Models\PermissionList;
use Macellan\Iys\Drivers\Permission\Models\Permission;
use Macellan\Iys\IysManager;

class PermissionDriverTest extends TestCase
{
    private function createHttpFakeToken()
    {
        Http::fake([
            '/oauth2/token' => Http::response([
                'access_token' => '162cc155429fdccd7f2be8e9482aa322993ff210c2923fb84e8ed0fb1902ae21',
                'refresh_token' => '2020-08-06 15:50:23',
                'expires_in' => 7200,
                'refresh_expires_in' => 14400,
                'token_type' => 'bearer',
            ]),
        ]);
    }

    /**
     * @return void
     * @throws RequestException
     */
    public function test_can_send_permission()
    {
        $endpoint = "/sps/$this->iysCode/brands/$this->brandCode/consents";

        $recipient = 'test@example.com';

        $consentDate = '2020-07-08 07:07:07';

        $this->createHttpFakeToken();

        Http::fake([
          $endpoint => Http::response([
                    'transaction_id' => '162cc155429fdccd7f2be8e9482aa322993ff210c2923fb84e8ed0fb1902ae21',
                    'creationDate' => '2020-08-06 15:50:23',
            ]),
        ]);

        IysManager::make()->createPermissionDriver()->sendSinglePermission(
            Permission::make()
                 ->setConsentDate($consentDate)
                 ->setSource(ConsentSourceTypes::MOBILE)
                 ->setRecipient($recipient)
                 ->setRecipientType(RecipientTypes::INDIVIDUAL)
                 ->setStatus(StatusTypes::APPROVE)
                 ->setType(PermissionTypes::EMAIL)
        );

        Http::assertSent(function (Request $request) use ($endpoint, $recipient, $consentDate) {
            return $request->url() == $this->url . $endpoint &&
                $request['consentDate'] == $consentDate &&
                $request['source'] == ConsentSourceTypes::MOBILE->value &&
                $request['recipient'] == $recipient &&
                $request['recipientType'] == RecipientTypes::INDIVIDUAL->value &&
                $request['status'] == StatusTypes::APPROVE->value &&
                $request['type'] == PermissionTypes::EMAIL->value;
        });
    }

    /**
     * @return void
     * @throws RequestException
     */
    public function test_throw_exception_send_permission()
    {
        $endpoint = "/sps/$this->iysCode/brands/$this->brandCode/consents";

        $recipient = 'test@example.com';

        $consentDate = '2020-07-08 07:07:07';

        $this->createHttpFakeToken();

        Http::fake([
            $endpoint => Http::response([
                "errors" => [
                    [
                        "message" => "Eksik veya geçersiz jeton!",
                        "code" => "H351",
                    ]
                ]
            ], 401),
        ]);

        $this->expectException(RequestException::class);

        IysManager::make()->createPermissionDriver()->sendSinglePermission(
            Permission::make()
                ->setConsentDate($consentDate)
                ->setSource(ConsentSourceTypes::MOBILE)
                ->setRecipient($recipient)
                ->setRecipientType(RecipientTypes::INDIVIDUAL)
                ->setStatus(StatusTypes::APPROVE)
                ->setType(PermissionTypes::EMAIL)
        );
    }

    /**
     * @return void
     * @throws RequestException
     */
    public function test_can_send_permissions()
    {
        $endpoint = "/sps/$this->iysCode/brands/$this->brandCode/consents/request";

        Http::fake([
            $endpoint => Http::response(
                [
                    'requestId' => '5980a44b06f333c25b1ah61bkceb95fa',
                    'subRequests' => [
                        'type' => 'EPOSTA',
                        'source' => 'HS_MOBIL',
                        'recipient' => 'test@example1.com',
                        'status' => 'ONAY',
                        'consentDate' => '2020-07-08 07:07:07',
                        'recipientType' => 'BIREYSEL',
                        'subRequestId' => '3e141ad8-cdd5-49f2-a4b9-28ed76bc9677',
                        'creationDate' => '2020-08-21 14:45:24',
                    ],
                    [
                        'type' => 'MESAJ',
                        'source' => 'HS_MOBIL',
                        'recipient' => 'test@example1.com',
                        'status' => 'RET',
                        'consentDate' => '2020-07-08 07:07:07',
                        'recipientType' => 'BIREYSEL',
                        'subRequestId' => '3e141ad8-cdd5-49f2-a4b9-28ed76bc9677',
                        'creationDate' => '2020-08-21 14:45:24',

                    ],
                ]
            )
        ]);

        $recipientFirst = 'test@example1.com';

        $recipientSecond = 'test@example1.com';

        $consentDate = '2020-07-08 07:07:07';

        $this->createHttpFakeToken();


        $permission = Permission::make()
            ->setConsentDate($consentDate)
            ->setSource(ConsentSourceTypes::MOBILE)
            ->setRecipientType(RecipientTypes::INDIVIDUAL);

         $permissionList =  PermissionList::make()
               ->addPermission($permission
                   ->setRecipient($recipientFirst)
                   ->setStatus(StatusTypes::APPROVE)
                   ->setType(PermissionTypes::EMAIL))
               ->addPermission($permission
                   ->setRecipient($recipientSecond)
                   ->setStatus(StatusTypes::REJECT)
                   ->setType(PermissionTypes::MESSAGE));

        IysManager::make()->createPermissionDriver()->sendPermissions($permissionList);

        Http::assertSent(function (Request $request) use ($endpoint, $recipientFirst, $recipientSecond, $consentDate) {
            return $request->url() == $this->url . $endpoint &&
                $request[0]['consentDate'] == $consentDate &&
                $request[0]['recipient'] == $recipientFirst &&
                $request[0]['source'] == ConsentSourceTypes::MOBILE->value &&
                $request[0]['recipientType'] == RecipientTypes::INDIVIDUAL->value &&
                $request[0]['status'] == StatusTypes::APPROVE->value &&
                $request[0]['type'] == PermissionTypes::EMAIL->value &&
                $request[1]['consentDate'] == $consentDate &&
                $request[1]['recipient'] == $recipientSecond &&
                $request[1]['source'] == ConsentSourceTypes::MOBILE->value &&
                $request[1]['recipientType'] == RecipientTypes::INDIVIDUAL->value &&
                $request[1]['status'] == StatusTypes::REJECT->value &&
                $request[1]['type'] == PermissionTypes::MESSAGE->value;
        });
    }

    /**
     * @return void
     * @throws RequestException
     */
    public function test_throw_exception_send_permissions()
    {
        $endpoint = "/sps/$this->iysCode/brands/$this->brandCode/consents/request";

        $this->createHttpFakeToken();

        Http::fake([
            $endpoint => Http::response([
                "errors" => [
                    [
                        "code" => "H124",
                        "message" => "Saniyede kabul edilen istek limitine ulaşıldı. Lütfen daha sonra 
                        tekrar deneyiniz."
                    ]
                ]
            ], 429),
        ]);

        $recipientFirst = 'test@example1.com';

        $recipientSecond = 'test@example1.com';

        $consentDate = '2020-07-08 07:07:07';

        $permission = Permission::make()
            ->setConsentDate($consentDate)
            ->setSource(ConsentSourceTypes::MOBILE)
            ->setRecipientType(RecipientTypes::INDIVIDUAL);

        $permissionList =  PermissionList::make()
            ->addPermission($permission
                ->setRecipient($recipientFirst)
                ->setStatus(StatusTypes::APPROVE)
                ->setType(PermissionTypes::EMAIL))
            ->addPermission($permission
                ->setRecipient($recipientSecond)
                ->setStatus(StatusTypes::REJECT)
                ->setType(PermissionTypes::MESSAGE));

        $this->expectException(RequestException::class);

        IysManager::make()->createPermissionDriver()->sendPermissions($permissionList);
    }

    /**
     * @return void
     * @throws RequestException
     */
    public function test_get_status_permissions()
    {
        $requestId = '5980a44b06f333c25b1ah61bkceb95fa';

        $endpoint = "/sps/$this->iysCode/brands/$this->brandCode/consents/request/$requestId";

        Http::fake([
            $endpoint => Http::response(
                [
                    [
                        "subRequestId" => "cf15b6bf-1b84-4e59-b5bb-bda95de5c1cf",
                        "index" => 0,
                        "status" => "failure",
                        "error" => [
                            "code" => "H175",
                            "message" => "İlk izin kayıt işlemlerinde RET durum (status) bilgisi 
                            içeren izinler kaydedilmez.",
                        ]
                    ],
                    [
                        "requestId" => "548014a006fb33c6wb1a0a1b0vebs5ae",
                        "subRequestId" => "2666eb07-cbc9-4069-bc6e-4eeb1a48991f",
                        "index" => 1,
                        "transactionId" => "432cc155429fdccd7f2be8e9482aa322993ff210c2923fb84e8ed0fb1902ae21",
                        "status" => "success",
                        "creationDate" => "2020-08-21 14:45:24",
                    ],
                    [
                        "index" => 2,
                        "status" => "enqueue",
                        "subrequestId" => "7ed0f89d-15c7-457f-a16a-d689ef5dca62",
                    ],
                ]
            )
        ]);


        $this->createHttpFakeToken();

        IysManager::make()->createPermissionDriver()->getPermissionsStatus($requestId);

        Http::assertSent(function (Request $request) use ($endpoint) {
            return $request->url() == $this->url . $endpoint;
        });
    }

    /**
     * @return void
     * @throws RequestException
     */
    public function test_throw_exception_get_status_permissions()
    {
        $requestId = '5980a44b06f333c25b1ah61bkceb95fa';

        $endpoint = "/sps/$this->iysCode/brands/$this->brandCode/consents/request/$requestId";

        $this->createHttpFakeToken();

        Http::fake([
            $endpoint => Http::response([
                "errors" => [
                    [
                        "code" => "H103",
                        "message" => "Sunucuda arama yapılamadı.",
                    ]
                ],
            ], 500),
        ]);

        $this->expectException(RequestException::class);

        IysManager::make()->createPermissionDriver()->getChangedPermissions();
    }

    /**
     * @return void
     * @throws RequestException
     */
    public function test_get_changed_permissions()
    {
        $query = Arr::query([
            'after' => null,
            'source' => SourceTypes::IYS->value,
            'limit' => 999,
        ]);

        $endpoint = "/sps/$this->iysCode/brands/$this->brandCode/consents/changes?$query";

        $this->createHttpFakeToken();

        Http::fake([
            $endpoint => Http::response(
                [
                    'after' => '1597740617_632cc155429fdccd7f2be8e9482aa322993ff210c2923fb84e8ed0fb1902ae21',
                    'list' => [
                        'consentDate' => '2018-02-10 09:50:02',
                        'creationDate' => '2020-02-10 09:50:02',
                        'source' => 'HS_WEB',
                        'recipient' => 'ornek@adiniz.com',
                        'recipientType' => 'BIREYSEL',
                        'status' => 'ONAY',
                        'type' => 'EPOSTA',
                        'transactionId' => '532cc155429fdccd7f2be8e9482aa322993ff210c2923fb84e8ed0fb1902ae21',
                        'citizenName' => 'Test isim',
                    ],
                ]
            )]);

        IysManager::make()->createPermissionDriver()->getChangedPermissions();

        Http::assertSent(function (Request $request) use ($endpoint) {
            return $request->url() == $this->url . $endpoint;
        });
    }

    /**
     * @throws RequestException
     */
    public function test_get_changed_permissions_with_options()
    {
        $limit = 50;

        $after = '1597740617_632cc155429fdccd7f2be8e9482aa322993ff210c2923fb84e8ed0fb1902ae22';

        $query = Arr::query([
            'source' => SourceTypes::HS->value,
            'limit' => $limit,
            'after' => $after,
        ]);

        $endpoint = "/sps/$this->iysCode/brands/$this->brandCode/consents/changes?$query";

        $this->createHttpFakeToken();

        Http::fake([
            $endpoint => Http::response(
                [
                    'after' => '1597740617_632cc155429fdccd7f2be8e9482aa322993ff210c2923fb84e8ed0fb1902ae21',
                    'list' => [
                        'consentDate' => '2018-02-10 09:50:02',
                        'creationDate' => '2020-02-10 09:50:02',
                        'source' => 'HS_WEB',
                        'recipient' => 'ornek@adiniz.com',
                        'recipientType' => 'BIREYSEL',
                        'status' => 'ONAY',
                        'type' => 'EPOSTA',
                        'transactionId' => '532cc155429fdccd7f2be8e9482aa322993ff210c2923fb84e8ed0fb1902ae21',
                        'citizenName' => 'Test isim',
                    ],
                ]
            )]);

        IysManager::make()->createPermissionDriver()->getChangedPermissions($after, SourceTypes::HS, $limit);

        Http::assertSent(function (Request $request) use ($endpoint) {
            return $request->url() == $this->url . $endpoint;
        });
    }

    /**
     * @return void
     * @throws RequestException
     */
    public function test_throw_exception_get_changed_permissions()
    {
        $query = Arr::query([
            'after' => null,
            'source' => SourceTypes::IYS->value,
            'limit' => 999,
        ]);

        $endpoint = "/sps/$this->iysCode/brands/$this->brandCode/consents/changes?$query";

        $this->createHttpFakeToken();

        Http::fake([
            $endpoint => Http::response([
                "errors" => [
                    [
                        "location" => [],
                        "code" => "H168",
                        "message" => "Alıcı listesi (recipients) bulunamadı.",
                    ]
                ]
            ], 422),
         ]);

        $this->expectException(RequestException::class);

        IysManager::make()->createPermissionDriver()->getChangedPermissions();
    }

    /**
     * @throws RequestException
     */
    public function test_get_permission_status()
    {
        $recipient = 'test@example.com';

        $consentDate = '2020-07-08 07:07:07';

        $endpoint = "/sps/$this->iysCode/brands/$this->brandCode/consents/status";

        $this->createHttpFakeToken();

        Http::fake([
            $endpoint => Http::response([
                    'consentDate' => $consentDate,
                    'source' => ConsentSourceTypes::MOBILE,
                    'recipientType' => RecipientTypes::INDIVIDUAL,
                    'status' => StatusTypes::APPROVE,
                    'type' => PermissionTypes::EMAIL,
                    'recipient' => $recipient,
                    'retailerCode' => 55550127,
                    'creationDate' => '2020-08-06 15:50:23',
                    'retailerTitle' => 'Test Company',
                    'retailerAccessCount' => 3,
                    'transactionId' => 'abc623z3cq4bhac9b88dadd49b767a2322be140a9n9cuc25abf1ac5392c4ca12',
                ])
            ]);

        IysManager::make()->createPermissionDriver()->getPermissionStatus(Permission::make()
            ->setConsentDate($consentDate)
            ->setSource(ConsentSourceTypes::MOBILE)
            ->setRecipient($recipient)
            ->setRecipientType(RecipientTypes::INDIVIDUAL)
            ->setStatus(StatusTypes::APPROVE)
            ->setType(PermissionTypes::EMAIL));

        Http::assertSent(function (Request $request) use ($endpoint, $recipient) {
            return $request->url() == $this->url . $endpoint &&
                $request['recipient'] == $recipient &&
                $request['recipientType'] == RecipientTypes::INDIVIDUAL->value &&
                $request['type'] == PermissionTypes::EMAIL->value;
        });
    }

    public function test_throw_exception_get_permission_status()
    {
        $endpoint = "/sps/$this->iysCode/brands/$this->brandCode/consents/status";

        $this->createHttpFakeToken();

        Http::fake([
            $endpoint => Http::response([
                [
                    "errors" => [
                        [
                            "code" => "H097",
                            "message" => "İzin sorgulama isteği geçerli olmalıdır."
                        ]
                    ]
                ]], 400),
            ]);

        $this->expectException(RequestException::class);

        IysManager::make()->createPermissionDriver()->getChangedPermissions();
    }
}
