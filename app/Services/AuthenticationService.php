<?php

namespace App\Services;

use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthenticationService
{
    /// US 1 : S'inscrire de manière sécurisée
    public function register(array $data): Utilisateur
    {
        return Utilisateur::create([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'mot_de_passe' => Hash::make($data['password']),
            'langue' => $data['langue'] ?? 'fr',
        ]);
    }

    /// US 2 : Se connecter de manière sécurisée
    public function login(string $email, string $password): bool|Utilisateur
    {
        $utilisateur = Utilisateur::where('email', $email)->first();

        if (!$utilisateur || !Hash::check($password, $utilisateur->mot_de_passe)) {
            return false;
        }

        Auth::login($utilisateur);
        return $utilisateur;
    }

    /// US 3 : Se déconnecter
    public function logout(): void
    {
        Auth::logout();
    }


    public function emailExists(string $email): bool
    {
        return Utilisateur::where('email', $email)->exists();
    }

    public function getCurrentUser(): ?Utilisateur
    {
        return Auth::guard()->user();
    }

    public function updateProfile(Utilisateur $utilisateur, array $data): Utilisateur
    {
        $utilisateur->update($data);
        return $utilisateur;
    }
}
