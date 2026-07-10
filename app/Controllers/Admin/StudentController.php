<?php

namespace Ecoursity\App\Controllers\Admin;

use Ecoursity\App\Services\TemplateService;
use Ecoursity\App\Models\Student;

class StudentController
{
    public function index()
    {
        $students = Student::all();

        return TemplateService::view('admin/student', compact('students'));
    }
}
