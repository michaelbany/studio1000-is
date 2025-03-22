<?php

namespace App\Enums;

enum MembersStatusEnum: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case EMPTY = 'empty'; 

    public static function array(): array
    {
        return [
            self::PENDING,
            self::APPROVED,
            self::REJECTED,
        ];
    }
}