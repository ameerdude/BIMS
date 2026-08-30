<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RevenueRecord extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['or_number','category','description','payer_id','payer_name','amount','payment_date','payment_method','received_by','remarks'];
    protected function casts(): array { return ['amount'=>'decimal:2','payment_date'=>'date']; }
    public function payer() { return $this->belongsTo(Resident::class, 'payer_id'); }
    public function receiver() { return $this->belongsTo(User::class, 'received_by'); }
    public function getCategoryLabel(): string { return ucwords(str_replace('_',' ',$this->category)); }

    public static function generateOrNumber(): string
    {
        $year = date('Y');
        $count = self::whereYear('payment_date', $year)->count() + 1;
        return 'OR-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }
}
