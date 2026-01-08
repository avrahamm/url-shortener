<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LinkHit extends Model
{
    use HasFactory;

    protected $table = 'link_hits';
    const UPDATED_AT = null; // Disable updated_at


    protected $fillable = [
        'link_id',
        'ip',
        'user_agent'
    ];

    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class);
    }

}
