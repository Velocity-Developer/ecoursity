<?php

namespace Ecoursity\App\Http\Controllers\Admin;

class StudentController
{
    public function index(): string
    {
        //get all user with role student
        $students = get_users(['role' => 'student']);
        return wp_json_encode($students);
    }
}
