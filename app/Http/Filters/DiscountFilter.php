<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class DiscountFilter extends AbstractFilter
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
                        ->orWhere('slug', 'like', '%' . $word . '%')
                        ->orWhere('description', 'like', '%' . $word . '%');
                });
            }
        });
    }

    protected function sort(Builder $builder, $value)
    {
        switch ($value) {
            case 'date_asc':
                $builder->orderBy('created_at');
                break;
            case 'date_desc':
                $builder->orderBy('created_at', 'desc');
                break;
            case 'percentage_asc':
                $builder->orderBy('percentage');
                break;
            case 'percentage_desc':
                $builder->orderBy('percentage', 'desc');
                break;
            default:
                $builder->orderBy('id', 'desc');
                break;
        }
    }

}
