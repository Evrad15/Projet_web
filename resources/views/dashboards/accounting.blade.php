@extends('layouts.app')

@section('content')
<style>
    :root { --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }

    .page-header {
        background: var(--primary-gradient);
        color: white; padding: 2rem; border-radius: 15px;
        margin-bottom: 2rem; box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .card  { border: none; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
    .table thead { background-color:#f8f9fa; color:#4a5568; text-transform:uppercase; font-size:.75rem; letter-spacing:.05em; }
    .kpi-card { border-left: 4px solid; border-radius: 12px; transition: transform .2s; }
    .kpi-card:hover { transform: translateY(-3px); }
    .nav-sidebar .nav-link { font-weight:600; color:#374151; border-radius:8px; padding:.6rem 1rem; }
    .nav-sidebar .nav-link:hover  { background:#f3f4f6; color:#667eea; }
    .nav-sidebar .nav-link.active { background:rgba(102,126,234,.12); color:#667eea; }
    .badge-impaye  { background:rgba(239,68,68,.12);  color:#ef4444; border-radius:20px; }
    .badge-partiel { background:rgba(245,158,11,.12); color:#f59e0b; border-radius:20px; }
    .badge-paye    { background:rgba(34,197,94,.12);  color:#22c55e; border-radius:20px; }
    .tab-section   { animation: fadeIn .2s ease; }
    @keyframes fadeIn { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }
</style>

<div class="d-flex">

    {{-- ══ SIDEBAR ══ --}}
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-white border-end"
         style="width:250px; min-height:calc(100vh - 65px);">
        <span class="fs-6 fw-bold text-primary mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-calculator fs-5"></i> Comptabilité
        </span>
        <hr class="mt-0">
        <ul class="nav flex-column mb-auto nav-sidebar gap-1">
            <li>
                <a href="#" onclick="showTab('kpis', this)" class="nav-link active">
                    <i class="bi bi-speedometer2 me-2"></i>Tableau de bord
                </a>
            </li>
            <li>
                <a href="#" onclick="showTab('paiements', this)" class="nav-link">
                    <i class="bi bi-cash-stack me-2 text-success"></i>Paiements reçus
                </a>
            </li>
            <li>
                <a href="#" onclick="showTab('impayes', this)" class="nav-link">
                    <i class="bi bi-exclamation-circle me-2 text-danger"></i>Impayés
                    @if(isset($impayesCount) && $impayesCount > 0)
                        <span class="badge bg-danger ms-1">{{ $impayesCount }}</span>
                    @endif
                </a>
            </li>
            <li>
                <a href="#" onclick="showTab('depenses', this)" class="nav-link">
                    <i class="bi bi-arrow-down-circle me-2 text-warning"></i>Dépenses
                </a>
            </li>
            <li>
                <a href="#" onclick="showTab('ventes', this)" class="nav-link">
                    <i class="bi bi-eye me-2 text-secondary"></i>Ventes (lecture)
                </a>
            </li>
        </ul>
        <hr>
        <div class="d-grid gap-2">
            <button class="btn btn-sm btn-outline-primary fw-semibold"
                    data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="bi bi-download me-2"></i>Exporter rapport
            </button>
        </div>
    </div>

    {{-- ══ CONTENT ══ --}}
    <div class="flex-grow-1 p-4" style="background:#f8f9fa; min-height:calc(100vh - 65px);">

        {{-- Header --}}
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h1 class="fw-bold mb-1">Comptabilité</h1>
                <p class="mb-0 opacity-75">Suivi financier — {{ now()->translatedFormat('F Y') }}</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-light fw-bold shadow-sm"
                        style="color:#22c55e;"
                        data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                    <i class="bi bi-plus-circle me-2"></i>Enregistrer un paiement
                </button>
                <button class="btn btn-light fw-bold shadow-sm"
                        style="color:#f59e0b;"
                        data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                    <i class="bi bi-dash-circle me-2"></i>Enregistrer une dépense
                </button>
            </div>
        </div>

        {{-- Alertes session --}}
        @if(session('success'))
            <div class="alert alert-success border-0 rounded-3 mb-4">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 rounded-3 mb-4">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            </div>
        @endif

        {{-- ════════════════════════════════════════ --}}
        {{-- TAB 1 : TABLEAU DE BORD                  --}}
        {{-- ════════════════════════════════════════ --}}
        <div id="tab-kpis" class="tab-section">

            {{-- KPIs --}}
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="card kpi-card p-3 h-100" style="border-color:#667eea;">
                        <div class="text-muted small fw-bold text-uppercase mb-1">CA du mois</div>
                        <div class="fs-3 fw-bold text-primary">
                            {{ number_format($caMonth ?? 0, 0, ',', ' ') }}
                            <small class="fs-6 fw-normal">FCFA</small>
                        </div>
                        <div class="text-muted small mt-1">
                            <i class="bi bi-calendar me-1"></i>{{ now()->format('F Y') }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card kpi-card p-3 h-100" style="border-color:#22c55e;">
                        <div class="text-muted small fw-bold text-uppercase mb-1">Total encaissé</div>
                        <div class="fs-3 fw-bold text-success">
                            {{ number_format($totalEncaisse ?? 0, 0, ',', ' ') }}
                            <small class="fs-6 fw-normal">FCFA</small>
                        </div>
                        <div class="text-muted small mt-1">
                            <i class="bi bi-cash me-1"></i>Paiements reçus
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card kpi-card p-3 h-100" style="border-color:#ef4444;">
                        <div class="text-muted small fw-bold text-uppercase mb-1">Créances clients</div>
                        <div class="fs-3 fw-bold text-danger">
                            {{ number_format($totalImpayes ?? 0, 0, ',', ' ') }}
                            <small class="fs-6 fw-normal">FCFA</small>
                        </div>
                        <div class="text-muted small mt-1">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            {{ $impayesCount ?? 0 }} factures impayées
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card kpi-card p-3 h-100" style="border-color:#f59e0b;">
                        <div class="text-muted small fw-bold text-uppercase mb-1">Dépenses du mois</div>
                        <div class="fs-3 fw-bold text-warning">
                            {{ number_format($depensesMonth ?? 0, 0, ',', ' ') }}
                            <small class="fs-6 fw-normal">FCFA</small>
                        </div>
                        <div class="text-muted small mt-1">
                            Bénéfice net :
                            <strong class="{{ (($caMonth ?? 0) - ($depensesMonth ?? 0)) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format(($caMonth ?? 0) - ($depensesMonth ?? 0), 0, ',', ' ') }} FCFA
                            </strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Graphique + Camembert --}}
            <div class="row g-3 mb-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 pt-3 px-4">
                            <span class="fw-bold text-muted text-uppercase" style="font-size:.72rem; letter-spacing:1px;">
                                <i class="bi bi-bar-chart me-2"></i>Évolution financière — 6 derniers mois
                            </span>
                        </div>
                        <div class="card-body">
                            <canvas id="financeChart" height="120"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 pt-3 px-4">
                            <span class="fw-bold text-muted text-uppercase" style="font-size:.72rem; letter-spacing:1px;">
                                <i class="bi bi-pie-chart me-2"></i>Statut des paiements
                            </span>
                        </div>
                        <div class="card-body d-flex flex-column align-items-center justify-content-center gap-3">
                            <canvas id="paymentPie" width="150" height="150"></canvas>
                            <div class="w-100">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge badge-paye px-3 py-1">✓ Payé</span>
                                    <strong>{{ $ventesPayees ?? 0 }}</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge badge-partiel px-3 py-1">~ Partiel</span>
                                    <strong>{{ $ventesPartielles ?? 0 }}</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge badge-impaye px-3 py-1">✗ Impayé</span>
                                    <strong>{{ $ventesImPayees ?? 0 }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Derniers paiements --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3 px-4">
                    <span class="fw-bold text-muted text-uppercase" style="font-size:.72rem; letter-spacing:1px;">
                        <i class="bi bi-clock-history me-2"></i>Derniers paiements reçus
                    </span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Client</th>
                                <th>Vente</th>
                                <th>Montant</th>
                                <th>Méthode</th>
                                <th class="pe-4">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments ?? [] as $p)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $p->sale->client->name ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('sales.show', $p->sale_id) }}"
                                       class="badge bg-primary-subtle text-primary text-decoration-none">
                                        #{{ $p->sale_id }}
                                    </a>
                                </td>
                                <td class="fw-bold text-success">+{{ number_format($p->amount, 0, ',', ' ') }} FCFA</td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ ucfirst($p->method ?? 'cash') }}
                                            {{ ucfirst($p->payment_method ?? 'cash') }}
                                    </span>
                                </td>
                                <td class="text-muted pe-4">{{ $p->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Aucun paiement récent.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════ --}}
        {{-- TAB 2 : PAIEMENTS REÇUS                 --}}
        {{-- ════════════════════════════════════════ --}}
        <div id="tab-paiements" class="tab-section d-none">

            {{-- Filtres --}}
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.accounting') }}"
                          class="row g-3 align-items-end">
                        <input type="hidden" name="tab" value="paiements">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Client</label>
                            <input type="text" name="search_client" class="form-control bg-light border-0"
                                   placeholder="Nom du client..." value="{{ request('search_client') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Du</label>
                            <input type="date" name="date_from" class="form-control bg-light border-0"
                                   value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Au</label>
                            <input type="date" name="date_to" class="form-control bg-light border-0"
                                   value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100 fw-bold">Filtrer</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive" style="overflow:visible;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>Vente</th>
                                    <th>Client</th>
                                    <th>Montant</th>
                                    <th>Méthode</th>
                                    <th>Date</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                <tr>
                                    <td class="ps-4 fw-bold text-muted">#{{ $payment->id }}</td>
                                    <td>
                                        <a href="{{ route('sales.show', $payment->sale_id) }}"
                                           class="badge bg-primary-subtle text-primary text-decoration-none">
                                            #{{ $payment->sale_id }}
                                        </a>
                                    </td>
                                    <td>{{ $payment->sale->client->name ?? '—' }}</td>
                                    <td class="fw-bold text-success">
                                        {{ number_format($payment->amount, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ ucfirst($payment->method ?? 'cash') }}
                                            {{ ucfirst($payment->payment_method ?? 'cash') }}
                                        </span>
                                    </td>
                                    <td class="text-muted">{{ $payment->created_at->format('d/m/Y') }}</td>
                                    <td class="text-end pe-4">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm rounded-circle shadow-sm"
                                                    data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg"
                                                style="border-radius:12px; z-index:1055;">
                                                <li>
                                                    <a class="dropdown-item py-2"
                                                       href="{{ route('payments.edit', $payment->id) }}">
                                                        <i class="bi bi-pencil me-2 text-warning"></i>Modifier
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('payments.destroy', $payment->id) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Supprimer ce paiement ?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="dropdown-item py-2 text-danger">
                                                            <i class="bi bi-trash me-2"></i>Supprimer
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                                        Aucun paiement enregistré.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="mt-3">{{ $payments->links() }}</div>
        </div>

        {{-- ════════════════════════════════════════ --}}
        {{-- TAB 3 : IMPAYÉS                          --}}
        {{-- ════════════════════════════════════════ --}}
        <div id="tab-impayes" class="tab-section d-none">

            @if(($impayesCount ?? 0) > 0)
            <div class="alert border-0 rounded-3 mb-4"
                 style="background:rgba(239,68,68,.08); color:#991b1b;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>{{ $impayesCount }} factures impayées</strong> —
                total de <strong>{{ number_format($totalImpayes ?? 0, 0, ',', ' ') }} FCFA</strong> à recouvrer.
            </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive" style="overflow:visible;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Vente</th>
                                    <th>Client</th>
                                    <th>Total facture</th>
                                    <th>Déjà payé</th>
                                    <th>Reste dû</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($impayes ?? [] as $sale)
                                @php
                                    $total  = $sale->total_amount ?? $sale->total ?? 0;
                                    $paid   = $sale->paid_amount ?? 0;
                                    $reste  = $total - $paid;
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <a href="{{ route('sales.show', $sale->id) }}"
                                           class="fw-bold text-primary text-decoration-none">
                                            #{{ $sale->id }}
                                        </a>
                                    </td>
                                    <td class="fw-bold">{{ $sale->client->name ?? '—' }}</td>
                                    <td>{{ number_format($total, 0, ',', ' ') }} FCFA</td>
                                    <td class="text-success">{{ number_format($paid, 0, ',', ' ') }} FCFA</td>
                                    <td class="fw-bold text-danger">{{ number_format($reste, 0, ',', ' ') }} FCFA</td>
                                    <td class="text-muted">{{ $sale->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        @if($paid == 0)
                                            <span class="badge badge-impaye px-3 py-1">Impayé</span>
                                        @else
                                            <span class="badge badge-partiel px-3 py-1">Partiel</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-success rounded-pill px-3 fw-bold"
                                                data-bs-toggle="modal"
                                                data-bs-target="#encaisserModal"
                                                data-sale-id="{{ $sale->id }}"
                                                data-client="{{ $sale->client->name ?? '' }}"
                                                data-reste="{{ $reste }}"
                                                onclick="fillEncaisserModal(this)">
                                            <i class="bi bi-cash me-1"></i>Encaisser
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-check-circle fs-1 d-block mb-2 text-success opacity-50"></i>
                                        Aucun impayé. Tout est à jour !
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════ --}}
        {{-- TAB 4 : DÉPENSES                         --}}
        {{-- ════════════════════════════════════════ --}}
        <div id="tab-depenses" class="tab-section d-none">

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3 kpi-card" style="border-color:#f59e0b;">
                        <div class="text-muted small fw-bold text-uppercase">Dépenses du mois</div>
                        <div class="fs-4 fw-bold text-warning mt-1">
                            {{ number_format($depensesMonth ?? 0, 0, ',', ' ') }} FCFA
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3 kpi-card" style="border-color:#8b5cf6;">
                        <div class="text-muted small fw-bold text-uppercase">Dépenses de l'année</div>
                        <div class="fs-4 fw-bold mt-1" style="color:#8b5cf6;">
                            {{ number_format($depensesYear ?? 0, 0, ',', ' ') }} FCFA
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3 kpi-card" style="border-color:#22c55e;">
                        <div class="text-muted small fw-bold text-uppercase">Bénéfice net (mois)</div>
                        <div class="fs-4 fw-bold mt-1 {{ (($caMonth ?? 0) - ($depensesMonth ?? 0)) >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format(($caMonth ?? 0) - ($depensesMonth ?? 0), 0, ',', ' ') }} FCFA
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive" style="overflow:visible;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Libellé</th>
                                    <th>Catégorie</th>
                                    <th>Montant</th>
                                    <th>Méthode</th>
                                    <th>Date</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($depenses ?? [] as $dep)
                                <tr>
                                    <td class="ps-4 fw-bold">{{ $dep->title }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $dep->category->name ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-danger">
                                        −{{ number_format($dep->amount, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="text-muted">{{ ucfirst($dep->payment_method ?? '—') }}</td>
                                    <td class="text-muted">{{ $dep->expense_date }}</td>
                                    <td class="text-end pe-4">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm rounded-circle shadow-sm"
                                                    data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg"
                                                style="border-radius:12px; z-index:1055;">
                                                <li>
                                                    <a class="dropdown-item py-2" href="#"
                                                       data-bs-toggle="modal"
                                                       data-bs-target="#editDepenseModal"
                                                       onclick="fillEditDepense({{ $dep->id }}, '{{ addslashes($dep->title) }}', {{ $dep->amount }})">
                                                        <i class="bi bi-pencil me-2 text-warning"></i>Modifier
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('expenses.destroy', $dep->id) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Supprimer cette dépense ?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="dropdown-item py-2 text-danger">
                                                            <i class="bi bi-trash me-2"></i>Supprimer
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                                        Aucune dépense enregistrée.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════ --}}
        {{-- TAB 5 : VENTES (lecture seule)           --}}
        {{-- ════════════════════════════════════════ --}}
        <div id="tab-ventes" class="tab-section d-none">

            <div class="alert alert-info border-0 rounded-3 mb-4 small">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Consultation uniquement.</strong>
                Les ventes sont modifiables uniquement par le responsable commercial.
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Réf.</th>
                                    <th>Client</th>
                                    <th>Total</th>
                                    <th>Payé</th>
                                    <th>Reste dû</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                    <th class="text-end pe-4">Voir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allSales ?? [] as $sale)
                                @php
                                    $total = $sale->total_amount ?? $sale->total ?? 0;
                                    $paid  = $sale->paid_amount ?? 0;
                                    $reste = $total - $paid;
                                @endphp
                                <tr>
                                    <td class="ps-4 fw-bold">#{{ $sale->id }}</td>
                                    <td>{{ $sale->client->name ?? '—' }}</td>
                                    <td class="fw-bold">{{ number_format($total, 0, ',', ' ') }} FCFA</td>
                                    <td class="text-success">{{ number_format($paid, 0, ',', ' ') }} FCFA</td>
                                    <td class="{{ $reste > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                                        {{ number_format(max(0, $reste), 0, ',', ' ') }} FCFA
                                    </td>
                                    <td>
                                        @if($reste <= 0)
                                            <span class="badge badge-paye px-3 py-1">Payé</span>
                                        @elseif($paid > 0)
                                            <span class="badge badge-partiel px-3 py-1">Partiel</span>
                                        @else
                                            <span class="badge badge-impaye px-3 py-1">Impayé</span>
                                        @endif
                                    </td>
                                    <td class="text-muted">{{ $sale->created_at->format('d/m/Y') }}</td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('sales.show', $sale->id) }}"
                                           class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="bi bi-eye me-1"></i>Voir
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">Aucune vente.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- fin flex-grow-1 --}}
</div>{{-- fin d-flex --}}


{{-- ══════════════════════════════════════════════════════ --}}
{{-- MODALS — hors du layout, juste avant @endsection      --}}
{{-- ══════════════════════════════════════════════════════ --}}

{{-- Modal : Enregistrer un paiement --}}
<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px;">
            <form action="{{ route('payments.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-cash-stack me-2 text-success"></i>Enregistrer un paiement
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">ID de la vente</label>
                        <input type="number" name="sale_id" class="form-control bg-light border-0"
                               placeholder="Ex: 42" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Montant reçu (FCFA)</label>
                        <input type="number" name="amount" class="form-control bg-light border-0"
                               placeholder="0" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Méthode de paiement</label>
                        <select name="payment_method" class="form-select bg-light border-0" required>
                            <option value="Espèces">Espèces</option>
                            <option value="MTN Money">MTN Money</option>
                            <option value="ORANGE Money">ORANGE Money</option>
                            <option value="Virement bancaire">Virement bancaire</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold text-muted small text-uppercase">Note (optionnel)</label>
                        <textarea name="notes" class="form-control bg-light border-0" rows="2"
                                  placeholder="Observation..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-link text-muted text-decoration-none"
                            data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success fw-bold px-4">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal : Encaisser un impayé --}}
<div class="modal fade" id="encaisserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px;">
            <form action="{{ route('payments.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="sale_id" id="enc_sale_id">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-cash me-2 text-success"></i>
                        Encaisser — <span id="enc_client" class="text-primary"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="alert border-0 rounded-3 small mb-3"
                         style="background:rgba(245,158,11,.1); color:#92400e;">
                        Montant restant dû :
                        <strong id="enc_reste_label"></strong> FCFA
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Montant encaissé (FCFA)</label>
                        <input type="number" name="amount" id="enc_amount"
                               class="form-control bg-light border-0" required min="1">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold text-muted small text-uppercase">Méthode</label>
                        <select name="payment_method" class="form-select bg-light border-0" required>
                            <option value="Espèces">Espèces</option>
                            <option value="MTN Money">MTN Money</option>
                            <option value="ORANGE Money">ORANGE Money</option>
                            <option value="Virement bancaire">Virement bancaire</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-link text-muted"
                            data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success fw-bold px-4">
                        Confirmer l'encaissement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal : Enregistrer une dépense --}}
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px;">
            <form action="{{ route('expenses.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-dash-circle me-2 text-warning"></i>Enregistrer une dépense
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Libellé</label>
                        <input type="text" name="title" class="form-control bg-light border-0"
                               required placeholder="Ex: Loyer octobre...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Catégorie</label>
                        <select name="expense_category_id" class="form-select bg-light border-0" required>
                            @foreach($expenseCategories ?? [] as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Montant (FCFA)</label>
                        <input type="number" name="amount" class="form-control bg-light border-0"
                               required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Date</label>
                        <input type="date" name="expense_date" class="form-control bg-light border-0"
                               value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold text-muted small text-uppercase">Méthode de paiement</label>
                        <select name="payment_method" class="form-select bg-light border-0" required>
                            <option value="Espèces">Espèces</option>
                            <option value="MTN Money">MTN Money</option>
                            <option value="ORANGE Money">ORANGE Money</option>
                            <option value="Virement bancaire">Virement bancaire</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-link text-muted"
                            data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning fw-bold px-4">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal : Modifier une dépense --}}
<div class="modal fade" id="editDepenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px;">
            <form id="editDepenseForm" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil me-2 text-warning"></i>Modifier la dépense
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Libellé</label>
                        <input type="text" name="title" id="edit_dep_title"
                               class="form-control bg-light border-0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Montant (FCFA)</label>
                        <input type="number" name="amount" id="edit_dep_amount"
                               class="form-control bg-light border-0" required min="1">
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-link text-muted"
                            data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning fw-bold px-4">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal : Exporter rapport --}}
<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px;">
            <form action="{{ route('reports.export', 'financial') }}" method="GET">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-download me-2 text-primary"></i>Exporter un rapport
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Période du</label>
                        <input type="date" name="from" class="form-control bg-light border-0"
                               value="{{ now()->startOfMonth()->toDateString() }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Au</label>
                        <input type="date" name="to" class="form-control bg-light border-0"
                               value="{{ now()->toDateString() }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold text-muted small text-uppercase">Format</label>
                        <div class="d-flex gap-4 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format"
                                       value="pdf" id="fmt_pdf" checked>
                                <label class="form-check-label fw-semibold" for="fmt_pdf">
                                    <i class="bi bi-file-pdf text-danger me-1"></i>PDF
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format"
                                       value="excel" id="fmt_excel">
                                <label class="form-check-label fw-semibold" for="fmt_excel">
                                    <i class="bi bi-file-excel text-success me-1"></i>Excel
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-link text-muted"
                            data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">
                        <i class="bi bi-download me-2"></i>Télécharger
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══ SCRIPTS ══ --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ── Navigation entre onglets ────────────────────────────────────────────
function showTab(name, linkEl) {
    // Masquer toutes les sections
    document.querySelectorAll('.tab-section').forEach(s => s.classList.add('d-none'));
    // Afficher la section cible
    document.getElementById('tab-' + name).classList.remove('d-none');
    // Mettre à jour les liens actifs
    document.querySelectorAll('.nav-sidebar .nav-link').forEach(l => l.classList.remove('active'));
    if (linkEl) linkEl.classList.add('active');
}

// ── Remplir le modal "Encaisser" ────────────────────────────────────────
function fillEncaisserModal(btn) {
    document.getElementById('enc_sale_id').value      = btn.dataset.saleId;
    document.getElementById('enc_client').textContent = btn.dataset.client;
    document.getElementById('enc_reste_label').textContent =
        new Intl.NumberFormat('fr-FR').format(btn.dataset.reste);
    document.getElementById('enc_amount').value = btn.dataset.reste;
    document.getElementById('enc_amount').max   = btn.dataset.reste;
}

// ── Remplir le modal "Modifier dépense" ────────────────────────────────
function fillEditDepense(id, title, amount) {
    document.getElementById('editDepenseForm').action = '/expenses/' + id;
    document.getElementById('edit_dep_title').value   = title;
    document.getElementById('edit_dep_amount').value  = amount;
}

// ── Activer l'onglet selon ?tab= dans l'URL ─────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const tab = new URLSearchParams(window.location.search).get('tab') || 'kpis';
    const link = document.querySelector(`.nav-sidebar [onclick*="'${tab}'"]`);
    showTab(tab, link);

    // ── Graphique barres — évolution financière ─────────────────────────
    new Chart(document.getElementById('financeChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($chartLabels ?? []),
            datasets: [
                {
                    label: 'CA',
                    data: @json($chartCA ?? []),
                    backgroundColor: 'rgba(102,126,234,.75)',
                    borderRadius: 6,
                },
                {
                    label: 'Encaissé',
                    data: @json($chartEncaisse ?? []),
                    backgroundColor: 'rgba(34,197,94,.75)',
                    borderRadius: 6,
                },
                {
                    label: 'Dépenses',
                    data: @json($chartDepenses ?? []),
                    backgroundColor: 'rgba(245,158,11,.75)',
                    borderRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // ── Graphique camembert — statuts paiements ─────────────────────────
    new Chart(document.getElementById('paymentPie').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Payé', 'Partiel', 'Impayé'],
            datasets: [{
                data: [{{ $ventesPayees ?? 0 }}, {{ $ventesPartielles ?? 0 }}, {{ $ventesImPayees ?? 0 }}],
                backgroundColor: [
                    'rgba(34,197,94,.8)',
                    'rgba(245,158,11,.8)',
                    'rgba(239,68,68,.8)'
                ],
                borderWidth: 0,
            }]
        },
        options: {
            cutout: '65%',
            plugins: { legend: { display: false } }
        }
    });
});
</script>
@endsection