<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DoctorApplication;
use App\Models\User;

class AdminApplicationController extends Controller
{
    // For testing/demonstration purposes, this uses an open route. In production, protect this using an Admin Auth Middleware.
    public function index()
    {
        $applications = DoctorApplication::with(['user', 'documents.requirement'])->orderBy('created_at', 'desc')->get();
        return view('admin.applications.index', compact('applications'));
    }

    public function show($id)
    {
        $application = DoctorApplication::with(['user', 'documents.requirement'])->findOrFail($id);
        return view('admin.applications.show', compact('application'));
    }

    public function approve(Request $request, $id)
    {
        $application = DoctorApplication::findOrFail($id);

        $application->update([
            'status' => 'approved',
            'reviewed_at' => now(),
            'admin_notes' => $request->admin_notes,
            // 'reviewed_by_admin_id' => admin() id
        ]);

        $application->user->update([
            'role' => 'doctor',
            'doctor_status' => 'approved'
        ]);

        // Accept all documents for simplicity if approved
        foreach ($application->documents as $doc) {
            $doc->update(['status' => 'accepted']);
        }

        return redirect()->route('admin.applications.index')->with('success', 'Application approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $application = DoctorApplication::findOrFail($id);

        $application->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'admin_notes' => $request->admin_notes,
        ]);

        $application->user->update([
            'doctor_status' => 'rejected'
        ]);

        // Reject documents
        foreach ($application->documents as $doc) {
            $doc->update(['status' => 'rejected']);
        }

        return redirect()->route('admin.applications.index')->with('success', 'Application rejected.');
    }
}
