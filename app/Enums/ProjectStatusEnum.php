<?php

namespace App\Enums;

enum ProjectStatusEnum: string
{
    case DRAFT = 'draft';
    case OPEN = 'open';
    case CLOSED = 'closed';
    case ARCHIVED = 'archived';
}
