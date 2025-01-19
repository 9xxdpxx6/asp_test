<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function __invoke()
    {
        return view('main')->with([
            'meta' => [
                'title' => 'Автошкола Политех',
                'description' => 'Научитесь водить с профессионалами! Курсы для начинающих и опытных водителей.',
                'keywords' => 'автошкола, обучение вождению, права категории B, курсы вождения, автошкола краснодар, политех, автошкола политех, права на мотоциклБ права категории A, категоря А, категория Б'
            ]
        ]);
    }
}
