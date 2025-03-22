<?php

namespace App\Enums;

enum ProjectStatusEnum: string
{
    case DRAFT = 'draft';
    case OPEN = 'open';
    case CLOSED = 'closed';
    case ARCHIVED = 'archived';

    public static function array(): array
    {
        if (request()->user()->role === RolesEnum::ADMIN) {
            return self::cases();
        } else {
            return [
                self::DRAFT,
                self::OPEN,
                self::CLOSED,
            ];
        }

    }
}
