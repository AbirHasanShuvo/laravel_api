<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class People extends Model
{
    protected $table = "peoples";
    protected $fillable = ["name", "age", "email", "mobile", "address", "gender"];
}
