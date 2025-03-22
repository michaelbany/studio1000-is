<?php

namespace App\Enums;

enum MembersStatusEnum: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case EMPTY = 'empty'; 
}