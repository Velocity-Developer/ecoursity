# AGENTS.md

# Ecoursity Development Guide

Ecoursity adalah plugin LMS (Learning Management System) modern untuk WordPress.

Plugin ini dibangun sebagai aplikasi berbasis MVC dengan arsitektur yang terinspirasi Laravel, namun tetap mengikuti lifecycle WordPress.

AI Agent wajib mengikuti seluruh aturan pada dokumen ini.

---

# Philosophy

Ecoursity BUKAN plugin WordPress procedural.

Ecoursity adalah aplikasi MVC yang berjalan di dalam WordPress.

Gunakan prinsip:

- Clean Architecture
- SOLID
- DRY
- Single Responsibility
- Dependency Injection
- Service Layer
- Repository Pattern bila diperlukan

Jangan membuat kode procedural.

---

# Tech Stack

Backend

- PHP 8.2+
- WordPress Latest
- WP REST API
- Composer
- PSR-4 Autoload

Frontend

- AlpineJS
- Vanilla ES Modules
- CSS Custom Properties
- Tanpa jQuery

Database

- wpdb
- Custom Tables jika diperlukan

---

# Folder Structure

Gunakan struktur berikut.

```
ecousity/

│
├── app/
│
│   ├── Controllers/
│   │
│   ├── Models/
│   │
│   ├── Services/
│   │
│   ├── Repositories/
│   │
│   ├── Requests/
│   │
│   ├── Resources/
│   │
│   ├── Middleware/
│   │
│   ├── Policies/
│   │
│   ├── Validators/
│   │
│   ├── Traits/
│   │
│   ├── Helpers/
│   │
│   ├── Providers/
│   │
│   ├── Routes/
│   │
│   └── Core/
│
├── bootstrap/
│
├── config/
│
├── database/
│
│   ├── migrations/
│   ├── seeders/
│   └── factories/
│
├── resources/
│
│   ├── css/
│   ├── js/
│   ├── images/
│   └── views/
│
├── routes/
│
│   └── api.php
│
├── storage/
│
│   ├── cache/
│   ├── logs/
│   └── temp/
│
├── vendor/
│
├── ecousity.php
├── uninstall.php
├── composer.json
└── README.md
```

---

# Architecture

Request Flow

```
Browser

↓

AlpineJS

↓

WP REST API

↓

Route

↓

Controller

↓

Request Validation

↓

Service

↓

Repository

↓

Model

↓

Database
```

Controller tidak boleh berisi business logic.

Semua business logic berada pada Service.

---

# Models

Model hanya bertanggung jawab terhadap data.

Model tidak boleh melakukan:

- rendering
- request
- redirect

Model boleh:

- query database
- relasi
- accessor
- mutator

---

# Controllers

Controller maksimal 100-150 baris.

Controller hanya:

- menerima request
- memanggil validator
- memanggil service
- mengembalikan response

---

# Services

Semua business logic berada di Service.

Contoh:

CourseService

EnrollmentService

QuizService

LessonService

CertificateService

ProgressService

OrderService

StudentService

InstructorService

---

# Repository

Repository digunakan untuk seluruh query kompleks.

Contoh

CourseRepository

LessonRepository

QuizRepository

UserRepository

---

# REST API

Seluruh fitur menggunakan REST API.

Tidak menggunakan admin-ajax.

Namespace

```
ecousity/v1
```

Contoh

GET

```
/courses
```

POST

```
/courses
```

GET

```
/courses/{id}
```

PATCH

```
/courses/{id}
```

DELETE

```
/courses/{id}
```

---

# Validation

Jangan melakukan validasi di Controller.

Gunakan Request Class.

Contoh

```
StoreCourseRequest

UpdateCourseRequest

StoreLessonRequest
```

---

# Response

Gunakan format JSON yang konsisten.

Success

```json
{
    "success": true,
    "message": "Course created.",
    "data": {}
}
```

Error

```json
{
    "success": false,
    "message": "Validation failed.",
    "errors": {}
}
```

---

# AlpineJS

Semua interaksi UI menggunakan AlpineJS.

Tidak menggunakan:

- jQuery
- Vue
- React

Gunakan:

- x-data
- x-model
- x-show
- x-transition
- x-bind
- x-for
- x-effect

Pisahkan state menjadi module kecil.

---

# CSS

Gunakan CSS native.

Gunakan:

- CSS Variables
- Flexbox
- Grid

Jangan menggunakan Bootstrap.

Jangan menggunakan Tailwind.

---

# Views

View hanya berisi HTML.

Business logic dilarang di view.

---

# Security

Seluruh endpoint wajib:

permission_callback

nonce verification

capability check

sanitize

escape

---

# Authentication

Gunakan autentikasi bawaan WordPress.

Jangan membuat sistem login sendiri.

Gunakan:

Current User

Roles

Capabilities

Nonce

---

# Database

Gunakan custom table hanya jika memang diperlukan.

Misalnya:

courses

lessons

enrollments

quiz_attempts

course_progress

certificates

orders

Jangan menyimpan data kompleks pada wp_options.

---

# Naming Convention

Controller

```
CourseController
```

Service

```
CourseService
```

Repository

```
CourseRepository
```

Model

```
Course
```

Request

```
StoreCourseRequest
```

Policy

```
CoursePolicy
```

---

# Coding Standard

PHP

PSR-12

WordPress Coding Standard

Gunakan

typed property

return type

constructor promotion

readonly jika memungkinkan

enum jika diperlukan

---

# Dependency Injection

Selalu gunakan constructor injection.

Contoh

```php
public function __construct(
    CourseService $service
) {}
```

---

# AI Rules

Saat menghasilkan kode:

Jangan membuat kode procedural.

Jangan membuat HTML di Controller.

Jangan membuat SQL di Controller.

Jangan menggunakan admin-ajax.

Gunakan WP REST API.

Gunakan AlpineJS.

Gunakan MVC.

Gunakan Service Layer.

Gunakan Request Validation.

Gunakan Repository bila query mulai kompleks.

Selalu gunakan type declaration.

Selalu gunakan namespace.

Ikuti PSR-4.

Jangan menggunakan Singleton kecuali benar-benar diperlukan.

---

# Code Quality

Kode yang dihasilkan harus:

- Production Ready
- Modular
- Testable
- Maintainable
- SOLID
- DRY
- KISS
- Secure
- Readable