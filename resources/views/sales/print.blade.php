@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h2 class="fw-bold mb-1">Documents de Vente #{{ $sale->id }}</h2>
            <p class="text-muted">Générés le {{ $sale->created_at->format('d/m/Y à H:i') }}</p>
        </div>
        <div>
            @php
            $dashboardRoute = auth()->user()->role === 'sales_employee' ? 'dashboard.sales_employee' : 'sales.index';
            @endphp
            <a href="{{ route($dashboardRoute) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Retour au Dashboard
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <!-- TICKET DE CAISSE -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light d-flex justify-content-between align-items-center no-print">
                    <h6 class="fw-bold mb-0">Ticket de Caisse</h6>
                    <button onclick="printDiv('receipt-area')" class="btn btn-sm btn-primary">
                        <i class="bi bi-printer me-1"></i> Imprimer
                    </button>
                </div>
                <div class="card-body d-flex justify-content-center bg-secondary bg-opacity-10">
                    <div id="receipt-area" class="bg-white p-3 shadow-sm" style="width: 80mm; min-height: 100mm; font-family: 'Courier New', Courier, monospace; font-size: 12px;">
                        <div class="text-center mb-3">
                            <h5 class="fw-bold mb-1">{{ config('app.name') }}</h5>
                            <p class="mb-0 small">Tél: +237 600 00 00 00</p>
                            <p class="mb-0 small">Douala, Cameroun</p>
                            <p class="mb-0 small">NIU: P000000000000</p>
                            <p class="mb-0 small">RCCM: RC/DLA/202X/A/0000</p>
                        </div>
                        <hr class="border-dark border-dashed">
                        <div class="mb-2">
                            <span class="fw-bold">Ticket #{{ $sale->id }}</span><br>
                            <span>Date: {{ $sale->created_at->format('d/m/Y H:i') }}</span><br>
                            <span>Client: {{ $sale->client->name }}</span>
                        </div>
                        <hr class="border-dark border-dashed">
                        <table class="table table-borderless table-sm mb-0" style="font-size: 12px;">
                            <thead>
                                <tr class="border-bottom border-dark">
                                    <th class="ps-0">Articles</th>
                                    <th class="text-center">Qté</th>
                                    <th class="text-end pe-0">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->items as $item)
                                <tr>
                                    <td class="ps-0">{{ Str::limit($item->product->name, 15) }}</td>
                                    <td class="text-center">x{{ $item->quantity }}</td>
                                    <td class="text-end pe-0">{{ number_format($item->price * $item->quantity, 0, ',', ' ') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <hr class="border-dark border-dashed">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Total</span>
                            <span>{{ number_format($sale->total, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Payé</span>
                            <span>{{ number_format($sale->paid_amount ?? 0, 0, ',', ' ') }} FCFA</span>
                        </div>
                        @php $balance = $sale->total - ($sale->paid_amount ?? 0); @endphp
                        @if ($balance > 0)
                        <div class="d-flex justify-content-between fw-bold fs-6" style="color: #dc3545;">
                            <span>RESTE À PAYER</span>
                            <span>{{ number_format($balance, 0, ',', ' ') }} FCFA</span>
                        </div>
                        @endif
                        <hr class="border-dark border-dashed">
                        <div class="text-center mt-3 small">
                            <p class="mb-1">Merci de votre visite !</p>
                            <p>À bientôt.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FACTURE (Format A4) -->
        <div class="col-lg-12 mb-5">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center no-print">
                    <h6 class="fw-bold mb-0">Facture Client (A4)</h6>
                    <button onclick="printDiv('invoice-area')" class="btn btn-sm btn-primary">
                        <i class="bi bi-printer me-1"></i> Imprimer
                    </button>
                </div>
                <div class="card-body d-flex justify-content-center bg-secondary bg-opacity-10 overflow-auto">
                    <div id="invoice-area" class="bg-white p-4 shadow-sm" style="width: 210mm; min-height: 297mm; margin: auto;">
                        <!-- En-tête Facture -->
                        <div class="row mb-4">
                            <div class="col-6">
                                <h2 class="fw-bold text-uppercase text-primary mb-2">Facture</h2>
                                <p class="mb-0 fw-bold fs-5">N° FAC-{{ $sale->id }}</p>
                                <p class="text-muted">Date: {{ $sale->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-6 text-end">
                                <h4 class="fw-bold">{{ config('app.name') }}</h4>
                                <p class="small mb-0">Tél: +237 600 00 00 00</p>
                                <p class="small mb-0">Douala, Cameroun</p>
                                <p class="small mb-0">NIU: P000000000000 | RCCM: RC/DLA/202X/A/0000</p>
                            </div>
                        </div>

                        <!-- Info Client -->
                        <div class="row mb-4">
                            <div class="col-6">
                                <div class="border p-4 rounded">
                                    <h6 class="text-uppercase text-muted small fw-bold mb-2">Client</h6>
                                    <h5 class="fw-bold mb-1">{{ $sale->client->name }}</h5>
                                    <p class="mb-0">{{ $sale->client->email ?? '' }}</p>
                                    <p class="mb-0">{{ $sale->client->address ?? 'Adresse non renseignée' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Tableau Articles -->
                        <table class="table table-bordered table-sm mb-4">
                            <thead class="table-light">
                                <tr>
                                    <th>Désignation</th>
                                    <th class="text-center" style="width: 15%">Qté</th>
                                    <th class="text-end" style="width: 20%">P.U. HT</th>
                                    <th class="text-end" style="width: 20%">Total HT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->items as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">{{ number_format($item->price, 0, ',', ' ') }}</td>
                                    <td class="text-end fw-bold">{{ number_format($item->price * $item->quantity, 0, ',', ' ') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold text-uppercase pt-3">Total HT</td>
                                    <td class="text-end fw-bold fs-5 pt-3">{{ number_format($sale->total, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end text-muted small border-0 pb-0">TVA (Non applicable)</td>
                                    <td class="text-end text-muted small border-0 pb-0">0 FCFA</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold text-uppercase text-primary pt-2">Net à Payer TTC</td>
                                    <td class="text-end fw-bold fs-4 text-primary pt-2">{{ number_format($sale->total, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end text-uppercase pt-3">Montant déjà versé</td>
                                    <td class="text-end fs-5 pt-3 text-success">{{ number_format($sale->paid_amount ?? 0, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                @php $balance = $sale->total - ($sale->paid_amount ?? 0); @endphp
                                <tr class="table-light">
                                    <td colspan="3" class="text-end fw-bold text-uppercase pt-2">Solde à Payer</td>
                                    <td class="text-end fw-bold fs-4 pt-2 {{ $balance > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($balance, 0, ',', ' ') }} FCFA</td>
                                </tr>
                            </tfoot>
                        </table>

                        <!-- Conditions de règlement -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="bg-light p-3 rounded border">
                                    <h6 class="fw-bold small text-uppercase mb-2">Conditions de règlement</h6>
                                    <p class="small mb-1"><span class="fw-bold">Date limite :</span> À réception de facture</p>
                                    <p class="small mb-0"><span class="fw-bold">Moyens de paiement :</span> Espèces, Virement, Orange Money, MTN Mobile Money</p>
                                </div>
                            </div>
                        </div>

                        <!-- Pied de page -->
                        <div class="text-center pb-4 mt-5">
                            <hr class="mx-5">
                            <p class="small text-muted mb-0">Merci de votre confiance !</p>
                            <p class="small text-muted">{{ config('app.name') }} - Gestion Commerciale</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BON DE LIVRAISON -->
        <div class="col-lg-12 mb-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light d-flex justify-content-between align-items-center no-print">
                    <h6 class="fw-bold mb-0">Bon de Livraison (Déstockage)</h6>
                    <button onclick="printDiv('delivery-note-area')" class="btn btn-sm btn-dark">
                        <i class="bi bi-printer me-1"></i> Imprimer
                    </button>
                </div>
                <div class="card-body bg-secondary bg-opacity-10 d-flex justify-content-center overflow-auto">
                    <div id="delivery-note-area" class="bg-white p-5 shadow-sm" style="width: 210mm; min-height: 297mm; margin: auto;">
                        <!-- En-tête -->
                        <div class="row mb-5">
                            <div class="col-6">
                                <h3 class="fw-bold text-uppercase text-primary mb-2">Bon de Livraison</h3>
                                <p class="mb-0">Réf: <span class="fw-bold text-dark">BL-{{ $sale->created_at->format('Ymd') }}-{{ $sale->id }}</span></p>
                                <p class="text-muted small">Date: {{ $sale->created_at->format('d/m/Y') }}</p>
                            </div>
                            <div class="col-6 text-end">
                                <h4 class="fw-bold">{{ config('app.name') }}</h4>
                                <p class="small mb-0">Direction Commerciale</p>
                                <p class="small mb-0">Douala, Cameroun</p>
                                <p class="small mb-0">NIU: P000000000000</p>
                            </div>
                        </div>

                        <!-- Infos Client -->
                        <div class="row mb-5">
                            <div class="col-6">
                                <div class="border p-3 rounded">
                                    <h6 class="text-uppercase text-muted small fw-bold mb-2">Destinataire</h6>
                                    <h5 class="fw-bold mb-1">{{ $sale->client->name }}</h5>
                                    <p class="mb-0 small">{{ $sale->client->phone ?? 'Tél: N/A' }}</p>
                                    <p class="mb-0 small">{{ $sale->client->email ?? '' }}</p>
                                    <p class="mb-0 small">{{ $sale->client->address ?? '' }}</p>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                <div class="p-3">
                                    <h6 class="text-uppercase text-muted small fw-bold mb-2">Vendeur</h6>
                                    <p class="fw-bold mb-0">{{ $sale->sales_employee ? $sale->sales_employee->name : 'Administrateur' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Tableau -->
                        <table class="table table-bordered mb-4">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 10%">Réf</th>
                                    <th style="width: 50%">Désignation</th>
                                    <th class="text-center" style="width: 20%">Quantité</th>
                                    <th class="text-center" style="width: 20%">Contrôle</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->items as $item)
                                <tr>
                                    <td>{{ $item->product->id }}</td>
                                    <td>{{ $item->product->name }}</td>
                                    <td class="text-center fw-bold fs-5">{{ $item->quantity }}</td>
                                    <td></td> <!-- Case vide pour cocher lors du déstockage physique -->
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Pied de page -->
                        <div class="row mt-5 pt-5">
                            <div class="col-6 text-center">
                                <p class="fw-bold mb-5">Signature du Magasinier</p>
                                <div class="border-bottom w-50 mx-auto"></div>
                            </div>
                            <div class="col-6 text-center">
                                <p class="fw-bold mb-5">Réception Client</p>
                                <div class="border-bottom w-50 mx-auto"></div>
                            </div>
                        </div>

                        <div class="mt-5 pt-5 text-center text-muted small">
                            <p>Ce document atteste de la sortie de stock des marchandises susmentionnées.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function printDiv(divId) {
        var printContents = document.getElementById(divId).innerHTML;
        var nw = window.open('', '_blank', 'width=900,height=600');

        nw.document.write('<html><head><title>Document</title>');
        nw.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">');
        nw.document.write('<style>');
        // Le secret est ici : margin 0 retire les infos du navigateur
        nw.document.write('@page { size: auto; margin: 0; }');
        // On ajoute du padding au body pour que le texte ne colle pas au bord du papier
        nw.document.write('body { font-family: sans-serif; padding: 15mm; background-color: white !important; }');

        if (divId === 'receipt-area') {
            nw.document.write('#print-content { width: 80mm; margin: auto; font-family: "Courier New", monospace; }');
        } else {
            nw.document.write('#print-content { width: 100%; }');
        }
        nw.document.write('</style></head><body>');
        nw.document.write('<div id="print-content">' + printContents + '</div>');
        nw.document.write('</body></html>');

        nw.document.close();

        setTimeout(function() {
            nw.print();
            nw.close();
        }, 500);
    }
</script>

<style>
    /* Styles pour l'écran (Aperçu) */
    #receipt-area {
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin: auto;
    }

    #delivery-note-area {
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        background-color: white;
    }

    /* --- STYLES D'IMPRESSION --- */
    @media print {
        .no-print {
            display: none !important;
        }

        body {
            background-color: white !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* On force le conteneur d'impression à prendre toute la largeur A4 */
        #print-container {
            width: 100% !important;
            margin: 0 !important;
            padding: 10mm !important;
            /* Marge interne pour ne pas coller aux bords de l'imprimante */
        }

        /* Ajustement spécifique pour le Bon de Livraison sur A4 */
        #delivery-note-area {
            width: 100% !important;
            max-width: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        #invoice-area {
            width: 100% !important;
            max-width: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        /* Ajustement pour le Ticket (pour qu'il ne s'étale pas sur tout le A4) */
        #receipt-area {
            width: 80mm !important;
            /* On garde la largeur ticket même sur A4 */
            margin: 0 auto !important;
            box-shadow: none !important;
        }

        /* Réglage des marges de la page via le navigateur */
        @page {
            size: auto;
            margin: 15mm;
            /* Donne de l'air sur les 4 côtés du papier */
        }

        /* Forcer l'affichage des couleurs et fonds à l'impression */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>
@endsection