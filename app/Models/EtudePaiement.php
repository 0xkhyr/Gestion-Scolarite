<?php

namespace App\Models;

use App\Events\StudentPaymentReceived;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EtudePaiement extends Model
{
    protected $table = 'etudepaiements';
    protected $primaryKey ='id_paiements';
    protected $fillable =['id_etudiant','typepaye','montant','statut','date_paiement'];
    
    protected $dates = ['date_paiement'];
    
    protected $casts = [
        'date_paiement' => 'date',
        'montant' => 'decimal:2',
    ];
    
    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class, 'id_etudiant');
    }

    protected static function booted()
    {
        static::created(function ($payment) {
            // Check if status is Paid or if we notify for all new entries
            if ($payment->statut === 'Payé' || $payment->statut === 'Complet') {
                event(new StudentPaymentReceived($payment));
            }
        });

        static::updated(function ($payment) {
            // If status changed to Paid
            if ($payment->isDirty('statut') && ($payment->statut === 'Payé' || $payment->statut === 'Complet')) {
                event(new StudentPaymentReceived($payment));
            }
        });
    }
    
    /**
     * Generate payment receipt number
     */
    public function getReceiptNumberAttribute(): string
    {
        return 'REC-' . str_pad($this->id_paiements, 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get formatted payment amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format($this->montant, 2);
    }
    
    /**
     * Get payment type label
     */
    public function getTypeLabel(): string
    {
        return match($this->typepaye) {
            'scolarite' => 'Tuition Fee',
            'inscription' => 'Registration Fee',
            'examen' => 'Exam Fee',
            'uniforme' => 'Uniform',
            'transport' => 'Transportation',
            'cantine' => 'Cafeteria',
            'autre' => 'Other',
            default => ucfirst($this->typepaye),
        };
    }
    
    /**
     * Get payment status badge
     */
    public function getStatusBadge(): string
    {
        return match($this->statut) {
            'paye' => '<span class="badge-success">Paid</span>',
            'non_paye' => '<span class="badge-warning">Pending</span>',
            'partiel' => '<span class="badge-info">Partial</span>',
            default => ucfirst($this->statut),
        };
    }
    
    use HasFactory;
}
