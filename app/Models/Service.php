<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name', 'description', 'key', 'secure', 'domain', 'port', 'path', 'active'
  ];

  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = [];
}
