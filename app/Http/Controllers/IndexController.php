<?php

namespace App\Http\Controllers;

use App\Http\Resources\Contact\ContactPagePublicResource;
use App\Http\Resources\Hero\HeroPublicResource;
use App\Models\ContactPageSetting;
use App\Models\HeroSetting;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function __invoke(Request $request)
    {
        $initialHero = null;
        if (trim($request->path(), '/') === '') {
            $hero = HeroSetting::query()->first();
            if ($hero !== null) {
                $initialHero = (new HeroPublicResource($hero))->resolve();
            }
        }

        $initialContact = null;
        if (trim($request->path(), '/') === 'contacts') {
            $contact = ContactPageSetting::query()->first();
            if ($contact !== null) {
                $initialContact = (new ContactPagePublicResource($contact))->resolve();
            }
        }

        return view('main')->with([
            'meta' => [
                'title' => 'Автошкола Политех',
                'description' => 'Научитесь водить с профессионалами! Курсы для начинающих и опытных водителей.',
                'keywords' => 'автошкола, обучение вождению, права категории B, курсы вождения, автошкола краснодар, политех, автошкола политех, права на мотоциклБ права категории A, категоря А, категория Б'
            ],
            'initialHero' => $initialHero,
            'initialContact' => $initialContact,
        ]);
    }
}
