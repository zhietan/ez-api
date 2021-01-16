<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Content extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $primaryKey = 'id';

    protected $keyType = 'int';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'value',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','otp'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getSyartaKetentuan()
    {
        # code...
        $sql = "SELECT 
            * 
            FROM content
            WHERE id='2'";
        $result = collect(\DB::select($sql));
        return $result;
    }

    public function getPrivasi()
    {
        # code...
        $sql = "SELECT 
            * 
            FROM content
            WHERE id='1'";
        $result = collect(\DB::select($sql));
        return $result;
    }

    public function getAboutUS()
    {
        # code...
        $sql = "SELECT 
            * 
            FROM content
            WHERE id='3'";
        $result = collect(\DB::select($sql));
        return $result;
    }
	
	public function getBooking1()
    {
        # code...
        $sql = "SELECT * FROM booking_list";
        $result = collect(\DB::select($sql));
        return $result;
    }
}
