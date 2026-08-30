<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ServiceRequest extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['request_number','resident_id','requester_name','requester_contact','category','subject','description','location','priority','status','assigned_to','resolution_notes','resolved_at','created_by'];
    protected function casts(): array { return ['resolved_at'=>'date']; }
    public function resident() { return $this->belongsTo(Resident::class); }
    public function assignee() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public static function generateNumber(): string { $y=date('Y'); $c=self::whereYear('created_at',$y)->count()+1; return "SR-{$y}-".str_pad($c,4,'0',STR_PAD_LEFT); }
    public function getCategoryLabel(): string { return ucwords(str_replace('_',' ',$this->category)); }
    public function getStatusLabel(): string { return ucwords(str_replace('_',' ',$this->status)); }
}
