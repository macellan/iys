<?php

namespace Macellan\Iys\Drivers\Permission\Enums;

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
