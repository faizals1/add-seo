<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $fillable = ['company_name','company_code','address1','address2','country','state','city','pincode','contact_person','contact_mobile','licence_valid_till','blocked','phone_no'];

    public function users() {
        return $this->belongsToMany(User::class,'company_user_mappings');  
    }

    public function countryname()
    {
        return $this->hasOne(Country::class, 'id', 'country');
    }
    public function statename()
    {
        return $this->hasOne(State::class, 'id', 'state');
    }
    public function cityname()
    {
        return $this->hasOne(City::class, 'id', 'city');
    }
}
