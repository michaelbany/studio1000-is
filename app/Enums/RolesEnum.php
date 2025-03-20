<?php

namespace App\Enums;

enum RolesEnum: string
{
    case ADMIN = 'admin';
    case MODERATOR = 'moderator';
    case GUEST = 'guest';
}