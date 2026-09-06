<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\User\Report;
use App\Models\User\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function submitReport(Request $request)
    {
        $request->validate([
            'reported_user_id' => 'required|exists:users,id',
            'reason'           => 'required|string|min:10',
        ]);

        $reporter = auth('api')->user();

        if (!$reporter) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        if ($reporter->id == $request->reported_user_id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'You cannot report yourself.'
            ], 422);
        }

        $existingReport = Report::where('reporter_id', $reporter->id)
            ->where('reported_user_id', $request->reported_user_id)
            ->exists();

        if ($existingReport) {
            return response()->json([
                'status'  => 'error',
                'message' => 'You have already reported this user.'
            ], 422);
        }

        Report::create([
            'reporter_id'      => $reporter->id,
            'reported_user_id' => $request->reported_user_id,
            'reason'           => $request->reason,
        ]);

        $reportedUser = User::find($request->reported_user_id);

        if (!$reportedUser->is_banned && $reportedUser->reportsReceived()->count() >= 3) {
            $reportedUser->ban("Exceeded maximum allowed reports");
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Report submitted successfully.'
        ], 200);
    }
}
