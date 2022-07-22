<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'needs_email',
        'last_email_at'
    ];


    public function responses(){
        return $this->hasMany(Response::class, 'job_id', 'id');
    }
}
