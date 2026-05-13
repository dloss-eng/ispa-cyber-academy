<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    public function download(Certificate $certificate)
    {
        $this->authorize('download', $certificate);

        $certificate->loadMissing(['user', 'module']);

        // 🔐 filename sécurisé
        $number = $certificate->certificate_number ?? 'certificat';
        $filename = 'certificat-' . Str::slug($number) . '.pdf';

        // 🔥 cache PDF (gros gain perf)
        $path = 'certificates/' . $filename;

        if (!Storage::disk('public')->exists($path)) {

            try {
                $pdf = Pdf::loadView('certificates.pdf', [
                    'certificate' => $certificate
                ])->setPaper('a4', 'landscape');

                Storage::disk('public')->put($path, $pdf->output());

            } catch (\Exception $e) {
                return back()->withErrors([
                    'certificate' => 'Erreur lors de la génération du PDF.'
                ]);
            }
        }

        // 🔥 download fichier stocké
        return Storage::disk('public')->download($path);
    }

    public function show(Certificate $certificate)
    {
        $this->authorize('view', $certificate);

        $certificate->loadMissing(['user', 'module']);

        return view('certificates.show', compact('certificate'));
    }
}