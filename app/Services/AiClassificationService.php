<?php
namespace App\Services;

class AiClassificationService
{
    protected static array $patterns = [
        'arnaque_mobile_money' => [
            'keywords' => ['momo','mobile money','orange money','wave','transfert','code secret','retrait','dépôt','fcfa','cfa','gagné','lot','prix','envoyer argent','compte bloqué mobile'],
            'confidence' => 92,
        ],
        'sms_frauduleux' => [
            'keywords' => ['sms','message','reçu un message','numéro','appeler','0700','07 00','félicitations','gagné','gratuit','urgent','cliquez'],
            'confidence' => 88,
        ],
        'phishing_whatsapp' => [
            'keywords' => ['whatsapp','profil','contact','ami','groupe','lien whatsapp','message whatsapp','se fait passer','usurpation'],
            'confidence' => 85,
        ],
        'phishing_email' => [
            'keywords' => ['email','mail','courriel','lien','cliquez ici','mot de passe','compte','vérification','pièce jointe','connexion suspecte'],
            'confidence' => 87,
        ],
        'faux_site' => [
            'keywords' => ['site','url','lien','http','www','.com','.ci','.ml','faux site','boutique','acheter','paiement en ligne','commande','livraison'],
            'confidence' => 83,
        ],
        'cyberharcèlement' => [
            'keywords' => ['harcèlement','menace','insulte','photo','vidéo privée','diffusion','intimidation','revenge','nude','chantage','persécution'],
            'confidence' => 90,
        ],
    ];

    public static function classify(string $text): array
    {
        $text = mb_strtolower($text);
        $scores = [];

        foreach (self::$patterns as $category => $data) {
            $matchCount = 0;
            foreach ($data['keywords'] as $keyword) {
                if (str_contains($text, mb_strtolower($keyword))) {
                    $matchCount++;
                }
            }
            if ($matchCount > 0) {
                $score = min(99, $data['confidence'] + ($matchCount * 3));
                $scores[$category] = $score;
            }
        }

        if (empty($scores)) {
            return ['category' => 'autre', 'confidence' => 50, 'label' => '❓ Non classifié'];
        }

        arsort($scores);
        $bestCategory = array_key_first($scores);
        $bestScore = $scores[$bestCategory];

        $labels = [
            'arnaque_mobile_money' => '💰 Arnaque Mobile Money',
            'sms_frauduleux' => '📱 SMS Frauduleux',
            'phishing_whatsapp' => '💬 Phishing WhatsApp',
            'phishing_email' => '📧 Phishing Email',
            'faux_site' => '🌐 Faux Site Web',
            'cyberharcèlement' => '😢 Cyberharcèlement',
        ];

        return [
            'category' => $bestCategory,
            'confidence' => $bestScore,
            'label' => $labels[$bestCategory] ?? '❓ Autre',
            'all_scores' => $scores,
        ];
    }
}
