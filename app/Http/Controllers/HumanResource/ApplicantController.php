<?php

namespace App\Http\Controllers\HumanResource;

use App\Http\Controllers\Controller;
use App\Models\HumanResource\Applicant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\HumanResource\Interview;

class ApplicantController extends Controller
{
    public function __invoke(Request $request)
    {
        $applicants = Applicant::with(['position'])->where('status', 'Pending')->latest()->paginate(20);
        if($request->has('search') && $request->search != null){
            $applicants = Applicant::where('first_name', 'like', "%{$request->search}%")
                ->orWhere('last_name', 'like', "%{$request->search}%")
                ->where('status', 'Pending')
                ->orderBy('created_at')
                ->paginate(10);
        }
        return View::make('layouts.hr.applicant', [
            'applicants' => $applicants,
            'acceptedApplicant' => Applicant::where('status', 'Accepted')->get(),
            'pendingApplicant' => Applicant::where('status', 'Pending')->get(),
            'rejectedApplicant' => Applicant::where('status', 'Rejected')->get(),
            'allApplicants' => Applicant::all(),
            'interviews' => Interview::all(),
            'applicantData' => Applicant::selectRaw('CONCAT(MONTHNAME(created_at), ", ", YEAR(created_at)) AS EachMonth, COUNT(*) AS TotalApplicants')
                    ->groupByRaw('YEAR(created_at), MONTH(created_at)')
                    ->orderby('created_at')
                    ->get()
        ]);
    }
}