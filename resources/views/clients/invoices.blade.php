@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);
    }

    .page-header {
        background: var(--primary-gradient);
        color: #5a3f37;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    }

    .table thead {
        background-color: #f8f9fa;
        color: #4a5568;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }

    .badge-impaye {
        background: rgba(239, 68, 68, .12);
        color: #ef4444;
        border-radius: 20px;
    }

    .badge-partiel {
        background: rgba(245, 158, 11, .12);
        color: #f59e0b;
        border-radius: 20px;
    }

    .badge-paye {
        background: rgba(34, 197, 94, .12);
        color: #22c55e;
        border-radius: 20px;
    }
</style>

<div class="container py-4">
    <!-- Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <a href="{{ route('dashboards.clients') }}" class="btn btn-light btn-sm rounded-circle me-3 d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" title="Retour à l'espace client">
                <i class="bi bi-arrow-left fs-5"></i>
            </a>
            <div>
                <h1 class="fw-bold mb-1">Mes Factures</h1>
                <p class="mb-0 opacity-75">Historique de tous vos achats finalisés.</p>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Facture N°</th>
                            <th>Date</th>
                            <th>Montant Total</th>
                            <th>Statut Paiement</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                        @php
                        $total = $invoice->total ?? 0;
                        $paid = $invoice->paid_amount ?? 0;
                        $balance = $total - $paid;
                        @endphp
                        <tr>
                            <td class="ps-4 fw-bold">#FA-{{ $invoice->id }}</td>
                            <td>{{ $invoice->created_at->format('d/m/Y') }}</td>
                            <td class="fw-bold">{{ number_format($total, 0, ',', ' ') }} FCFA</td>
                            <td>
                                @if($balance <= 0 && $total> 0)
                                    <span class="badge badge-paye px-3 py-1">Payé</span>
                                    @elseif($paid > 0)
                                    <span class="badge badge-partiel px-3 py-1">Partiel</span>
                                    @else
                                    <span class="badge badge-impaye px-3 py-1">Impayé</span>
                                    @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" target="_blank">
                                    <i class="bi bi-printer me-1"></i> Télécharger
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-receipt fs-1 d-block mb-2 opacity-25"></i>
                                Vous n'avez aucune facture pour le moment.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $invoices->links() }}
    </div>
</div>
@endsection