<?php

namespace Ecoursity\App\Http\Controllers\Admin;

use Ecoursity\App\Helpers\LayoutHelper;

class StudentController
{
    public function index(): void
    {
        //get all user with role student
        $students = get_users(['role' => 'student']);
        LayoutHelper::view('admin.student', compact('students'));
    }
}
