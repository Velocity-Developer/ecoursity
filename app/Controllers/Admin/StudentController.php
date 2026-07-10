<?php

namespace Ecoursity\App\Controllers\Admin;

use Ecoursity\App\Template;
use Ecoursity\App\Models\Student;

class StudentController
{
    public function index()
    {
        $students = Student::all();

        return Template::view('pages/admin/student', compact('students'));
    }
}
