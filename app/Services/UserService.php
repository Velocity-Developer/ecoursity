<?php

declare(strict_types=1);

namespace Ecoursity\App\Services;

use InvalidArgumentException;
use RuntimeException;
use WP_User;

defined('ABSPATH') || exit;

class UserService
{
    public const AVATAR_META_KEY = '_ecoursity_avatar';
    private const MAX_AVATAR_SIZE = 2097152;

    public function __construct(
        private readonly UploadService $uploadService = new UploadService()
    ) {
    }

    public function upload_avatar(int $userId, array $file): array
    {
        $user = $this->resolveUser($userId);
        $this->validateAvatarFile($file);

        $previousAvatar = $this->storedAvatarPath((int) $user->ID);
        $uploaded = $this->uploadService->uploadToSubfolder(
            $file,
            UploadService::PROFILE_DIRECTORY . '/' . (int) $user->ID
        );

        update_user_meta((int) $user->ID, self::AVATAR_META_KEY, (string) $uploaded['path']);

        if ($previousAvatar !== '' && $previousAvatar !== (string) $uploaded['path']) {
            $this->uploadService->delete($previousAvatar);
        }

        return $this->avatarResponse((int) $user->ID, $uploaded, true);
    }

    public function uploadAvatar(int $userId, array $file): array
    {
        return $this->upload_avatar($userId, $file);
    }

    public function get_avatar(int $userId, int $size = 96): array
    {
        $user = $this->resolveUser($userId);
        $avatarPath = $this->storedAvatarPath((int) $user->ID);

        if ($avatarPath !== '') {
            $avatar = $this->uploadService->get($avatarPath);

            if (is_array($avatar)) {
                return $this->avatarResponse((int) $user->ID, $avatar, true);
            }

            delete_user_meta((int) $user->ID, self::AVATAR_META_KEY);
        }

        return [
            'user_id' => (int) $user->ID,
            'is_custom' => false,
            'name' => '',
            'path' => '',
            'full_path' => '',
            'url' => get_avatar_url((int) $user->ID, ['size' => max(1, absint($size))]) ?: '',
            'mime_type' => '',
            'size' => 0,
        ];
    }

    public function getAvatar(int $userId, int $size = 96): array
    {
        return $this->get_avatar($userId, $size);
    }

    private function resolveUser(int $userId): WP_User
    {
        $user = get_user_by('id', absint($userId));

        if (!$user instanceof WP_User || !$user->exists()) {
            throw new InvalidArgumentException('User not found.');
        }

        return $user;
    }

    private function validateAvatarFile(array $file): void
    {
        if (! empty($file['size']) && (int) $file['size'] > self::MAX_AVATAR_SIZE) {
            throw new InvalidArgumentException('Avatar file may not be larger than 2 MB.');
        }

        $filename = (string) ($file['name'] ?? '');
        $temporaryPath = (string) ($file['tmp_name'] ?? '');

        if ($filename === '' || $temporaryPath === '') {
            throw new InvalidArgumentException('Avatar file is required.');
        }

        $allowedTypes = [
            'jpg|jpeg|jpe' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
        ];

        $fileType = wp_check_filetype_and_ext($temporaryPath, $filename, $allowedTypes);
        $mimeType = (string) ($fileType['type'] ?? '');

        if (! in_array($mimeType, $allowedTypes, true)) {
            throw new InvalidArgumentException('Avatar must be a JPG, PNG, GIF, or WebP image.');
        }
    }

    private function storedAvatarPath(int $userId): string
    {
        return sanitize_text_field((string) get_user_meta($userId, self::AVATAR_META_KEY, true));
    }

    private function avatarResponse(int $userId, array $avatar, bool $isCustom): array
    {
        return [
            'user_id' => $userId,
            'is_custom' => $isCustom,
            'name' => (string) ($avatar['name'] ?? ''),
            'path' => (string) ($avatar['path'] ?? ''),
            'full_path' => (string) ($avatar['full_path'] ?? ''),
            'url' => (string) ($avatar['url'] ?? ''),
            'mime_type' => (string) ($avatar['mime_type'] ?? ''),
            'size' => (int) ($avatar['size'] ?? 0),
        ];
    }
}
