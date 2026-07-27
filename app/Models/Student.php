<?php

declare(strict_types=1);

namespace Ecoursity\App\Models;

defined('ABSPATH') || exit;

class Student extends User
{
    public const ROLE = 'ecoursity_student';
}
