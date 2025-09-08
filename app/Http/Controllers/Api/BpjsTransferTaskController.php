<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BpjsTransferTask;
use App\Models\MarketingCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BpjsTransferTaskController extends Controller
{
    public function toggle(Request $request)
    {
        \Log::info('BPJS Transfer task toggle request', [
            'method' => $request->method(),
            'all' => $request->all(),
            'input' => $request->input(),
        ]);
        
        try {
            $request->validate([
                'transfer_id' => 'required|exists:bpjs_transfers,id',
                'category_id' => 'required|exists:marketing_categories,id',
                'is_completed' => 'required'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('BPJS Transfer task validation failed', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);
            throw $e;
        }

        try {
            DB::beginTransaction();

            // Convert string to boolean
            $isCompleted = $request->is_completed === '1' || $request->is_completed === 'true' || $request->is_completed === true;
            
            \Log::info('Processing BPJS transfer task', [
                'transfer_id' => $request->transfer_id,
                'category_id' => $request->category_id,
                'is_completed_raw' => $request->is_completed,
                'is_completed_converted' => $isCompleted
            ]);
            
            $task = BpjsTransferTask::where('bpjs_transfer_id', $request->transfer_id)
                ->where('category_id', $request->category_id)
                ->first();

            if (!$task) {
                $task = new BpjsTransferTask();
                $task->bpjs_transfer_id = $request->transfer_id;
                $task->category_id = $request->category_id;
            }

            $task->is_completed = $isCompleted;
            
            if ($isCompleted) {
                $task->completed_by = Auth::id();
                $task->completed_at = now();
            } else {
                $task->completed_by = null;
                $task->completed_at = null;
            }

            $task->save();

            DB::commit();
            
            \Log::info('BPJS Transfer task toggle success', [
                'task_id' => $task->id,
                'transfer_id' => $task->bpjs_transfer_id,
                'category_id' => $task->category_id,
                'is_completed' => $task->is_completed
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diupdate'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            \Log::error('BPJS Transfer task toggle error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}