<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    Role.php
 * Date:    15/04/2019
 * Time:    15:07
 */

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $fillable = ['name', 'description'];

    /**
     * Get the role record associated with the user.
     */
    public function users()
    {
        return $this->hasMany('Models\User', 'role_id', 'id');
    }
}
