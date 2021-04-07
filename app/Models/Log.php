<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'serviceId', 'path', 'method', 'statusCode', 'requestTime', 'responseTime'
  ];

  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = [];
}
