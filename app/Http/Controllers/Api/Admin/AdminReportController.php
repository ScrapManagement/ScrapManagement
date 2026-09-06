<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User\Report;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with(['reporter:id,name', 'reportedUser:id,name']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $reports
        ]);
    }

    public function changeStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewed,confirmed'
        ]);

        $report = Report::findOrFail($id);
        $report->status = $request->status;
        $report->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Report status updated successfully.',
            'data' => $report
        ]);
    }
}

