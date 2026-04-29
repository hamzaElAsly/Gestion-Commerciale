<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Facture #{{ $historique->id_historique }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #1e293b;
            background: white;
            padding: 0;
        }

        .page {
            padding: 40px;
            min-height: 100vh;
        }

        /* Header */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 40px;
            border-bottom: 3px solid #1a56db;
            padding-bottom: 24px;
        }

        .header-left { display: table-cell; width: 50%; vertical-align: top; }
        .header-right { display: table-cell; width: 50%; vertical-align: top; text-align: right; }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #1a56db;
            margin-bottom: 4px;
        }

        .company-sub { font-size: 12px; color: #64748b; }

        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .invoice-number { color: #1a56db; font-size: 14px; font-weight: bold; }

        /* Info section */
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            gap: 20px;
        }

        .info-box {
            display: table-cell;
            width: 48%;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            vertical-align: top;
        }

        .info-box-right {
            display: table-cell;
            width: 48%;
            text-align: right;
            vertical-align: top;
            padding-left: 20px;
        }

        .info-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .info-value { font-weight: bold; font-size: 14px; color: #0f172a; }
        .info-sub { font-size: 11px; color: #64748b; margin-top: 2px; }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }

        .table-products thead th {
            background: #1a56db;
            color: white;
            padding: 10px 14px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .table-products tbody td {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 12px;
        }

        .table-products tbody tr:nth-child(even) td { background: #f8fafc; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Total */
        .total-section {
            margin-left: auto;
            width: 40%;
            margin-bottom: 30px;
        }

        .total-row {
            display: table;
            width: 100%;
            padding: 6px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .total-label { display: table-cell; color: #64748b; font-size: 12px; }
        .total-value { display: table-cell; text-align: right; font-size: 12px; font-weight: bold; }

        .total-final {
            background: #1a56db;
            color: white;
            padding: 12px 16px;
            border-radius: 8px;
            display: table;
            width: 100%;
            margin-top: 8px;
        }

        .total-final .total-label { color: rgba(255,255,255,0.8); font-size: 13px; }
        .total-final .total-value { color: white; font-size: 18px; font-weight: bold; }

        /* Remarks */
        .remarks {
            background: #fffbeb;
            border: 1px solid #fbbf24;
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 30px;
            font-size: 12px;
        }

        .remarks-title { font-weight: bold; color: #92400e; margin-bottom: 4px; }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 80px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 2px solid #e2e8f0;
            padding-top: 16px;
        }

        /* Status badge */
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }

        .status-termine { background: #d1fae5; color: #065f46; }
        .status-en_cours { background: #fef3c7; color: #92400e; }
        .status-annule { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
<div class="page">

    <!-- En-tête -->
    <div class="header">
        <div class="header-left">
           <div class="company-name">
    <img src="{{ public_path('storage/images/Logo.png') }}" alt="Logo" style="height: 80px;">
        </div>
            <div class="company-sub">Ste. CHARRAK TECHNOLOGIE</div>
            <div class="company-sub" style="margin-top: 8px;">📍 Fès, Maroc</div>
            <div class="company-sub">📞 +212 622-390028</div>
        </div>
        <div class="header-right">
            <div class="invoice-title">FACTURE</div>
            <div class="invoice-number">#{{ str_pad($historique->id_historique, 6, '0', STR_PAD_LEFT) }}</div>
            <div style="margin-top: 8px; font-size: 12px; color: #64748b;">
                Date : {{ $historique->date_service->format('d/m/Y') }} {{--  à H:i --}}
            </div>
            <div style="margin-top: 6px;">
                <span class="status-badge status-{{ $historique->statut }}">
                    {{ $historique->statut_label }}
                </span>
            </div>
        </div>
    </div>

    <!-- Informations client / service -->
    <div style="display: table; width: 100%; margin-bottom: 30px;">
        <div style="display: table-cell; width: 50%; vertical-align: top;">
            <div class="info-label">Facturé à</div>
            <div class="info-value">{{ $historique->client->nom }}</div>
            @if($historique->client->telephone)
                <div class="info-sub">📞 {{ $historique->client->telephone }}</div>
            @endif
            @if($historique->client->adresse)
                <div class="info-sub">📍 {{ $historique->client->adresse }}</div>
            @endif
        </div>
        <div style="display: table-cell; width: 50%; vertical-align: top; padding-left: 30px;">
            <div class="info-label">Détails de la facture</div>
            <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">
                N° ICE : <strong style="color: #0f172a;">{{ str_pad($historique->client->ICE, 10, '0', STR_PAD_LEFT) }}</strong>
            </div>
            {{-- <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">
                Date service : <strong style="color: #0f172a;">{{ $historique->date_service->format('d/m/Y') }}</strong>
            </div>
            <div style="font-size: 12px; color: #64748b;">
                Imprimé le : <strong style="color: #0f172a;">{{ now()->format('d/m/Y à H:i') }}</strong>
            </div> --}}
        </div>
    </div>

    <!-- Séparateur -->
    <div style="border-top: 1px solid #e2e8f0; margin-bottom: 24px;"></div>

    <!-- Tableau des produits -->
    <table class="table-products">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 40%;">Produit / Service</th>
                {{-- <th style="width: 20%;">Catégorie</th> --}}
                <th style="width: 10%; text-align: center;">Qté</th>
                <th style="width: 12%; text-align: right;">Prix Unit.</th>
                <th style="width: 13%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($historique->details as $idx => $detail)
            <tr>
                <td style="color: #94a3b8;">{{ $idx + 1 }}</td>
                <td style="font-weight: 600;">{{ $detail->produit->nom_produit ?? 'N/A' }}</td>
                {{-- <td style="color: #64748b;">{{ $detail->produit->categorie->nom_categorie ?? '—' }}</td> --}}
                <td style="text-align: center; font-weight: bold;">{{ $detail->quantite_utilisee }}</td>
                <td style="text-align: right; font-family: monospace;">{{ number_format($detail->prix_unitaire, 2) }} MAD</td>
                <td style="text-align: right; font-family: monospace; font-weight: bold;">{{ number_format($detail->prix_total, 2) }} MAD</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Total -->
    <div style="display: table; width: 100%;">
        <div style="display: table-cell; width: 55%; vertical-align: top;">
            @if($historique->remarque)
            <div class="remarks">
                <div class="remarks-title">💬 Remarque :</div>
                {{ $historique->remarque }}
            </div>
            @endif
        </div>
        <div style="display: table-cell; width: 45%; vertical-align: top; padding-left: 20px;">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px;">
                <div style="display: table; width: 100%; padding: 6px 0; border-bottom: 1px solid #e2e8f0; margin-bottom: 6px;">
                    <div style="display: table-cell; color: #64748b; font-size: 12px;">Sous-total :</div>
                    <div style="display: table-cell; text-align: right; font-size: 12px; font-weight: bold; font-family: monospace;">{{ number_format($historique->montant_total, 2) }} MAD</div>
                </div>
                <div style="display: table; width: 100%; padding: 6px 0; border-bottom: 1px solid #e2e8f0; margin-bottom: 6px;">
                    <div style="display: table-cell; color: #64748b; font-size: 12px;">TVA (0%) :</div>
                    <div style="display: table-cell; text-align: right; font-size: 12px; font-family: monospace;">0.00 MAD</div>
                </div>
                <div style="background: #1a56db; color: white; padding: 12px; border-radius: 6px; display: table; width: 100%; margin-top: 8px;">
                    <div style="display: table-cell; font-size: 13px; font-weight: bold;">TOTAL À PAYER</div>
                    <div style="display: table-cell; text-align: right; font-size: 18px; font-weight: bold; font-family: monospace;">{{ number_format($historique->montant_total, 2) }} MAD</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pied de page -->
    <div class="footer" style="margin-top: 40px;">
        <div style="margin-top: 4px;">
            1 RUE 1 HY SIDI HADI ZOUAGHA RCE NOUR MAG 2 30000 SECTEUR 0502 FES <br>
                **TEL 06 22 39 00 28***IF68334179***ICE003778507000061 <br>
                        COMPTE BANQUER 011270000023210000549604
    </div>

    </div>

</div>
</body>
</html>