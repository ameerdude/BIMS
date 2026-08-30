<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['title','content','type','priority','publish_date','expiry_date','is_active','author_id'];
    protected function casts(): array { return ['publish_date'=>'date','expiry_date'=>'date','is_active'=>'boolean']; }
    public function author() { return $this->belongsTo(User::class, 'author_id'); }
    public function getTypeLabel(): string { return ucfirst($this->type); }
}
