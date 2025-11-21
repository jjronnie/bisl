<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory, SoftDeletes; 
    
   protected $fillable = [
        'user_id',
         'tier',
        'member_no',
        'name',
        'date_of_birth',
        'nationality',
        'gender',
        'marital_status',
        'national_id_number',
        'passport_number',
        'avatar',
        'phone1',
        'phone2',
        'address',
     
    ];

    protected $casts = [
       
        'date_of_birth' => 'date',
    ];

  
    public function user()
{
    return $this->belongsTo(User::class);
}

public function savingsAccount()
{
    return $this->hasOne(SavingsAccount::class);
}

public function transactions()
{
    return $this->hasMany(Transaction::class);
}



 


    public function employmentDetail()
    {
        return $this->hasOne(EmploymentDetail::class);
    }

    public function emergencyContact()
    {
        return $this->hasOne(EmergencyContact::class);
    }



    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}