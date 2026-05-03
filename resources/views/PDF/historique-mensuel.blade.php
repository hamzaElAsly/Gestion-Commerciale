<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rapport Mensuel — {{ $nomMois }} {{ $annee }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1e293b; background: white; }
        .page { padding: 30px 35px; }

        .header {
            display: table; width: 100%;
            border-bottom: 3px solid #1a56db;
            padding-bottom: 20px; margin-bottom: 24px;
        }
        .header-left { display: table-cell; width: 60%; vertical-align: middle; }
        .header-right { display: table-cell; width: 40%; text-align: right; vertical-align: middle; }

        .report-title { font-size: 20px; font-weight: bold; color: #0f172a; }
        .report-sub { font-size: 12px; color: #1a56db; font-weight: bold; margin-top: 4px; }
        .company { font-size: 13px; color: #64748b; }

        /* Stats boxes */
        .stats { display: table; width: 100%; margin-bottom: 24px; border-spacing: 8px; }
        .stat-box {
            display: table-cell; width: 33%;
            background: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: 6px; padding: 12px 14px; text-align: center;
        }
        .stat-value { font-size: 20px; font-weight: bold; color: #1a56db; }
        .stat-label { font-size: 10px; color: #64748b; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.04em; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead th {
            background: #1a56db; color: white;
            padding: 9px 10px; text-align: left;
            font-size: 10px; font-weight: bold; text-transform: uppercase;
        }
        tbody td { padding: 9px 10px; border-bottom: 1px solid #f1f5f9; font-size: 11px; }
        tbody tr:nth-child(even) td { background: #f8fafc; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .money { font-family: monospace; font-weight: bold; }
        .muted { color: #64748b; }

        tfoot td {
            padding: 10px; font-weight: bold;
            border-top: 2px solid #1a56db;
            font-size: 12px;
        }

        .detail-prod { font-size: 10px; color: #64748b; margin-top: 3px; }
        .detail-prod span { margin-right: 8px; }

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
            margin-top: 30px; 
            padding-top: 16px;
        }

        .page-break { page-break-after: always; }
    </style>
</head>
<body>
<div class="page">

    <!-- En-tête -->
    <div class="header">
        <div class="header-left">
            <div class="company" style="font-size:16px;font-weight:bold;color:#1a56db;">
                <img src="{{ public_path('storage/images/Logo.png') }}" alt="Logo" style="height: 80px;">
            </div>
            <div class="company">Ste. CHARRAK TECHNOLOGIE</div>
            <div class="company" style="margin-top:6px;">Imprimé le {{ now()->format('d/m/Y') }}</div>{{--  à H:i --}}
        </div>
        <div class="header-right">
            <div class="report-title">Rapport Mensuel des Services</div>
            <div class="report-sub">{{ ucfirst($nomMois) }} {{ $annee }}</div>
        </div>
        
    </div>

    <!-- Statistiques -->
    <div class="stats">
        <div class="stat-box">
            <div class="stat-value">{{ $historiques->count() }}</div>
            <div class="stat-label">Services réalisés</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" style="font-size:16px;">{{ number_format($totalMois, 2) }}</div>
            <div class="stat-label">CA Total (MAD)</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ $historiques->unique('id_client')->count() }}</div>
            <div class="stat-label">Clients servis</div>
        </div>
    </div>

    <!-- Tableau récapitulatif -->
<table>
    <thead>
        <tr>
            <th style="width:5%">#</th>
            <th style="width:18%">Client</th>
            <th style="width:10%">Date</th>
            <th style="width:35%">Produits utilisés</th>
            <th style="width:15%">Remarque</th>
            <th style="width:10%">Frais service</th>
            <th style="width:12%;text-align:right">Montant</th>
            <th style="width:5%;text-align:center">Statut</th>
        </tr>
    </thead>

    <tbody>
        @forelse($historiques as $h)
        <tr>
            <td class="muted">#{{ $h->id_historique }}</td>

            <td style="font-weight:600;">
                {{ $h->client->nom ?? 'N/A' }}
            </td>

            <td class="muted">
                {{ $h->date_service->format('d/m/Y') }}
            </td>

            <!-- ✅ PRODUITS -->
            <td>
                @foreach($h->details as $d)
                    <span style="font-size:10px; background:#f1f5f9; padding:2px 6px; border-radius:3px; margin-right:3px; margin-bottom:2px; display:inline-block;">
                        {{ $d->produit->nom_produit ?? '?' }} ×{{ $d->quantite_utilisee }}
                    </span>
                @endforeach
            </td>

            <!-- ✅ REMARQUE -->
            <td class="muted" style="font-size:10px;">
                {{ $h->remarque ? \Illuminate\Support\Str::limit($h->remarque, 40) : '—' }}
            </td>

            <!-- ✅ CHARGES -->
            <td class="muted text-right">
                {{ number_format($h->charges, 2) }} MAD
            </td>

            <!-- ✅ MONTANT -->
            <td class="text-right money" style="color:#1a56db;">
                {{ number_format($h->montant_total, 2) }} MAD
            </td>

            <!-- ✅ STATUT -->
            <td class="text-center" style="font-size:9px;font-weight:bold;color:
                {{ $h->statut === 'termine' ? '#059669' : ($h->statut === 'annule' ? '#dc2626' : '#d97706') }}">
                {{ $h->statut_label }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" style="text-align:center;padding:20px;color:#64748b;">
                Aucun service pour ce mois.
            </td>
        </tr>
        @endforelse
    </tbody>

    @if($historiques->count() > 0)
    <tfoot>
        <tr>
            <td colspan="6" class="text-right">
                TOTAL {{ strtoupper($nomMois) }} {{ $annee }} :
            </td>
            <td class="text-right money" style="color:#1a56db;font-size:14px;">
                {{ number_format($totalMois, 2) }} MAD
            </td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

    <!-- Récapitulatif par client -->
    @if($historiques->count() > 0)
    <div style="margin-top: 20px;">
        <div style="font-size: 13px; font-weight: bold; color: #0f172a; margin-bottom: 10px; padding-bottom: 6px; border-bottom: 1px solid #e2e8f0;">
            Récapitulatif par Client
        </div>
        <table>
            <thead>
                <tr>
                    <th>Client</th>
                    <th class="text-center">Nombre de services</th>
                    <th class="text-right">Montant total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($historiques->groupBy('id_client') as $clientId => $services)
                <tr>
                    <td style="font-weight:600;">{{ $services->first()->client->nom ?? 'N/A' }}</td>
                    <td class="text-center">{{ $services->count() }}</td>
                    <td class="text-right money" style="color:#1a56db;">{{ number_format($services->sum('montant_total'), 2) }} MAD</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <div>GestPro — Gestion Commerciale Professionnelle | Rapport généré automatiquement</div>
        <div>Ce document est confidentiel — {{ ucfirst($nomMois) }} {{ $annee }}</div>
    </div>

</div>
</body>
</html>