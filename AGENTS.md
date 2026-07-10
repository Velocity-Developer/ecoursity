# AGENTS.md

# Ecoursity Development Guide

## Project Overview

Ecoursity is a modern Learning Management System (LMS) plugin for WordPress.

The architecture prioritizes:

- PSR-4 autoloading
- REST API first
- Alpine.js frontend
- Custom Post Types for content
- Custom Tables for transactional data
- Service-oriented architecture
- Separation of business logic from WordPress APIs

WordPress acts primarily as the CMS and authentication layer, while all user interactions should occur through REST API endpoints.

***

# Tech Stack

Backend

- PHP >= 8.2
- WordPress >= 6.8
- Composer
- PSR-4
- wpdb
- WordPress REST API

Frontend

- Alpine.js
- Fetch API

***

# Coding Standards

- Follow PSR-12.
- Every class must have a single responsibility.
- Avoid global functions whenever possible.
- Avoid static classes except for Helpers.
- Use dependency injection whenever possible.
- Never place business logic inside Controllers.
- Never access `$wpdb` directly from Controllers.
- Never access CPT directly from Alpine components.

***

# Architecture

```
Browser
    │
    ▼
Alpine.js
    │
Fetch API
    │
    ▼
REST Controller
    │
    ▼
Service
    │
    ▼
Repository
    │
    ▼
WordPress
(CPT / wpdb)
```

***

# Directory Structure

```
ecoursity/

├── composer.json
├── ecoursity.php
├── vendor/
│
├── src/
│
│   ├── Core/
│   ├── Providers/
│   ├── Controllers/
│   ├── Services/
│   ├── Repositories/
│   ├── Models/
│   ├── PostTypes/
│   ├── Database/
│   ├── Api/
│   ├── Support/
│   └── Helpers/
│
├── resources/
│   ├── js/
│   ├── css/
│   └── images/
│
├── build/
│
├── templates/
│
└── languages/
```

***

# Layer Responsibilities

## Controller

Responsible for:

- receiving HTTP requests
- validating basic request format
- calling Services
- returning JSON responses

Controllers MUST NOT:

- execute SQL
- call wpdb directly
- contain business logic

***

## Service

Responsible for:

- business rules
- authorization
- workflow
- validation
- transactions

Example:

Enroll Student

```
check login

↓

check course exists

↓

check enrollment

↓

create enrollment

↓

return DTO
```

***

## Repository

Responsible for:

- database access
- WordPress queries
- wpdb
- WP\_Query

Repositories must not contain business logic.

***

## Models

Models represent domain objects.

Examples:

- Course
- Lesson
- Enrollment
- Progress

Models should not know about HTTP.

***

## Providers

Providers register:

- CPT
- REST Routes
- Admin Hooks
- Assets
- Cron
- Shortcodes (if needed)

***

# REST API First

Every frontend interaction should use REST API.

Preferred:

```
GET    /courses

POST   /enrollments

GET    /me/progress
```

Avoid:

- admin-post.php
- AJAX handlers
- form submissions

unless absolutely necessary.

***

# Custom Post Types

Use CPT only for content.

Examples

- Course
- Lesson

Never store transactional data inside CPT.

***

# Custom Tables

Use tables for:

- enrollments
- progress
- quiz\_attempts
- certificates
- orders
- logs
- analytics

***

# Naming

Classes

```
CourseService
LessonRepository
EnrollmentController
ProgressRepository
```

Interfaces

```
CourseRepositoryInterface
```

Tables

```
wp_ecoursity_enrollments

wp_ecoursity_progress

wp_ecoursity_quiz_attempts
```

REST Namespace

```
ecoursity/v1
```

***

# API Response Format

Success

```json
{
    "success": true,
    "message": "Enrollment created.",
    "data": {}
}
```

Error

```json
{
    "success": false,
    "message": "Course not found.",
    "errors": {}
}
```

Always return consistent JSON.

***

# Database Rules

Every custom table should have:

```
id

created_at

updated_at
```

If soft delete is needed:

```
deleted_at
```

Never delete data unless explicitly required.

***

# Security

Always:

- sanitize input
- escape output
- verify capabilities
- verify nonce when required
- use prepare() for SQL
- validate permissions

Never trust client input.

***

# Alpine.js

Frontend must remain lightweight.

Rules:

- No business logic.
- No SQL assumptions.
- No duplicated validation.
- Only consume REST API.

Preferred flow:

```
load()

↓

fetch()

↓

update state

↓

render
```

***

# Dependency Injection

Preferred

```php
class CourseService
{
    public function __construct(
        private CourseRepository $repository
    ) {}
}
```

Avoid creating dependencies inside methods.

***

# Error Handling

Use Exceptions inside Services.

Controllers convert exceptions into JSON responses.

Never expose raw PHP errors.

***

# Future Features

Architecture should support:

- Quiz
- Assignment
- Certificate
- Payment
- Subscription
- Instructor
- Drip Content
- Live Class
- Discussion
- Notifications
- Reporting
- Multi Instructor

without restructuring existing code.

***

# General Principles

- Prefer composition over inheritance.
- Keep classes small.
- Keep methods short.
- Avoid duplicated code.
- Write readable code.
- Favor maintainability over cleverness.
- Business logic belongs in Services.
- Database logic belongs in Repositories.
- Controllers remain thin.
- Frontend communicates exclusively through REST API.

Ecoursity should feel like a modern Laravel-style application running inside WordPress while remaining fully compatible with WordPress standards.
