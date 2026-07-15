# GET /wp-json/ecoursity/v1/template_view/{template}

## Ringkas

Ambil hasil render template berdasarkan id template.

- Lokasi: [ApiRoutes.php](ecoursity/app/Routes/ApiRoutes.php#L42-L47)
- Controller: [TemplateController.php](ecoursity/app/Controllers/TemplateController.php)
- Permission: public

## Parameter

### Path

- `template` — id template.

## Response

Response mengikuti output dari controller template dan dipakai untuk render template dari frontend.
