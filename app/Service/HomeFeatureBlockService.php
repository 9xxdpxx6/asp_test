<?php

namespace App\Service;

use App\Models\HomeFeatureBlock;
use App\Models\HomeFeatureSection;
use App\Models\HomeSectionSetting;
use Illuminate\Support\Facades\DB;

class HomeFeatureBlockService
{
    public function syncWhyChooseUs(string $heading, array $blocks): void
    {
        $this->sync(HomeFeatureSection::WHY_CHOOSE_US, $heading, null, $blocks);
    }

    public function syncLearningProcess(string $heading, string $subheading, array $blocks): void
    {
        $this->sync(HomeFeatureSection::LEARNING_PROCESS, $heading, $subheading, $blocks);
    }

    private function sync(string $section, string $heading, ?string $subheading, array $blocks): void
    {
        DB::transaction(function () use ($section, $heading, $subheading, $blocks) {
            HomeSectionSetting::query()->updateOrCreate(
                ['section' => $section],
                [
                    'heading' => $heading,
                    'subheading' => $subheading,
                ]
            );

            HomeFeatureBlock::query()->where('section', $section)->delete();

            foreach (array_values($blocks) as $index => $row) {
                HomeFeatureBlock::query()->create([
                    'section' => $section,
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'icon' => $row['icon'],
                    'sort_order' => $index + 1,
                ]);
            }
        });
    }
}
