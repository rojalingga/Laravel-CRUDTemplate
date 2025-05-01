<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Biodata extends Model
{
    use HasFactory;

    protected $table = 'data_diri';
    public $timestamps = false;

    protected $fillable = [
        'nama',
        'jenis_kelamin',
        'tgl_lahir',
        'foto',
    ];   
}
