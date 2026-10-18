<?php

namespace Core\Services;

use Config\App;

/**
 * Upload de MP4 para a home. Separado do ImageUploadService (SRP).
 */
class VideoUploadService
{
    private string $uploadDir;
    private int $maxFileSize = 50 * 1024 * 1024;

    public function __construct()
    {
        $this->uploadDir = App::rootPath() . '/Public/uploads/videos/';
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function upload(array $file): string|false
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            return false;
        }

        if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
            $_SESSION['settings_error'] = 'O MP4 excede o limite de upload do servidor (upload_max_filesize).';
            return false;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['settings_error'] = 'Falha no envio do vídeo. Código: ' . $file['error'];
            return false;
        }

        if (($file['size'] ?? 0) > $this->maxFileSize) {
            $_SESSION['settings_error'] = 'O vídeo excede o limite de 50 MB.';
            return false;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if ($mime !== 'video/mp4') {
            $_SESSION['settings_error'] = 'Envie apenas arquivo MP4.';
            return false;
        }

        $safeName = sprintf('home_%s_%s.mp4', date('YmdHis'), bin2hex(random_bytes(6)));
        $destination = $this->uploadDir . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $_SESSION['settings_error'] = 'Não foi possível gravar o arquivo de vídeo.';
            return false;
        }

        return '/uploads/videos/' . $safeName;
    }

    public function delete(?string $relativePath): void
    {
        if ($relativePath === null || $relativePath === '') {
            return;
        }

        $relativePath = '/' . ltrim(str_replace('\\', '/', $relativePath), '/');
        if (!str_starts_with($relativePath, '/uploads/videos/') || str_contains($relativePath, '..')) {
            return;
        }

        $full = App::rootPath() . '/Public' . $relativePath;
        if (is_file($full)) {
            @unlink($full);
        }
    }
}
