<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_type',
        'user_id',
        'user_name',
        'message',
        'attachments',
        'is_internal'
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_internal' => 'boolean',
    ];

    /**
     * علاقة مع التذكرة
     */
    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    /**
     * علاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * هل الرد من فريق الدعم
     */
    public function isFromSupport()
    {
        return $this->user_type === 'support';
    }

    /**
     * هل الرد من العميل
     */
    public function isFromCustomer()
    {
        return $this->user_type === 'customer';
    }
}
