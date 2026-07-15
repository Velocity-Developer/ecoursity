# PUT /wp-json/ecoursity/v1/lessons/{id}

## Ringkas

Update lesson berdasarkan id.

- Lokasi: [ApiRoutes.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/app/Routes/ApiRoutes.php#L102-L107)
- Controller: [LessonController.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/app/Controllers/LessonController.php#L66-L106)
- Permission: perlu `edit_posts`

## Parameter

### Path

- `id` — id post lesson.

## Body

Mendukung field yang sama dengan endpoint create. Hanya field yang dikirim yang diubah.

## Response sukses

```json
{
  "success": true,
  "message": "Lesson updated.",
  "data": {
    "id": 1
  }
}
```
