<?php

namespace Tests\Unit;

use App\Support\Mp4Info;
use PHPUnit\Framework\TestCase;

class Mp4InfoTest extends TestCase
{
    private function fixture(string $name): string
    {
        return __DIR__ . '/../Fixtures/' . $name;
    }

    public function test_reads_faststart_video_with_audio(): void
    {
        $info = Mp4Info::read($this->fixture('video-faststart.mp4'));

        $this->assertNotNull($info);
        $this->assertSame(128, $info['width']);
        $this->assertSame(96, $info['height']);
        $this->assertEqualsWithDelta(1.0, $info['duration'], 0.2);
        $this->assertTrue($info['has_audio']);
        $this->assertTrue($info['faststart']);
    }

    public function test_reads_video_when_moov_is_at_the_end(): void
    {
        $info = Mp4Info::read($this->fixture('video-moov-at-end.mp4'));

        $this->assertNotNull($info);
        $this->assertSame(96, $info['width']);
        $this->assertSame(128, $info['height']);
        $this->assertFalse($info['has_audio']);
        $this->assertFalse($info['faststart']);
    }

    public function test_rejects_non_mp4(): void
    {
        $text = tempnam(sys_get_temp_dir(), 'mp4');
        file_put_contents($text, '<?php echo "not a video";');

        $this->assertNull(Mp4Info::read($text));
        $this->assertNull(Mp4Info::read(__DIR__ . '/../Fixtures/does-not-exist.mp4'));

        unlink($text);
    }

    public function test_rejects_truncated_file(): void
    {
        $data = file_get_contents($this->fixture('video-faststart.mp4'));
        $cut = tempnam(sys_get_temp_dir(), 'mp4');
        file_put_contents($cut, substr($data, 0, 60));

        $this->assertNull(Mp4Info::read($cut));

        unlink($cut);
    }

    public function test_rejects_file_with_ftyp_only(): void
    {
        $data = file_get_contents($this->fixture('video-faststart.mp4'));
        $ftypSize = (int) unpack('N', substr($data, 0, 4))[1];
        $only = tempnam(sys_get_temp_dir(), 'mp4');
        file_put_contents($only, substr($data, 0, $ftypSize));

        $this->assertNull(Mp4Info::read($only));

        unlink($only);
    }
}
