<?php

namespace App\Services;

use App\Models\Organisateur;
use App\Models\Utilisateur;

class OrganizerRequestService
{
    // US 8 : Demander le statut d'organisateur
    public function requestOrganizerStatus(Utilisateur $utilisateur, array $data): Organisateur
    {
        return Organisateur::create([
            'nom_structure' => $data['nom_structure'],
            'type_structure' => $data['type_structure'],
            'description' => $data['description'] ?? null,
            'id_utilisateur' => $utilisateur->id_utilisateur,
        ]);
    }

    public function isOrganizer(Utilisateur $utilisateur): bool
    {
        return Organisateur::where('id_utilisateur', $utilisateur->id_utilisateur)->exists();
    }

    public function getOrganizerInfo(Utilisateur $utilisateur): ?Organisateur
    {
        return Organisateur::where('id_utilisateur', $utilisateur->id_utilisateur)->first();
    }

    public function updateOrganizerInfo(Organisateur $organisateur, array $data): Organisateur
    {
        $organisateur->update($data);
        return $organisateur;
    }


    public function getAllOrganizers()
    {
        return Organisateur::with('utilisateur')->get();
    }
}
