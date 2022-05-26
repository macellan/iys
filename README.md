## Contents

- [Installation](#installation)
    - [Setting up the Iys service](#setting-up-the-IYS-service)
- [Enums](#enums)
- [Usage](#usage)
- [Testing](#testing)
- [License](#license)

## Installation

You can install this package via composer:

``` bash
composer require macellan/iys
```

### Setting up the IYS service

Add your IYS login to your config/services.php:

```php
// config/services.php
...
    'iys' => [
        'username' => env('IYS_USERNAME', ''),
        'password' => env('IYS_PASSWORD', ''),
        'iys_code' => env('IYS_CODE', ''),
        'brand_code' => env('IYS_BRAND_CODE', ''),
        'url' => env('IYS_URL', ''),
    ],
...
```

## Enums

IYS consent source types:

```php
enum ConsentSourceTypes: string
{
    case PHYSICAL = 'HS_FIZIKSEL_ORTAM';
    case WET_SIGNATURE = 'HS_ISLAK_IMZA';
    case WEB = 'HS_WEB';
    case CALL_CENTER = 'HS_CAGRI_MERKEZI';
    case SOCIAL_MEDIA = 'HS_SOSYAL_MEDYA';
    case EMAIL = 'HS_EPOSTA';
    case MESSAGE = 'HS_MESAJ';
    case MOBILE = 'HS_MOBIL';
    case HS_EORTAM = 'HS_EORTAM';
    case ACTIVITY = 'HS_ETKINLIK';
    case HS_2015 = 'HS_2015';
    case HS_ATM = 'HS_ATM';
    case HS_DECISION = 'HS_KARAR';
}
```

IYS permission types:

```php
enum PermissionTypes: string
{
    case CALL = 'ARAMA';
    case MESSAGE = 'MESAJ';
    case EMAIL = 'EPOSTA';
}
```

IYS recipient types:

```php
enum RecipientTypes: string
{
    case INDIVIDUAL = 'BIREYSEL';
    case TRADER = 'TACIR';
}
```

IYS source types:

```php
enum SourceTypes: string
{
    case HS = 'HS';
    case IYS = 'IYS';
}
```

IYS status types:

```php
enum StatusTypes: string
{
    case APPROVE = 'ONAY';
    case REJECT = 'RET';
}
```

## Usage

**NOTE:**
Authentication token is generated per UserManager instance. Expire time is two hour.

With the usage in this example, you can submit a single release. This method works with the "Permission" model

```php
    IysManager::make()->createPermissionDriver()->sendSinglePermission(
            Permission::make()
                ->setConsentDate('2022-02-10 09:50:02')
                ->setSource(ConsentSourceTypes::MOBILE)
                ->setRecipient('example@.com')
                ->setRecipientType(RecipientTypes::INDIVIDUAL)
                ->setStatus(StatusTypes::APPROVE)
                ->setType(PermissionTypes::EMAIL)
    );
```

You can send permissions by filling out the permission list model. The permission list model is the permission model array.

```php
    $permissionList =  PermissionList::make()
        ->addPermission($permission
          ->setConsentDate('2022-02-10 09:50:02')
            ->setRecipient('example1@.com')
            ->setStatus(StatusTypes::APPROVE)
            ->setType(PermissionTypes::EMAIL))
            ->setSource(ConsentSourceTypes::MOBILE)
            ->setRecipientType(RecipientTypes::INDIVIDUAL);
        ->addPermission($permission
            ->setConsentDate('2022-02-10 09:50:03')
            ->setRecipient('example2@.com')
            ->setStatus(StatusTypes::REJECT)
            ->setType(PermissionTypes::MESSAGE));
            ->setSource(ConsentSourceTypes::MOBILE)
            ->setRecipientType(RecipientTypes::INDIVIDUAL);

    IysManager::make()->createPermissionDriver()->sendPermissions($permissionList);
```

You can get send permission information with request id

```php
   IysManager::make()->createPermissionDriver()->getPermissionsStatus('request_id');
```
You can get changed permission by IYS

```php
   IysManager::make()->createPermissionDriver()->getChangedPermissions();
```

You can get permission status by permission model

```php
    IysManager::make()->createPermissionDriver()->getPermissionStatus(
            Permission::make()
                ->setConsentDate('2022-02-10 09:50:02')
                ->setSource(ConsentSourceTypes::MOBILE)
                ->setRecipient('example@.com')
                ->setRecipientType(RecipientTypes::INDIVIDUAL)
                ->setStatus(StatusTypes::APPROVE)
                ->setType(PermissionTypes::EMAIL)
    );
```

## Testing

``` bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.