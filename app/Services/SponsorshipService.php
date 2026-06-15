<?php

namespace App\Services;

use App\Models\Sponsorisation;
use App\Models\Evenement;
use App\Models\Partenaire;

class SponsorshipService
{
    public function addSponsor(Evenement $evenement, Partenaire $partenaire, array $data): Sponsorisation
    {
        return Sponsorisation::create([
            'montant' => $data['montant'] ?? null,
            'type_visibilite' => $data['type_visibilite'] ?? 'standard',
            'id_evenement' => $evenement->id_evenement,
            'id_partenaire' => $partenaire->id_partenaire,
        ]);
    }

    public function getEventSponsors(Evenement $evenement)
    {
        return Sponsorisation::where('id_evenement', $evenement->id_evenement)
            ->with('partenaire')
            ->get();
    }

    public function getPartnerEvents(Partenaire $partenaire)
    {
        return Sponsorisation::where('id_partenaire', $partenaire->id_partenaire)
            ->with('evenement')
            ->get();
    }

    public function updateSponsorship(Sponsorisation $sponsorisation, array $data): Sponsorisation
    {
        $sponsorisation->update($data);
        return $sponsorisation;
    }

    public function removeSponsorship(Sponsorisation $sponsorisation): bool
    {
        return $sponsorisation->delete();
    }

    public function getTotalSponsorshipAmount(Evenement $evenement): float
    {
        return Sponsorisation::where('id_evenement', $evenement->id_evenement)
            ->sum('montant') ?? 0;
    }

    public function getSponsorsByVisibility(Evenement $evenement, string $visibility)
    {
        return Sponsorisation::where('id_evenement', $evenement->id_evenement)
            ->where('type_visibilite', $visibility)
            ->with('partenaire')
            ->get();
    }

    public function createPartner(array $data): Partenaire
    {
        return Partenaire::create([
            'nom' => $data['nom'],
            'secteur' => $data['secteur'] ?? null,
            'description' => $data['description'] ?? null,
        ]);
    }

    public function getPartnerById(int $partnerId): ?Partenaire
    {
        return Partenaire::find($partnerId);
    }

    public function getAllPartners()
    {
        return Partenaire::all();
    }

    public function updatePartner(Partenaire $partenaire, array $data): Partenaire
    {
        $partenaire->update($data);
        return $partenaire;
    }
}
