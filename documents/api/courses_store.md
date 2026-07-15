# POST /wp-json/ecoursity/v1/courses/

## Ringkas

Buat course baru.

- Lokasi: [ApiRoutes.php](ecoursity/app/Routes/ApiRoutes.php#L66-L71)
- Controller: [CourseController.php](ecoursity/app/Controllers/CourseController.php#L36-L64)
- Permission: public

## Body

Field utama:

- `title`
- `content`
- `excerpt`
- `slug`
- `status`
- `duration`
- `level`
- `max_students`
- `price`
- `price_sale`
- `price_sale_start`
- `price_sale_end`
- `course_evaluation`
- `passing_grade`
- `thumbnail_id`
- `course_category_ids`
- `course_tags` atau `course_tags_input`

## Response sukses

```json
{
  "success": true,
  "message": "Course created.",
  "data": {
    "id": 1
  }
}
```
