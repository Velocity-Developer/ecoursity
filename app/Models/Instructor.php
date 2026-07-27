<?php

declare(strict_types=1);

namespace Ecoursity\App\Models;

defined('ABSPATH') || exit;

class Instructor extends User
{
    public const ROLE = 'ecoursity_instructor';
}
