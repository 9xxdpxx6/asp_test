<?php

namespace Tests\Unit;

use App\Support\HeroSliderOptions;
use PHPUnit\Framework\TestCase;

class HeroSliderOptionsTest extends TestCase
{
    public function test_aspect_uses_chosen_ratio(): void
    {
        $this->assertEqualsWithDelta(16 / 9, HeroSliderOptions::aspect('16:9', 720, 1280), 0.0001);
        $this->assertEqualsWithDelta(4 / 5, HeroSliderOptions::aspect('4:5', 1920, 1080), 0.0001);
    }

    public function test_aspect_auto_follows_video(): void
    {
        $this->assertEqualsWithDelta(720 / 1280, HeroSliderOptions::aspect('auto', 720, 1280), 0.0001);
        $this->assertEqualsWithDelta(9 / 16, HeroSliderOptions::aspect('auto', 0, 0), 0.0001);
    }

    public function test_natural_aspect_ignores_frame_when_video_known(): void
    {
        // Вертикальное видео в рамке 16:9 остаётся вертикальным для решения про «фоном»
        $this->assertEqualsWithDelta(720 / 1280, HeroSliderOptions::naturalAspect(720, 1280, '16:9'), 0.0001);
        $this->assertEqualsWithDelta(16 / 9, HeroSliderOptions::naturalAspect(0, 0, '16:9'), 0.0001);
    }

    public function test_background_is_only_for_landscape_video(): void
    {
        $this->assertSame('right', HeroSliderOptions::normalizeSide('background', 9 / 16));
        $this->assertSame('right', HeroSliderOptions::normalizeSide('background', 1.0));
        $this->assertSame('background', HeroSliderOptions::normalizeSide('background', 16 / 9));
        $this->assertSame('background', HeroSliderOptions::normalizeSide('background', 4 / 3));
    }

    public function test_unknown_side_falls_back_to_right(): void
    {
        $this->assertSame('right', HeroSliderOptions::normalizeSide('top', 16 / 9));
        $this->assertSame('left', HeroSliderOptions::normalizeSide('left', 9 / 16));
    }

    public function test_option_lists_have_expected_keys(): void
    {
        $this->assertArrayHasKey('auto', HeroSliderOptions::ratios());
        $this->assertSame(['cover', 'contain'], array_keys(HeroSliderOptions::fits()));
        $this->assertSame(['right', 'left', 'background'], array_keys(HeroSliderOptions::sides()));
        $this->assertSame(['s', 'm', 'l'], array_keys(HeroSliderOptions::sizes()));
        $this->assertContains(0, HeroSliderOptions::autoplayChoices());
    }
}
