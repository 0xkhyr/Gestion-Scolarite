<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    @php
        $arabic = new \ArPHP\I18N\Arabic();
        $ar = function($text) use ($arabic) {
            if (!$text || !preg_match('/\p{Arabic}/u', $text)) return $text;
            return $arabic->utf8Glyphs($text);
        };
    @endphp
    <title>{{ $ar(__('app.recu_paiement')) }} #{{ $payment->id_paiements }}</title>

    <style>
        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            @if(app()->getLocale() == 'ar')
            text-align: right; 
            direction: rtl;
            @else
            text-align: left;
            @endif
            color: #1f2937;
            margin: 0;
            padding: 40px;
            font-size: 14px;
        }
        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-bottom: 2px solid #3b82f6; 
            padding-bottom: 20px; 
            margin-bottom: 30px;
        }
        .school-info { font-size: 18px; font-weight: bold; color: #1e3a8a; }
        .receipt-title { font-size: 24px; font-weight: bold; color: #3b82f6; text-transform: uppercase; }
        
        .grid { width: 100%; margin-bottom: 30px; }
        .col { width: 48%; display: inline-block; vertical-align: top; }
        
        .section-title { 
            font-size: 12px; 
            font-weight: bold; 
            color: #6b7280; 
            text-transform: uppercase; 
            margin-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px;
        }
        
        .info-box { padding: 10px; background: #f9fafb; border-radius: 8px; line-height: 1.6; }
        
        .table-container { margin-top: 40px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f3f4f6; color: #4b5563; text-align: left; padding: 12px; border-bottom: 2px solid #e5e7eb; }
        @if(app()->getLocale() == 'ar')
        th { text-align: right; }
        @endif
        td { padding: 12px; border-bottom: 1px solid #e5e7eb; }
        
        .total-row { font-weight: bold; font-size: 16px; background: #eff6ff; }
        .total-row td { border-top: 2px solid #3b82f6; }
        
        .status-badge { 
            display: inline-block; 
            padding: 4px 12px; 
            border-radius: 9999px; 
            font-size: 12px; 
            font-weight: bold;
            @if($payment->statut == 'paid')
            background: #dcfce7; color: #166534;
            @elseif($payment->statut == 'pending')
            background: #fef9c3; color: #854d0e;
            @else
            background: #fee2e2; color: #991b1b;
            @endif
        }
        
        .footer { 
            margin-top: 50px; 
            text-align: center; 
            color: #94a3b8; 
            font-size: 11px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        .signatures { margin-top: 40px; }
        .sig-box { width: 200px; border-top: 1px solid #000; text-align: center; padding-top: 10px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-info">
            {{ $ar(config('app.school_name', config('app.name'))) }}<br>
            <span style="font-size: 12px; color: #6b7280;">{{ $ar(__('app.gestion_scolaire_complete')) }}</span>
        </div>
        <div class="receipt-title">{{ $ar(__('app.recu_paiement')) }}</div>
    </div>

    <div class="grid">
        <div class="col" style="float: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};">
            <div class="section-title">{{ $ar(__('app.etudiant')) }}</div>
            <div class="info-box">
                <strong>{{ $ar(($payment->etudiant->nom ?? '') . ' ' . ($payment->etudiant->prenom ?? '')) }}</strong><br>
                {{ $ar(__('app.matricule')) }}: {{ $payment->etudiant->matricule }}<br>
                {{ $ar(__('app.classe')) }}: {{ $ar($payment->etudiant->classe->nom_classe ?? 'N/A') }}
            </div>
        </div>
        <div class="col" style="float: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">
            <div class="section-title">{{ $ar(__('app.payment_details')) }}</div>
            <div class="info-box">
                {{ $ar(__('app.recu_n')) }}: #{{ $payment->id_paiements }}<br>
                {{ $ar(__('app.date')) }}: {{ \Carbon\Carbon::parse($payment->date_paiement)->format('d/m/Y') }}<br>
                {{ $ar(__('app.status')) }}: <span class="status-badge">{{ strtoupper($payment->statut) }}</span>
            </div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};">{{ $ar(__('app.description')) }}</th>
                    <th style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};">{{ $ar(__('app.type_paiement')) }}</th>
                    <th style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">{{ $ar(__('app.amount')) }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $ar(__('app.paiement_pour')) }} {{ $ar($payment->typepaye) }}</td>
                    <td>{{ $ar(ucfirst($payment->typepaye)) }}</td>
                    <td style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">{{ number_format($payment->montant, 2) }} $</td>
                </tr>
                <tr class="total-row">
                    <td colspan="2" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">{{ $ar(__('app.total_paye')) }}</td>
                    <td style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">{{ number_format($payment->montant, 2) }} $</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="signatures">
        <div style="float: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};" class="sig-box">{{ $ar(__('app.signature_parent')) }}</div>
        <div style="float: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};" class="sig-box">{{ $ar(__('app.signature_admin')) }}</div>
        <div style="clear: both;"></div>
    </div>

    <div class="footer">
        {{ $ar(__('app.generated_by')) }} {{ $ar(config('app.name')) }} - {{ now()->format('d/m/Y H:i') }}<br>
        {{ $ar(__('app.merci_paiement')) }}
    </div>
</body>
</html>
