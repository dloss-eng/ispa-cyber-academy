<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Signalement;
use App\Services\AiClassificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SignalementController extends Controller
{
    /**
     * Types d'arnaques autorisés (whitelist).
     * Doit correspondre aux options du <select> dans le formulaire.
     */
    private const TYPES_AUTORISES = [
        'mobile_money',
        'phishing',
        'reseaux_sociaux',
        'cyberharcelement',
        'fausse_offre',
        'autre',
    ];

    public function index()
    {
        $signalements = Auth::user()->signalements()->latest()->paginate(10);
        return view('signalement.index', compact('signalements'));
    }

    public function create()
    {
        return view('signalement.create');
    }

    public function store(Request $request)
    {
        // Validation stricte avec whitelist + limites de longueur
        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(self::TYPES_AUTORISES)],
            'description' => 'required|string|min:10|max:5000',
            'suspect_contact' => 'nullable|string|max:255',
            'incident_date' => 'nullable|date|before_or_equal:today',
            // mimes vérifie l'extension ET le type MIME réel du fichier (pas juste l'extension)
            'screenshot' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        // Sanitisation : on échappe tout HTML dans la description (stockage propre)
        // L'affichage utilise déjà {{ }} qui échappe, mais double-protection.
        $description = strip_tags($validated['description']);
        $suspectContact = isset($validated['suspect_contact'])
            ? strip_tags($validated['suspect_contact'])
            : null;

        $screenshotPath = null;
        if ($request->hasFile('screenshot')) {
            $file = $request->file('screenshot');
            // Vérification du MIME réel via getimagesize (plus fiable que l'extension)
            $imageInfo = @getimagesize($file->getPathname());
            if ($imageInfo === false) {
                return back()->withErrors(['screenshot' => 'Fichier image invalide.'])->withInput();
            }
            $screenshotPath = $file->store('signalements', 'public');
        }

        // Classification IA
        $ai = AiClassificationService::classify($description);

        $signalement = Signalement::create([
            'user_id' => Auth::id(),
            'ticket_number' => Signalement::generateTicket(),
            'type' => $validated['type'],
            'description' => $description,
            'suspect_contact' => $suspectContact,
            'screenshot_path' => $screenshotPath,
            'incident_date' => $validated['incident_date'] ?? null,
            'ai_category' => $ai['category'],
            'ai_confidence' => $ai['confidence'],
        ]);

        return redirect()
            ->route('signalements.index')
            ->with('success', 'Signalement envoyé ! N° ' . $signalement->ticket_number . ' — IA : ' . $ai['label'] . ' (' . $ai['confidence'] . '%)');
    }
}
