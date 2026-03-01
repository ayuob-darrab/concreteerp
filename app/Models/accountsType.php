<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class accountsType extends Model
{
    use HasFactory;
    protected $table = 'accounts_type';
    protected $fillable = ['id', 'typename', 'code'];
}
