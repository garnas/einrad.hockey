<?php

// Override move_uploaded_file in the FileService namespace so tests can simulate
// successful uploads without an actual HTTP upload context.

namespace App\Service\Neuigkeit {
    function move_uploaded_file(string $from, string $to): bool
    {
        return \copy($from, $to);
    }
}

namespace integration {

    use App\Service\Neuigkeit\FileService;
    use PHPUnit\Framework\TestCase;

    class FileServiceTest extends TestCase
    {
        private string $targetDir;

        protected function setUp(): void
        {
            $this->targetDir = sys_get_temp_dir() . '/fileservice_test_' . uniqid() . '/';
            mkdir($this->targetDir);
        }

        protected function tearDown(): void
        {
            foreach (glob($this->targetDir . '*') as $file) {
                unlink($file);
            }
            rmdir($this->targetDir);
        }

        public function testUploadPDFReturnsFalseWhenFileIsTooLarge(): void
        {
            $tmpFile = tempnam(sys_get_temp_dir(), 'pdf_test_');
            file_put_contents($tmpFile, str_repeat('a', 100));

            $file = [
                'tmp_name' => $tmpFile,
                'name' => 'document.pdf',
                'size' => 3_100_001,
            ];

            $result = FileService::uploadPDF($file, $this->targetDir);

            unlink($tmpFile);
            $this->assertFalse($result);
        }

        public function testUploadPDFReturnsFalseForInvalidFileType(): void
        {
            $tmpFile = tempnam(sys_get_temp_dir(), 'pdf_test_');
            file_put_contents($tmpFile, 'some content');

            $file = [
                'tmp_name' => $tmpFile,
                'name' => 'document.txt',
                'size' => 1_000,
            ];

            $result = FileService::uploadPDF($file, $this->targetDir);

            unlink($tmpFile);
            $this->assertFalse($result);
        }

        public function testUploadImageReturnsFalseWhenFileIsTooLarge(): void
        {
            // Size is checked before file_exists in check_error_image, so no real file is needed.
            $file = [
                'tmp_name' => '',
                'name' => 'photo.jpg',
                'size' => 12_582_913,
            ];

            $result = FileService::uploadImage($file, $this->targetDir);

            $this->assertFalse($result);
        }

        public function testUploadImageSucceedsWithValidJPEGFile(): void
        {
            $tmpFile = tempnam(sys_get_temp_dir(), 'img_test_');
            $image = imagecreatetruecolor(100, 100);
            imagejpeg($image, $tmpFile);
            $image = null;

            $file = [
                'tmp_name' => $tmpFile,
                'name' => 'photo.jpg',
                'size' => filesize($tmpFile),
            ];

            $result = FileService::uploadImage($file, $this->targetDir);

            unlink($tmpFile);
            $this->assertIsString($result);
            $this->assertFileExists($result);
        }
    }
}
