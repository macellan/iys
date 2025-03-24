<?php

namespace Macellan\Iys\Drivers\Permission\Models;

class PermissionList implements InterfaceModel
{
    private array $permissions = [];

    public function addPermission(Permission $singlePermission): self
    {
        $this->permissions[] = $singlePermission->toArray();

        return $this;
    }

    public function isEmpty(): bool
    {
        return empty($this->permissions);
    }

    public function toArray(): array
    {
        return $this->permissions;
    }

    public static function make(): PermissionList
    {
        return new self;
    }
}
