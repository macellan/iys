<?php

namespace Macellan\Iys\Drivers\Permission;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Macellan\Iys\Drivers\AbstractDriver;
use Macellan\Iys\Drivers\Permission\Enums\SourceTypes;
use Macellan\Iys\Drivers\Permission\Models\PermissionList;
use Macellan\Iys\Drivers\Permission\Models\Permission;

class PermissionDriver extends AbstractDriver
{
    protected int $timeout = 10;

    /**
     * @throws RequestException
     */
    public function sendSingle(Permission $permission)
    {
        return Http::timeout($this->timeout)
            ->withToken($this->bearer)->asJson()->acceptJson()
            ->post($this->buildUrl('consents'), $permission->toArray())
            ->throw();
    }

    /**
     * @throws RequestException
     */
    public function sendMultiple(PermissionList $permissionList)
    {
        return Http::timeout($this->timeout)
            ->withToken($this->bearer)->asJson()->acceptJson()
            ->post($this->buildUrl('consents/request'), $permissionList->toArray())
            ->throw();
    }

    /**
     * @throws RequestException
     */
    public function getStatusByRequestId(string $requestId)
    {
        return Http::timeout($this->timeout)
            ->withToken($this->bearer)->asJson()->acceptJson()
            ->get($this->buildUrl("consents/request/$requestId"))
            ->throw();
    }

    /**
     * @throws RequestException
     */
    public function getChanges(?string $after = null, SourceTypes $source = SourceTypes::IYS, int $limit = 999)
    {
        $query = [
           'source' => $source->value,
           'limit'  => $limit,
        ];

        if ($after) {
            $query['after'] = $after;
        }

        return Http::timeout($this->timeout)
           ->withToken($this->bearer)->asJson()->acceptJson()
           ->get($this->buildUrl('consents/changes?' . Arr::query($query)))
           ->throw();
    }

    /**
     * @throws RequestException
     */
    public function getStatus(Permission $permission)
    {
        $permissionData = $permission->toArray();

        return Http::timeout($this->timeout)
            ->withToken($this->bearer)->asJson()->acceptJson()
            ->post($this->buildUrl("consents/status"), [
                "recipient" => $permissionData['recipient'],
                "recipientType" => $permissionData['recipientType'],
                "type" => $permissionData['type']
            ])
            ->throw();
    }

    private function buildUrl(string $api): string
    {
        return sprintf('%s/sps/%s/brands/%s/%s', $this->baseUrl, $this->iysCode, $this->branchCode, $api);
    }
}
