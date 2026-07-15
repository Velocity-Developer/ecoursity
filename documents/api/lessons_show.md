# GET /wp-json/ecoursity/v1/lessons/{id}

## Ringkas

Ambil detail lesson berdasarkan id.

- Lokasi: [ApiRoutes.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/app/Routes/ApiRoutes.php#L90-L95)
- Controller: [LessonController.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/app/Controllers/LessonController.php#L18-L35)
- Permission: public

## Parameter

### Path

- `id` — id post lesson.

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
  "message": "Lesson not found."
}
```
