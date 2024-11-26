<?php

namespace App\Http\Filters;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;

class CallbackRequestFilter extends AbstractFilter
{
    const KEYWORD = 'keyword';
    const SORT = 'sort';
    const STATUS = 'status';

    protected function getCallbacks(): array
    {
        return [
            self::KEYWORD => [$this, 'keyword'],
            self::SORT => [$this, 'sort'],
            self::STATUS => [$this, 'status'],
        ];
    }

    /**
     * Фильтр по ключевым словам
     */
    protected function keyword(Builder $builder, $value)
    {
        $words = explode(' ', $value);

        $builder->where(function ($query) use ($words) {
            foreach ($words as $word) {
                $query->where(function ($query) use ($word) {
                    $query->where('full_name', 'like', '%' . $word . '%')
                        ->orWhere('phone', 'like', '%' . $word . '%')
                        ->orWhere('email', 'like', '%' . $word . '%')
                        ->orWhere('comment', 'like', '%' . $word . '%');
                });
            }
        });
    }

    /**
     * Сортировка по значениям
     */

    /**
     * Сортировка по дате
     */
    protected function sort(Builder $builder, $value)
    {
        switch ($value) {
            case 'date_asc':
                $builder->orderBy('created_at');
                break;
            case 'date_desc':
                $builder->orderBy('created_at', 'desc');
                break;
            default:
                $builder->orderBy('id', 'desc'); // По умолчанию по id
                break;
        }
    }

    /**
     * Фильтрация по статусу
     */
    protected function status(Builder $builder, $value)
    {
        if ($value) {
            $builder->where('status_id', $value);
        }
    }
}
