<?php

namespace App\Enums;

enum ProjectRolesEnum: string
{
    case DIRECTOR = 'director';
    case PRODUCER = 'producer';
    case WRITER = 'writer';
    case ACTOR = 'actor';
    case EDITOR = 'editor';
    case SOUND_ENGINEER = 'sound_engineer';
    case CAMERA_OPERATOR = 'camera_operator';
    case LIGHTING_TECHNICIAN = 'lighting_technician';
    case MAKEUP_ARTIST = 'makeup_artist';
    case COSTUME_DESIGNER = 'costume_designer';
    case SET_DESIGNER = 'set_designer';
    case VFX_SUPERVISOR = 'vfx_supervisor';
    
    case OWNER = 'owner';
}