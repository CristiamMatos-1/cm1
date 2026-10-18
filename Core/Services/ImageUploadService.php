<?php

namespace Core\Services;

class ImageUploadService
{
    private string $uploadDir;
    private array $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
    private int $maxFileSize = 10 * 1024 * 1024; // 10MB
    
    // Dimensões padrão para resize
    private int $targetWidth = 800;
    private int $targetHeight = 600;

    public function __construct(string $uploadDir = '/uploads/articles/')
    {
        $this->uploadDir = dirname(dirname(__DIR__)) . '/Public' . $uploadDir;
        
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    /**
     * Processa o upload de uma imagem
     * @return string|false Caminho relativo da imagem ou false em caso de erro
     */
    public function upload(array $file)
    {
        if (!isset($file['error']) || is_array($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        if ($file['size'] > $this->maxFileSize) {
            $_SESSION['error_msg'] = 'O arquivo excede o limite de 10MB.';
            return false;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        if (!in_array($mime, $this->allowedMimeTypes, true)) {
            $_SESSION['error_msg'] = 'Formato de imagem inválido. Apenas JPG, PNG e WEBP são permitidos.';
            return false;
        }

        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };

        // Sanitização extrema do nome do arquivo
        $safeName = sprintf('%s_%s.%s', date('YmdHis'), bin2hex(random_bytes(8)), $ext);
        $destination = $this->uploadDir . $safeName;

        if ($this->resizeAndCropImage($file['tmp_name'], $destination, $mime)) {
            // Retorna o caminho relativo para salvar no banco
            return '/uploads/articles/' . $safeName;
        } else {
            $_SESSION['error_msg'] = 'Falha ao processar e redimensionar a imagem. Verifique se a extensão GD do PHP está ativa no cPanel.';
        }

        return false;
    }

    /**
     * Processa o upload de um logo (sem crop/resize)
     * @return string|false Caminho relativo da imagem ou false em caso de erro
     */
    public function uploadLogo(array $file)
    {
        if (!isset($file['error']) || is_array($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        if ($file['size'] > $this->maxFileSize) {
            $_SESSION['error_msg'] = 'O arquivo excede o limite de 10MB.';
            return false;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        if (!in_array($mime, $this->allowedMimeTypes, true)) {
            $_SESSION['error_msg'] = 'Formato de imagem inválido. Apenas JPG, PNG e WEBP são permitidos.';
            return false;
        }

        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };

        $safeName = sprintf('logo_%s_%s.%s', date('YmdHis'), bin2hex(random_bytes(4)), $ext);
        $destination = dirname(dirname(__DIR__)) . '/Public/uploads/' . $safeName;
        
        // Garante que a pasta uploads existe
        if (!is_dir(dirname($destination))) {
            mkdir(dirname($destination), 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return '/uploads/' . $safeName;
        }

        return false;
    }

    /**
     * Banner da home (recorte largo). Caminho relativo em /uploads/banners/.
     */
    public function uploadBanner(array $file): string|false
    {
        $previousDir = $this->uploadDir;
        $previousWidth = $this->targetWidth;
        $previousHeight = $this->targetHeight;

        $this->uploadDir = dirname(dirname(__DIR__)) . '/Public/uploads/banners/';
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
        $this->targetWidth = 1600;
        $this->targetHeight = 560;

        $path = $this->storeValidatedImage($file, 'banner_', '/uploads/banners/');

        $this->uploadDir = $previousDir;
        $this->targetWidth = $previousWidth;
        $this->targetHeight = $previousHeight;

        return $path;
    }

    public function deleteBanner(?string $relativePath): void
    {
        if ($relativePath === null || $relativePath === '') {
            return;
        }
        $relativePath = '/' . ltrim(str_replace('\\', '/', $relativePath), '/');
        if (!str_starts_with($relativePath, '/uploads/banners/') || str_contains($relativePath, '..')) {
            return;
        }
        $full = dirname(dirname(__DIR__)) . '/Public' . $relativePath;
        if (is_file($full)) {
            @unlink($full);
        }
    }

    private function storeValidatedImage(array $file, string $prefix, string $publicDir): string|false
    {
        if (!isset($file['error']) || is_array($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            if (($file['error'] ?? null) === UPLOAD_ERR_INI_SIZE || ($file['error'] ?? null) === UPLOAD_ERR_FORM_SIZE) {
                $_SESSION['error_msg'] = 'A imagem excede o limite de upload do servidor.';
            }
            return false;
        }

        if ($file['size'] > $this->maxFileSize) {
            $_SESSION['error_msg'] = 'O arquivo excede o limite de 10MB.';
            return false;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        if (!in_array($mime, $this->allowedMimeTypes, true)) {
            $_SESSION['error_msg'] = 'Formato de imagem inválido. Apenas JPG, PNG e WEBP são permitidos.';
            return false;
        }

        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };

        $safeName = sprintf('%s%s_%s.%s', $prefix, date('YmdHis'), bin2hex(random_bytes(6)), $ext);
        $destination = $this->uploadDir . $safeName;

        if ($this->resizeAndCropImage($file['tmp_name'], $destination, $mime)) {
            return rtrim($publicDir, '/') . '/' . $safeName;
        }

        $_SESSION['error_msg'] = 'Falha ao processar a imagem. Verifique se a extensão GD do PHP está ativa no cPanel.';
        return false;
    }

    /**
     * Redimensiona e corta a imagem para manter o tamanho padrão (crop center)
     */
    private function resizeAndCropImage(string $sourcePath, string $destinationPath, string $mime): bool
    {
        if (!function_exists('imagecreatefromjpeg')) {
            // Se a extensão GD não estiver instalada, apenas move o arquivo original (fallback)
            return move_uploaded_file($sourcePath, $destinationPath);
        }

        // Cria a imagem na memória baseada no tipo
        $sourceImage = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($sourcePath),
            'image/png' => @imagecreatefrompng($sourcePath),
            'image/webp' => @imagecreatefromwebp($sourcePath),
            default => false
        };

        if (!$sourceImage) {
            return false;
        }

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);

        // Calcula a proporção
        $sourceRatio = $sourceWidth / $sourceHeight;
        $targetRatio = $this->targetWidth / $this->targetHeight;

        $cropWidth = $sourceWidth;
        $cropHeight = $sourceHeight;
        $cropX = 0;
        $cropY = 0;

        if ($sourceRatio > $targetRatio) {
            // Imagem é mais larga, corta nas laterais
            $cropWidth = (int)($sourceHeight * $targetRatio);
            $cropX = (int)(($sourceWidth - $cropWidth) / 2);
        } elseif ($sourceRatio < $targetRatio) {
            // Imagem é mais alta, corta em cima e embaixo
            $cropHeight = (int)($sourceWidth / $targetRatio);
            $cropY = (int)(($sourceHeight - $cropHeight) / 2);
        }

        $targetImage = imagecreatetruecolor($this->targetWidth, $this->targetHeight);

        // Preserva transparência para PNG e WEBP
        if ($mime === 'image/png' || $mime === 'image/webp') {
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
            $transparent = imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
            imagefilledrectangle($targetImage, 0, 0, $this->targetWidth, $this->targetHeight, $transparent);
        }

        // Redimensiona e corta
        imagecopyresampled(
            $targetImage, $sourceImage,
            0, 0, $cropX, $cropY,
            $this->targetWidth, $this->targetHeight,
            $cropWidth, $cropHeight
        );

        // Salva a nova imagem
        $success = match ($mime) {
            'image/jpeg' => imagejpeg($targetImage, $destinationPath, 85),
            'image/png' => imagepng($targetImage, $destinationPath, 8),
            'image/webp' => imagewebp($targetImage, $destinationPath, 85),
            default => false
        };

        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        return $success;
    }
}
