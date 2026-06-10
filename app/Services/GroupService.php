<?php

namespace App\Services;

use App\Models\Groupe;
use App\Models\Utilisateur;
use Illuminate\Support\Str;

class GroupService
{
    public function createGroup(Utilisateur $utilisateur, array $data): Groupe
    {
        return Groupe::create([
            'nom' => $data['nom'],
            'code_invitation' => $this->generateInvitationCode(),
            'id_utilisateur_createur' => $utilisateur->id_utilisateur,
        ]);
    }

    public function generateInvitationCode(): string
    {
        return strtoupper(Str::random(8));
    }

    public function regenerateInvitationCode(Groupe $groupe): string
    {
        $nouveauCode = $this->generateInvitationCode();
        $groupe->update(['code_invitation' => $nouveauCode]);
        return $nouveauCode;
    }

    public function joinGroupByCode(string $invitationCode): ?Groupe
    {
        return Groupe::where('code_invitation', $invitationCode)->first();
    }

    public function getUserGroups(Utilisateur $utilisateur)
    {
        return Groupe::where('id_utilisateur_createur', $utilisateur->id_utilisateur)->get();
    }

    public function getGroupById(int $groupId): ?Groupe
    {
        return Groupe::find($groupId);
    }

    public function deleteGroup(Groupe $groupe): bool
    {
        return $groupe->delete();
    }

    public function updateGroup(Groupe $groupe, array $data): Groupe
    {
        $groupe->update($data);
        return $groupe;
    }

    public function isGroupOwner(Utilisateur $utilisateur, Groupe $groupe): bool
    {
        return $groupe->id_utilisateur_createur === $utilisateur->id_utilisateur;
    }
}
