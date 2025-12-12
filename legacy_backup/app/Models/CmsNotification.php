<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsNotification extends Model
{
    use HasFactory;

    protected $table = 'cms_notifications'; // tabla asociada

    protected $fillable = [
        'content',
        'id_cms_users',
        'url',
        'is_read'
    ];
}
