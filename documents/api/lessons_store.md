# POST /wp-json/ecoursity/v1/lessons/

## Ringkas

Buat lesson baru.

- Lokasi: [ApiRoutes.php](ecoursity/app/Routes/ApiRoutes.php#L96-L101)
- Controller: [LessonController.php](ecoursity/app/Controllers/LessonController.php#L37-L64)
- Permission: perlu `edit_posts`

## Body

Field utama:

- `title`
- `content`
- `excerpt`
- `slug`
- `status`
- `duration`
- `preview`
- `assigned`

## Catatan body

- `duration` bisa angka atau array `[amount, unit]`
- `preview` boolean
- `assigned` id course

## Response sukses

```json
{
  "success": true,
  "message": "Lesson created.",
  "data": {
    "id": 1
  }
}
```
