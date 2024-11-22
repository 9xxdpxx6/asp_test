<?php

namespace App\Service;

use App\Models\CallbackRequest;
use Illuminate\Support\Facades\DB;

class CallbackRequestService
{
    public function update($data, CallbackRequest $callback)
    {
        try {
            DB::beginTransaction();
            $callback->update($data);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            abort(500);
        }
        return $callback;
    }

    public function delete(CallbackRequest $callback)
    {
        try {
            DB::beginTransaction();
            $callback->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }
}
