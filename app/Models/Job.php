<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    /** @use HasFactory<\Database\Factories\JobFactory> */
    use HasFactory;
    protected $table = 'jobs_apps';
   protected $fillable = ['name',  'recruteur_id', 'description', 'location', 'salary', 'company'];

   public function user(){
    return $this->belongsTo(User::class);
   }
 
}
 