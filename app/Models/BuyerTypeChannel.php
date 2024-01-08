<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Company;


class BuyerTypeChannel extends Model
{
    use HasFactory;
    protected $table='buyer_type_channel';
    protected $fillable = ['company_id','name'];

    public function companyname()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

}

