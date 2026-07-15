# PUT /wp-json/ecoursity/v1/courses/{id}

## Ringkas

Update course berdasarkan id.

- Lokasi: [ApiRoutes.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/app/Routes/ApiRoutes.php#L72-L77)
- Controller: [CourseController.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/app/Controllers/CourseController.php#L66-L93)
- Permission: public

## Parameter

### Path

- `id` — id post course.

## Body

Mendukung field yang sama dengan endpoint create. Hanya field yang dikirim yang diubah.

## Response sukses

```json
{
  "success": true,
  "message": "Course updated.",
  "data": {
    "id": 1
  }
}
```
