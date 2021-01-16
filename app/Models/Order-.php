<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Order extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $primaryKey = 'order_id';

    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id','order_number','service_type','customer_id','partner_id','layanan_id','schedule_datetime','qty','unit','amount','address','latitude','longitude','address_note','status','created_at','updated_at','deleted_at'
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

    public function getOrder($where)
    {
        # code...
        $sql = "SELECT 
            a.order_id,a.service_type as type,
            CONCAT(a.service_type,' Service ','#',a.order_number,' - ',b.name) as service_type,
            CONCAT(a.qty,' ',a.unit) as qty,
            CONCAT(DATE_FORMAT(a.schedule_datetime,'%M,%d %Y'),' - ',DATE_FORMAT(a.schedule_datetime,'%H:%i')) as schedule 
            FROM orders a 
            INNER JOIN layanan b ON a.layanan_id = b.id 
            WHERE a.customer_id='".$where['customer_id']."' AND a.status='".$where['status']."'";
        $result = collect(\DB::select($sql));
        return $result;
    }

    public function getLayanan($where)
    {
        # code...
        $sql = "SELECT id,name,price
            FROM layanan where type='".$where['type']."'";
        $result = collect(\DB::select($sql));
        return $result;
    }
}
