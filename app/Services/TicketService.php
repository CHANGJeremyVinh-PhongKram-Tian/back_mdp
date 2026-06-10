<?php

namespace App\Services;

use App\Models\Billet;
use App\Models\Evenement;
use App\Models\Utilisateur;

class TicketService
{
    public function reserveTicket(Utilisateur $utilisateur, Evenement $evenement, array $data): Billet
    {
        return Billet::create([
            'prix' => $data['prix'] ?? 0,
            'statut' => 'en_attente',
            'id_utilisateur' => $utilisateur->id_utilisateur,
            'id_evenement' => $evenement->id_evenement,
        ]);
    }

    public function getUserTickets(Utilisateur $utilisateur)
    {
        return Billet::where('id_utilisateur', $utilisateur->id_utilisateur)
            ->with('evenement', 'evenement.organisateur')
            ->orderBy('date_achat', 'desc')
            ->get();
    }

    public function getEventTickets(Evenement $evenement)
    {
        return Billet::where('id_evenement', $evenement->id_evenement)
            ->with('utilisateur')
            ->get();
    }

    public function getTicketById(int $ticketId): ?Billet
    {
        return Billet::find($ticketId);
    }

    public function hasTicketForEvent(Utilisateur $utilisateur, Evenement $evenement): bool
    {
        return Billet::where('id_utilisateur', $utilisateur->id_utilisateur)
            ->where('id_evenement', $evenement->id_evenement)
            ->exists();
    }

    public function updateTicketStatus(Billet $billet, string $status): Billet
    {
        $billet->update(['statut' => $status]);
        return $billet;
    }

    public function cancelTicket(Billet $billet): bool
    {
        return $this->updateTicketStatus($billet, 'annule')->save();
    }

    public function getTicketPrice(Evenement $evenement): float
    {
        return 0;
    }

    public function isTicketAvailable(Evenement $evenement): bool
    {
        if (!$evenement->capacite) {
            return true;
        }

        $billetsVendus = Billet::where('id_evenement', $evenement->id_evenement)
            ->where('statut', '!=', 'annule')
            ->count();

        return $billetsVendus < $evenement->capacite;
    }
}
