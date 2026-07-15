# GET /wp-json/ecoursity/v1/courses/{id}

## Ringkas

Ambil detail course berdasarkan id.

- Lokasi: [ApiRoutes.php](ecoursity/app/Routes/ApiRoutes.php#L60-L65)
- Controller: [CourseController.php](ecoursity/app/Controllers/CourseController.php#L16-L34)
- Permission: public

## Parameter

### Path

- `id` — id post course.

## Response sukses

```json
{
  "success": true,
  "data": {
    "id": 1
  }
}
```

## Response gagal

```json
{
  "success": false,
  "message": "Course not found."
}
```
