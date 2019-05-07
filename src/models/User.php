<?php
/**
 * Created by PhpStorm.
 * User: Thibaud
 * Date: 15/04/2019
 * Time: 15:07
 */

namespace Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['firstname', 'lastname', 'email','password', 'role_id'];

    /**
     * Get the role record associated with the user.
     */
    public function role()
    {
        return $this->hasOne('Models\Role', 'id', 'role_id');
    }
}
