<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Vente #{{ str_pad($vente->id_vente, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px; color: #1e293b; background: white;
        }
        .page { padding: 40px 44px; }

        /* ─── En-tête ─── */
        .header { display:table; width:100%; padding-bottom:20px; margin-bottom:28px;
                  border-bottom:3px solid #2563eb; }
        .header-left  { display:table-cell; width:55%; vertical-align:middle; }
        .header-right { display:table-cell; width:45%; vertical-align:middle; text-align:right; }

        .co-name  { font-size:22px; font-weight:bold; color:#2563eb; }
        .co-sub   { font-size:11px; color:#64748b; margin-top:2px; }
        .co-coord { font-size:11px; color:#64748b; margin-top:6px; line-height:1.7; }

        .doc-title  { font-size:26px; font-weight:bold; color:#0f172a; }
        .doc-number { font-size:13px; color:#2563eb; font-weight:bold; margin-top:4px; }
        .doc-date   { font-size:11px; color:#64748b; margin-top:6px; }

        /* ─── Section client ─── */
        .client-box {
            background:#f8fafc; border:1px solid #e2e8f0;
            border-radius:7px; padding:14px 18px; margin-bottom:24px;
        }
        .section-label {
            font-size:9.5px; text-transform:uppercase; letter-spacing:.07em;
            color:#94a3b8; font-weight:bold; margin-bottom:6px;
        }
        .client-name { font-size:15px; font-weight:bold; color:#0f172a; }

        /* ─── Table produits ─── */
        table { width:100%; border-collapse:collapse; margin-bottom:22px; }

        .tbl-head th {
            background:#2563eb; color:white;
            padding:9px 12px; text-align:left;
            font-size:10px; font-weight:bold; text-transform:uppercase;
        }
        .tbl-head th.r { text-align:right; }
        .tbl-head th.c { text-align:center; }

        tbody td {
            padding:10px 12px;
            border-bottom:1px solid #f1f5f9;
            font-size:11.5px;
        }
        tbody tr:nth-child(even) td { background:#f8fafc; }
        .td-r { text-align:right; font-family:monospace; }
        .td-c { text-align:center; }

        /* ─── Totaux ─── */
        .totaux-wrap { display:table; width:100%; }
        .totaux-left  { display:table-cell; width:55%; vertical-align:top; padding-right:20px; }
        .totaux-right { display:table-cell; width:45%; vertical-align:top; }

        .total-row {
            display:table; width:100%;
            padding:7px 0; border-bottom:1px solid #e2e8f0;
        }
        .total-lbl { display:table-cell; color:#64748b; font-size:12px; }
        .total-val { display:table-cell; text-align:right; font-size:12px; font-weight:bold;
                     font-family:monospace; }

        .total-final {
            background:#2563eb; color:white;
            padding:12px 14px; border-radius:7px;
            display:table; width:100%; margin-top:8px;
        }
        .tf-lbl { display:table-cell; font-size:13px; font-weight:bold; }
        .tf-val { display:table-cell; text-align:right; font-size:19px; font-weight:bold;
                  font-family:monospace; }

        /* ─── Footer ─── */
        .footer {
            margin-top:36px; border-top:1px solid #e2e8f0;
            padding-top:14px;
        }
        .footer-grid { display:table; width:100%; }
        .footer-col  { display:table-cell; width:33%; font-size:10px;
                       color:#94a3b8; vertical-align:top; padding:0 4px; }
        .footer-col .fc-title { font-weight:bold; color:#64748b;
                                 margin-bottom:4px; font-size:10.5px; }

        .footer-bottom {
            text-align:center; margin-top:14px;
            font-size:9.5px; color:#cbd5e1;
        }

        /* ─── Mention aucun produit ─── */
        .no-prod {
            text-align:center; padding:20px; color:#94a3b8;
            font-style:italic; font-size:11.5px;
            background:#f8fafc; border-radius:6px;
            margin-bottom:22px;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- ── En-tête ── --}}
    {{-- <div class="header">
        <div class="header-left">
            <div class="co-name">{{ $entreprise['logo_texte'] }}</div>
            <div class="co-sub">{{ $entreprise['slogan'] }}</div>
            <div class="co-coord">
                📍 {{ $entreprise['adresse'] }}, {{ $entreprise['ville'] }}<br>
                📞 {{ $entreprise['telephone'] }} &nbsp;|&nbsp;
                ✉ {{ $entreprise['email'] }}<br>
                @if($entreprise['ice'])ICE : {{ $entreprise['ice'] }}@endif
            </div>
        </div>
        <div class="header-right">
            <div class="doc-title">BON DE VENTE</div>
            <div class="doc-number">#{{ str_pad($vente->id_vente, 4, '0', STR_PAD_LEFT) }}</div>
            <div class="doc-date">
                Date : {{ $vente->created_at->format('d/m/Y à H:i') }}<br>
                Imprimé le : {{ now()->format('d/m/Y') }}
            </div>
        </div>
    </div> --}}
    <div class="header">
        <div class="header-left">
            <div class="company-name">
                <img src="{{ public_path('storage/images/Logo.png') }}" alt="Logo" style="height: 80px;">
            </div>
            <div class="company-sub">Ste. CHARRAK TECHNOLOGY</div>
            <div class="company-sub" style="margin-top: 8px;">📍 Fès, Maroc</div>
            <div class="company-sub">📞 +212 622-390028</div>
        </div>
        <div class="header-right">
            <div class="invoice-title">BON DE VENTE</div>
            <div class="invoice-number">#{{ str_pad($vente->id_vente, 6, '0', STR_PAD_LEFT) }}</div>
            <div style="margin-top: 8px; font-size: 12px; color: #64748b;">
                Date : {{ $vente->updated_at->format('d/m/Y') }} {{--  à H:i --}}
            </div>
            
        </div>
    </div>

    {{-- ── Client ── --}}
    <div class="client-box">
        <div class="section-label">Facturé à</div>
        <div class="client-name">{{ $vente->nom_client }}</div>
    </div>

    {{-- ── Tableau produits ── --}}
    @if($vente->details->count())
    <table>
        <thead class="tbl-head">
            <tr>
                <th style="width:5%">#</th>
                <th style="width:50%">Désignation</th>
                <th class="c" style="width:12%">Qté</th>
                <th class="r" style="width:16%">P.U. (MAD)</th>
                <th class="r" style="width:17%">Total (MAD)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vente->details as $i => $d)
            <tr>
                <td style="color:#94a3b8;">{{ $i + 1 }}</td>
                <td style="font-weight:600;">{{ $d->nom_produit }}</td>
                <td class="td-c">{{ $d->quantite }}</td>
                <td class="td-r">{{ number_format($d->prix_vente, 2) }}</td>
                <td class="td-r" style="font-weight:bold;">{{ number_format($d->prix_total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-prod">— Vente sans produits — charges uniquement —</div>
    @endif

    {{-- ── Totaux ── --}}
    <div class="totaux-wrap">
        <div class="totaux-left">
            {{-- mention TVA --}}
            <div style="font-size:10.5px;color:#94a3b8;margin-top:4px;">
                {{-- {{ $entreprise['tva_mention'] }} --}} 0
            </div>
        </div>
        <div class="totaux-right">
            <div class="total-row">
                <div class="total-lbl">Sous-total produits</div>
                <div class="total-val">{{ number_format($vente->details->sum('prix_total'), 2) }}</div>
            </div>
            <div class="total-row">
                <div class="total-lbl">Frais / Charges</div>
                <div class="total-val">+ {{ number_format($vente->charges, 2) }}</div>
            </div>
            <div class="total-final">
                <div class="tf-lbl">TOTAL À PAYER</div>
                <div class="tf-val">{{ number_format($vente->montant_total, 2) }} MAD</div>
            </div>
        </div>
    </div>

    {{-- ── Footer ── --}}
    {{-- <div class="footer">
        <div class="footer-grid">
            <div class="footer-col">
                <div class="fc-title">Coordonnées</div>
                {{ $entreprise['adresse'] }}<br>
                {{ $entreprise['ville'] }} {{ $entreprise['code_postal'] }}<br>
                {{ $entreprise['pays'] }}
            </div>
            <div class="footer-col">
                <div class="fc-title">Contact</div>
                Tél : {{ $entreprise['telephone'] }}<br>
                Email : {{ $entreprise['email'] }}<br>
                Web : {{ $entreprise['site_web'] }}
            </div>
            <div class="footer-col">
                <div class="fc-title">Informations légales</div>
                @if($entreprise['ice'])ICE : {{ $entreprise['ice'] }}<br>@endif
                @if($entreprise['if_fiscal'])IF : {{ $entreprise['if_fiscal'] }}<br>@endif
                @if($entreprise['rc'])RC : {{ $entreprise['rc'] }}@endif
            </div>
        </div>
        <div class="footer-bottom">
            {{ $entreprise['pied_page_facture'] }}
            &nbsp;—&nbsp; {{ $entreprise['nom'] }} &copy; {{ now()->year }}
        </div>
    </div> --}}
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