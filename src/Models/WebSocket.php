<?php

namespace RiseTechApps\WebSocket\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use RiseTechApps\HasUuid\Traits\HasUuid\HasUuid;

class WebSocket extends Model
{
    protected $table = "websocket";

    use HasFactory, SoftDeletes, Notifiable;
    use Notifiable, HasUuid;

    protected $fillable = [
        'key',
        'secret',
        'max_connections',
        'enable_client_messages',
        'enabled',
        'max_backend_events_per_sec',
        'max_client_events_per_sec',
        'max_read_req_per_sec',
        'webhooks',
        'max_presence_members_per_channel',
        'max_presence_member_size_in_kb',
        'max_channel_name_length',
        'max_event_channels_at_once',
        'max_event_name_length',
        'max_event_payload_in_kb',
        'max_event_batch_size',
        'enable_user_authentication',
    ];

    public function getConnectionName()
    {
        return config('tenancy.database.central');
    }
}
