# DELETE /wp-json/ecoursity/v1/lessons/{id}

## Ringkas

Hapus lesson berdasarkan id.

- Lokasi: [ApiRoutes.php](ecoursity/app/Routes/ApiRoutes.php#L108-L113)
- Controller: [LessonController.php](ecoursity/app/Controllers/LessonController.php#L108-L125)
- Permission: perlu `delete_posts`

## Parameter

### Path

- `id` — id post lesson.

## Response sukses

```json
{
  "success": true,
  "message": "Lesson deleted."
}
```
