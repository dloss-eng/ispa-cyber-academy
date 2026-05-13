<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;

class CertificateController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Certificate::class);

        $certificates = Certificate::with(['user','module'])
            ->orderByDesc('issued_at')
            ->paginate(20);

        return view('admin.certificates.index', compact('certificates'));
    }
}
