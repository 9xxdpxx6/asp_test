<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class CategoryFilter extends AbstractFilter
{
    const KEYWORD = 'keyword';
    const SORT = 'sort';

    protected function getCallbacks(): array
    {
        return [
            self::KEYWORD => [$this, 'keyword'],
            self::SORT => [$this, 'sort'],
        ];
    }

    protected function keyword(Builder $builder, $value)
    {
        $words = explode(' ', $value);

        $builder->where(function ($query) use ($words) {
            foreach ($words as $word) {
                $query->where(function ($query) use ($word) {
                    $query->where('name', 'like', '%' . $word . '%')
                        ->orWhere('duration', 'like', '%' . $word . '%');
                });
            }
        });
    }

    protected function sort(Builder $builder, $value)
    {
        switch ($value) {
            case 'price_asc':
                $builder->orderBy('price');
                break;
            case 'price_desc':
                $builder->orderBy('price', 'desc');
                break;
            case 'date_asc':
                $builder->orderBy('created_at');

                break;
            case 'date_desc':
                $builder->orderBy('created_at', 'desc');

                break;
            default:
                $builder->orderBy('id', 'asc');
                break;
        }
    }
}
