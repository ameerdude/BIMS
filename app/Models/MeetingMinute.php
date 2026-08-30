<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeetingMinute extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['meeting_number','type','meeting_date','start_time','end_time','venue','agenda','minutes_content','resolutions','attendees','recorded_by'];
    protected function casts(): array { return ['meeting_date'=>'date','start_time'=>'datetime','end_time'=>'datetime']; }
    public function recorder() { return $this->belongsTo(User::class, 'recorded_by'); }
    public static function generateNumber(): string { $y=date('Y'); $c=self::whereYear('meeting_date',$y)->count()+1; return "MN-{$y}-".str_pad($c,3,'0',STR_PAD_LEFT); }
}
