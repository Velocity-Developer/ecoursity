# GET /wp-json/ecoursity/v1/template_component/{component_name}

## Ringkas

Ambil hasil render component template berdasarkan nama component.

- Lokasi: [ApiRoutes.php](ecoursity/app/Routes/ApiRoutes.php#L48-L53)
- Controller: [TemplateController.php](ecoursity/app/Controllers/TemplateController.php)
- Permission: public

## Parameter

### Path

- `component_name` — nama component.

## Response

Response mengikuti output dari controller template dan dipakai untuk render component dari frontend.
