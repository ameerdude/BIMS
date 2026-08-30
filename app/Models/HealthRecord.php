<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HealthRecord extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['resident_id','record_type','title','description','record_date','provider','result','next_schedule','recorded_by'];
    protected function casts(): array { return ['record_date'=>'date','next_schedule'=>'date']; }
    public function resident() { return $this->belongsTo(Resident::class); }
    public function recorder() { return $this->belongsTo(User::class, 'recorded_by'); }
    public function getTypeLabel(): string { return match($this->record_type) { 'vaccination'=>'Vaccination','medical_referral'=>'Medical Referral','health_program'=>'Health Program','checkup'=>'Checkup',default=>'Other' }; }
}
