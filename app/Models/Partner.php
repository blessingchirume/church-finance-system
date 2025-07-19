<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'commitment_amount',
        'commitment_start_date',
        'is_active'
    ];

    protected $casts = [
        'commitment_start_date' => 'date',
        'is_active' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function payments()
    {
        return $this->hasMany(PartnerPayment::class);
    }

    public function arrears()
    {
        return $this->hasMany(PartnerArrear::class);
    }

    public function getCurrentArrearsAttribute()
    {
        return $this->arrears()->where('is_settled', false)->sum('balance');
    }
}
