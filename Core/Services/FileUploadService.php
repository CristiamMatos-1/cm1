<?php

namespace Core\Services;

use Config\App;

/**
 * Upload de arquivos de suporte. Arquivos ficam fora da webroot (storage/downloads).
 */
class FileUploadService
{
    public const MAX_BYTES = 100 * 1024 * 1024;

    /** @var list<string> */
    public const ALLOWED_EXTENSIONS = [
        'zip', 'rar', '7z', 'gz', 'tar', 'pdf', 'txt',
        'exe', 'msi', 'apk', 'dmg', 'pkg',
        'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
        'png', 'jpg', 'jpeg', 'webp',
    ];

    private string $storageDir;

    public function __construct(?string $storageDir = null)
    {
        $this->storageDir = $storageDir ?? (App::rootPath() . '/storage/downloads/');
        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0750, true);
        }
    }

    /**
     * @return array{stored_name: string, original_name: string, mime_type: string, file_size: int}|false
     */
    public function upload(array $file): array|false
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            $_SESSION['error_msg'] = 'Envio de arquivo inválido.';
            return false;
        }

        if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
            $_SESSION['error_msg'] = 'O arquivo excede o limite de upload do servidor (upload_max_filesize).';
            return false;
        }

        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['error_msg'] = 'Selecione um arquivo para enviar.';
            return false;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error_msg'] = 'Falha no envio do arquivo. Código: ' . $file['error'];
            return false;
        }

        if (($file['size'] ?? 0) > self::MAX_BYTES) {
            $_SESSION['error_msg'] = 'O arquivo excede o limite de 100 MB.';
            return false;
        }

        $original = $this->safeOriginalName((string) ($file['name'] ?? 'arquivo'));
        $extension = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        if (!self::isAllowedExtension($extension)) {
            $_SESSION['error_msg'] = 'Tipo de arquivo não permitido. Use ZIP, PDF, EXE, MSI ou similar.';
            return false;
        }

        $tmp = (string) ($file['tmp_name'] ?? '');
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            $_SESSION['error_msg'] = 'Arquivo temporário inválido.';
            return false;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmp) ?: 'application/octet-stream';
        if ($this->isDangerousMime($mime)) {
            $_SESSION['error_msg'] = 'O tipo do arquivo não é permitido.';
            return false;
        }

        $storedName = sprintf('dl_%s_%s.%s', date('YmdHis'), bin2hex(random_bytes(8)), $extension);
        $destination = $this->storageDir . $storedName;

        if (!move_uploaded_file($tmp, $destination)) {
            $_SESSION['error_msg'] = 'Não foi possível gravar o arquivo.';
            return false;
        }

        @chmod($destination, 0640);

        return [
            'stored_name' => $storedName,
            'original_name' => $original,
            'mime_type' => $mime,
            'file_size' => (int) $file['size'],
        ];
    }

    public function delete(?string $storedName): void
    {
        $path = $this->absolutePath($storedName);
        if ($path !== null && is_file($path)) {
            @unlink($path);
        }
    }

    public function absolutePath(?string $storedName): ?string
    {
        if ($storedName === null || $storedName === '') {
            return null;
        }
        if (preg_match('/^dl_[A-Za-z0-9._-]+$/', $storedName) !== 1) {
            return null;
        }

        $base = realpath(rtrim($this->storageDir, '/'));
        if ($base === false) {
            return null;
        }

        $full = realpath($base . DIRECTORY_SEPARATOR . $storedName);
        if ($full === false || !str_starts_with($full, $base . DIRECTORY_SEPARATOR)) {
            return null;
        }

        return $full;
    }

    public static function isAllowedExtension(string $extension): bool
    {
        return in_array(strtolower($extension), self::ALLOWED_EXTENSIONS, true);
    }

    private function safeOriginalName(string $name): string
    {
        $name = basename(str_replace(["\0", '\\', '/'], '', $name));
        $name = trim($name);
        if ($name === '' || $name === '.' || $name === '..') {
            return 'arquivo.bin';
        }
        return mb_substr($name, 0, 180);
    }

    private function isDangerousMime(string $mime): bool
    {
        $mime = strtolower($mime);
        return str_contains($mime, 'php')
            || str_contains($mime, 'javascript')
            || $mime === 'text/html'
            || $mime === 'image/svg+xml'
            || $mime === 'application/x-httpd-php';
    }
}
