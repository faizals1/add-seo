<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyerGroup extends Model
{
    use HasFactory;
    protected $fillable = [
        'group_code',
        'group_name',
        'type',
        'currency_code'
    ];
}
