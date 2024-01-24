<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    protected $table = "books";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        "user_id",
        "table_id",
        "booked_date",
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function table(): BelongsTo {
        return $this->belongsTo(Table::class, "table_id", "id");
    }
}
