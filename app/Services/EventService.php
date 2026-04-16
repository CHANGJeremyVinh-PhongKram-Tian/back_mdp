<?php

namespace App\Services;

use App\Models\Evenement;
use App\Models\Organisateur;

class EventService
{
    /// US 11 : Consulter les événements disponibles
    public function getAvailableEvents($limit = 20, $offset = 0)
    {
        return Evenement::where('statut', 'actif')
            ->orderBy('date_debut', 'asc')
            ->limit($limit)
            ->offset($offset)
            ->with('organisateur')
            ->get();
    }

    /// US 12 : Créer un événement
    public function createEvent(Organisateur $organisateur, array $data): Evenement
    {
        return Evenement::create([
            'titre' => $data['titre'],
            'description' => $data['description'] ?? null,
            'date_debut' => $data['date_debut'],
            'date_fin' => $data['date_fin'],
            'lieu' => $data['lieu'],
            'capacite' => $data['capacite'] ?? null,
            'statut' => $data['statut'] ?? 'actif',
            'empreinte_carbonne' => $data['empreinte_carbonne'] ?? null,
            'id_organisateur' => $organisateur->id_organisateur,
        ]);
    }

    /// US 13 : Modifier les détails d'un événement
    public function updateEvent(Evenement $evenement, array $data): Evenement
    {
        $evenement->update($data);
        return $evenement;
    }

    /// US 14 : Consulter les statistiques d'un événement
    public function getEventStats(Evenement $evenement): array
    {
        $totalBillets = $evenement->billets()->count();
        $billetsPayes = $evenement->billets()->where('statut', 'paye')->count();
        $billetsEnAttente = $evenement->billets()->where('statut', 'en_attente')->count();
        $capacite = $evenement->capacite ?? 0;

        return [
            'total_billets' => $totalBillets,
            'billets_payes' => $billetsPayes,
            'billets_en_attente' => $billetsEnAttente,
            'capacite' => $capacite,
            'taux_remplissage' => $capacite > 0 ? round(($totalBillets / $capacite) * 100, 2) : 0,
            'places_restantes' => max(0, $capacite - $totalBillets),
        ];
    }

    public function getEventById(int $eventId): ?Evenement
    {
        return Evenement::with('organisateur', 'billets')->find($eventId);
    }

    public function getOrganizerEvents(Organisateur $organisateur)
    {
        return Evenement::where('id_organisateur', $organisateur->id_organisateur)
            ->orderBy('date_debut', 'desc')
            ->get();
    }

    public function searchEvents(string $query)
    {
        return Evenement::where('titre', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orWhere('lieu', 'LIKE', "%{$query}%")
            ->where('statut', 'actif')
            ->get();
    }

    public function deleteEvent(Evenement $evenement): bool
    {
        return $evenement->delete();
    }

    public function getUpcomingEvents($days = 30)
    {
        $now = now();
        $futureDate = $now->copy()->addDays($days);

        return Evenement::whereBetween('date_debut', [$now, $futureDate])
            ->where('statut', 'actif')
            ->orderBy('date_debut', 'asc')
            ->get();
    }
}
