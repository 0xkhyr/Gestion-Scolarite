<?php

namespace App\Models;

use App\Events\TeacherPaymentProcessed;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnseignPaiement extends Model
{
    protected $table = 'enseignpaiements';
    protected $primaryKey ='id_paiements';
    protected $fillable =['user_id','typepaiement','montant','statut','date_paiement','notified'];
    
    protected $dates = ['date_paiement'];
    
    protected $casts = [
        'date_paiement' => 'date',
        'montant' => 'decimal:2',
        'notified' => 'boolean',
    ];
    
    public function enseignant()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function booted()
    {
        static::created(function ($payment) {
            if ($payment->statut === 'paye' && !$payment->notified) {
                event(new TeacherPaymentProcessed($payment));
            }
        });

        static::updated(function ($payment) {
            if (
                $payment->wasChanged('statut') &&
                $payment->statut === 'paye' &&
                !$payment->notified
            ) {
                event(new TeacherPaymentProcessed($payment));
            }
        });
    }
    
    /**
     * Generate payment voucher number
     */
    public function getVoucherNumberAttribute(): string
    {
        return 'PAY-' . str_pad($this->id_paiements, 6, '0', STR_PAD_LEFT);
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
        return match($this->typepaiement) {
            'salaire' => 'Salary',
            'prime' => 'Bonus',
            'heures_supp' => 'Overtime Pay',
            'formation' => 'Training Allowance',
            'transport' => 'Transportation Allowance',
            'autre' => 'Other Payment',
            default => ucfirst($this->typepaiement),
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
