<?php

namespace App\Entities;

use CodeIgniter\Shield\Entities\User;


/**
 * @property int $tenant_id
 */
class UserEntity extends User
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];
}
