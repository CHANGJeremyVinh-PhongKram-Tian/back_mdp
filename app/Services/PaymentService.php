<?php

namespace App\Services;

use App\Models\Billet;
use App\Models\Paiement;
use App\Models\Utilisateur;
use Illuminate\Support\Str;

class PaymentService
{
    // US 10 : Payer un billet
    public function processPayment(Billet $billet, array $data): Paiement
    {
        $paiement = Paiement::create([
            'montant' => $billet->prix,
            'moyen_paiement' => $data['moyen_paiement'] ?? 'carte',
            'statut' => 'en_cours',
            'id_billet' => $billet->id_billet,
        ]);

        return $paiement;
    }

    // US 11 : Payer un billet pour un membre
    public function payTicketForMember(Utilisateur $payeur, Billet $billet, array $data): Paiement
    {

        $paiement = Paiement::create([
            'montant' => $billet->prix,
            'moyen_paiement' => $data['moyen_paiement'] ?? 'carte',
            'statut' => 'en_cours',
            'id_billet' => $billet->id_billet,
        ]);

        return $paiement;
    }

    public function confirmPayment(Paiement $paiement): Paiement
    {
        $paiement->update(['statut' => 'confirme']);
        
        $paiement->billet->update(['statut' => 'paye']);

        return $paiement;
    }

    public function cancelPayment(Paiement $paiement): Paiement
    {
        $paiement->update(['statut' => 'annule']);
        
        $paiement->billet->update(['statut' => 'en_attente']);

        return $paiement;
    }

    public function getUserPayments(Utilisateur $utilisateur)
    {
        return Paiement::whereHas('billet', function ($query) use ($utilisateur) {
            $query->where('id_utilisateur', $utilisateur->id_utilisateur);
        })->with('billet.evenement')->get();
    }

    public function getPaymentById(int $paymentId): ?Paiement
    {
        return Paiement::find($paymentId);
    }

    public function generatePaymentReference(): string
    {
        return 'PAY-' . Str::uuid();
    }

    public function getPaymentStatus(Paiement $paiement): string
    {
        return $paiement->statut;
    }

    public function getEventPaymentHistory(int $eventId)
    {
        return Paiement::whereHas('billet', function ($query) use ($eventId) {
            $query->where('id_evenement', $eventId);
        })->with('billet.utilisateur')->get();
    }

    public function getEventRevenue(int $eventId): float
    {
        return Paiement::whereHas('billet', function ($query) use ($eventId) {
            $query->where('id_evenement', $eventId);
        })->where('statut', 'confirme')->sum('montant');
    }
}
