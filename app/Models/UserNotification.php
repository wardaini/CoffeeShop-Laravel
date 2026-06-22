<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'message', 'icon', 'link', 'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper statis untuk kirim notifikasi ke user tertentu
     */
    public static function send(int $userId, string $title, string $message, string $icon = '🔔', ?string $link = null): void
    {
        self::create([
            'user_id' => $userId,
            'title'   => $title,
            'message' => $message,
            'icon'    => $icon,
            'link'    => $link,
        ]);
    }

    /**
     * Kirim notifikasi ke semua user dengan role tertentu
     */
    public static function sendToRole(string $role, string $title, string $message, string $icon = '🔔', ?string $link = null): void
    {
        $users = User::where('role', $role)->where('is_active', true)->get();
        foreach ($users as $user) {
            self::send($user->id, $title, $message, $icon, $link);
        }
    }
}