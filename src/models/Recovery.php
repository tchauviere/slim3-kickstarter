<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    User.php
 * Date:    15/04/2019
 * Time:    15:07
 */

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Recovery extends Model
{
    protected $table = 'recovery';
    protected $fillable = ['user_id', 'token', 'expires_at'];

    /**
     * Get the role record associated with the user.
     */
    public function user()
    {
        return $this->hasOne('Models\User', 'id', 'user_id');
    }
}
