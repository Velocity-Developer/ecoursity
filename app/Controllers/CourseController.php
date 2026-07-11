<?php

namespace Ecoursity\App\Controllers;

use Ecoursity\App\Models\Course;

class CourseController
{
    public function index(): array
    {
        $courses = Course::all();

        return $courses;
    }
}
