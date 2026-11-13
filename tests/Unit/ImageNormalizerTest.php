<?php

namespace Tests\Unit;

use App\Support\ImageNormalizer;
use PHPUnit\Framework\TestCase;

class ImageNormalizerTest extends TestCase
{
    private array $cleanup = [];

    protected function tearDown(): void
    {
        foreach ($this->cleanup as $file) {
            @unlink($file);
        }
        parent::tearDown();
    }

    private function tmp(string $ext): string
    {
        $path = tempnam(sys_get_temp_dir(), 'img') . '.' . $ext;
        $this->cleanup[] = $path;

        return $path;
    }

    private function makeJpeg(int $w, int $h): string
    {
        $image = imagecreatetruecolor($w, $h);
        imagefilledrectangle($image, 0, 0, $w, $h, imagecolorallocate($image, 30, 120, 220));
        $path = $this->tmp('jpg');
        imagejpeg($image, $path, 90);
        imagedestroy($image);

        return $path;
    }

    public function test_large_photo_is_scaled_down_keeping_ratio(): void
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped('GD не установлен');
        }

        $src = $this->makeJpeg(3200, 2400);
        $dest = $this->tmp('jpg');

        $result = ImageNormalizer::normalize($src, $dest, 2560);

        $this->assertSame(2560, $result['width']);
        $this->assertSame(1920, $result['height']);
        $this->assertSame('jpg', $result['ext']);
        $this->assertSame([2560, 1920], array_slice(getimagesize($dest), 0, 2));
    }

    public function test_small_photo_is_kept_as_is(): void
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped('GD не установлен');
        }

        $src = $this->makeJpeg(800, 600);
        $dest = $this->tmp('jpg');

        $result = ImageNormalizer::normalize($src, $dest, 2560);

        $this->assertSame(800, $result['width']);
        $this->assertSame(600, $result['height']);
        $this->assertSame(filesize($src), filesize($dest));
    }

    public function test_png_stays_png(): void
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped('GD не установлен');
        }

        $image = imagecreatetruecolor(3000, 1000);
        imagesavealpha($image, true);
        $src = $this->tmp('png');
        imagepng($image, $src);
        imagedestroy($image);
        $dest = $this->tmp('png');

        $result = ImageNormalizer::normalize($src, $dest, 1500);

        $this->assertSame('png', $result['ext']);
        $this->assertSame(1500, $result['width']);
        $this->assertSame(500, $result['height']);
        $this->assertSame('image/png', getimagesize($dest)['mime']);
    }

    public function test_rejects_non_images(): void
    {
        $src = $this->tmp('jpg');
        file_put_contents($src, '<?php echo 1;');

        $this->assertNull(ImageNormalizer::inspect($src));
        $this->expectException(\InvalidArgumentException::class);
        ImageNormalizer::normalize($src, $this->tmp('jpg'), 2560);
    }
}
