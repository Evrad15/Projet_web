@extends('layouts.app')

@section('content')
@php $user = Auth::user(); @endphp

{{-- HEADER --}}
<header class="bg-white shadow">
    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h4>Profil</h4>
            <span class="px-4 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-bold uppercase">
                {{ $user->role ?? 'N/A' }}
            </span>
        </div>
    </div>
</header>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- COLONNE GAUCHE : carte profil --}}
            <div class="md:col-span-1 space-y-6">
                <div class="p-6 bg-white shadow-xl rounded-2xl border border-gray-100 text-center">
                    <div class="w-24 h-24 bg-indigo-500 rounded-full mx-auto flex items-center justify-center text-white text-3xl font-bold shadow-lg mb-4">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">{{ $user->name }}</h3>
                    <p class="text-gray-500 text-sm">{{ $user->email }}</p>
                    <p class="text-indigo-600 font-medium mt-2">{{ $user->phone ?? 'Pas de numéro' }}</p>
                    <hr class="my-6 border-gray-100">
                    <div class="text-left space-y-3">
                        <p class="text-xs text-gray-400 uppercase font-black">Statut Compte</p>
                        <div class="flex items-center text-sm text-green-600">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path>
                            </svg>
                            Actif & Vérifié
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLONNE DROITE : formulaires --}}
            <div class="md:col-span-2 space-y-6">

                {{-- Informations générales --}}
                <div class="p-8 bg-white shadow-sm rounded-2xl border border-gray-100 transition hover:shadow-md">
                    <div class="flex items-center mb-6">
                        <div class="p-2 bg-blue-50 rounded-lg mr-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Informations Générales</h3>
                    </div>
                    @include('profile.partials.update-profile-information-form')
                </div>

                {{-- Sécurité --}}
                <div class="p-8 bg-white shadow-sm rounded-2xl border border-gray-100 transition hover:shadow-md">
                    <div class="flex items-center mb-6">
                        <div class="p-2 bg-yellow-50 rounded-lg mr-4">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Sécurité du compte</h3>
                    </div>
                    @include('profile.partials.update-password-form')
                </div>

                {{-- Zone de danger --}}
                <div class="p-8 bg-red-50 shadow-sm rounded-2xl border border-red-100">
                    <div class="flex items-center mb-6 text-red-700">
                        <svg class="w-6 h-6 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <h3 class="text-lg font-bold">Zone de danger</h3>
                    </div>
                    @include('profile.partials.delete-user-form')
                </div>

            </div>
        </div>
    </div>
</div>

@endsection