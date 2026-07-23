<?php

declare(strict_types=1);

namespace Ecoursity\App\Controllers;

use Ecoursity\App\Models\File as FileModel;
use WP_REST_Request;
use WP_REST_Response;

class FileController
{
    public function index(WP_REST_Request $request): WP_REST_Response
    {
        $itemId = absint($request->get_param('item_id'));
        $itemType = sanitize_key((string) $request->get_param('item_type'));

        if ($itemId < 1) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Post wajib dipilih.',
                'data' => [],
            ], 422);
        }

        return new WP_REST_Response([
            'success' => true,
            'data' => array_map(
                fn(FileModel $file): array => $this->transformFile($file),
                FileModel::allByItem($itemId, $itemType)
            ),
        ]);
    }

    public function store(WP_REST_Request $request): WP_REST_Response
    {
        $itemId = absint($request->get_param('item_id'));
        $itemType = sanitize_key((string) $request->get_param('item_type'));
        $method = sanitize_key((string) $request->get_param('method'));
        $orders = absint($request->get_param('orders'));

        if ($itemId < 1 || $itemType === '') {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Post wajib dipilih.',
            ], 422);
        }

        if ($method === FileModel::METHOD_UPLOAD) {
            $fileParams = $request->get_file_params();
            $uploadedFile = $fileParams['file'] ?? null;

            if (! is_array($uploadedFile)) {
                return new WP_REST_Response([
                    'success' => false,
                    'message' => 'File upload wajib diisi.',
                ], 422);
            }

            $file = FileModel::createFromUpload($uploadedFile, $itemId, $itemType, $orders);
            $title = sanitize_text_field((string) $request->get_param('file_name'));

            if ($title !== '') {
                $file->file_name = $title;
                $file->save();
            }

            return new WP_REST_Response([
                'success' => true,
                'message' => 'File berhasil disimpan.',
                'data' => $this->transformFile($file),
            ], 201);
        }

        if ($method !== FileModel::METHOD_EXTERNAL) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Method file tidak valid.',
            ], 422);
        }

        $fileName = sanitize_text_field((string) $request->get_param('file_name'));
        $filePath = esc_url_raw((string) $request->get_param('file_path'));

        if ($fileName === '' || $filePath === '') {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Judul dan URL file wajib diisi.',
            ], 422);
        }

        $file = new FileModel([
            'file_name' => $fileName,
            'file_type' => sanitize_mime_type((string) $request->get_param('file_type')),
            'item_id' => $itemId,
            'item_type' => $itemType,
            'method' => FileModel::METHOD_EXTERNAL,
            'file_path' => $filePath,
            'orders' => $orders,
        ]);
        $file->save();

        return new WP_REST_Response([
            'success' => true,
            'message' => 'File berhasil disimpan.',
            'data' => $this->transformFile($file),
        ], 201);
    }

    public function delete(WP_REST_Request $request): WP_REST_Response
    {
        $file = FileModel::find(absint($request->get_param('id')));

        if (! $file) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'File tidak ditemukan.',
            ], 404);
        }

        $file->delete();

        return new WP_REST_Response([
            'success' => true,
            'message' => 'File berhasil dihapus.',
        ]);
    }

    private function transformFile(FileModel $file): array
    {
        return $file->toArray();
    }
}
