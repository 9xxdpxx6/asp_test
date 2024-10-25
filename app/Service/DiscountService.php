<?php

namespace App\Service;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;

class DiscountService
{
    public function store($data)
    {
        $category = new Category();

        try {
            DB::beginTransaction();

            $category = Category::create($data);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }

    public function update($data,Category $category)
    {

        try {
            DB::beginTransaction();

            $category = Category::update($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }

    public function delete($data)
    {
        try {
            DB::beginTransaction();
            $data->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }
}
