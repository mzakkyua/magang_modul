<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    // ================= ADMIN =================

    public function create()
    {
        $users = User::all();
        return view('admin.certificate.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'title' => 'required',
            'file' => 'required|mimes:pdf,jpg,png|max:2048'
        ]);

        $filePath = $request->file('file')->store('certificates', 'public');

        Certificate::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'file' => $filePath
        ]);

        return back()->with('success', 'Sertifikat berhasil diupload');
    }

    // ================= USER =================

    public function index()
    {
        $certificates = Certificate::where('user_id', Auth::id())->get();
        return view('magang.sertifikat.index', compact('certificates'));
    }

    public function download($id)
    {
        $cert = Certificate::findOrFail($id);

        if ($cert->user_id != Auth::id()) {
            abort(403);
        }

        return Storage::disk('public')->download($cert->file);
    }
}