<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Driver;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderNotificationMail;

class NotificationService
{
    protected $fcmServerKey;
    protected $fcmUrl;

    public function __construct()
    {
        $this->fcmServerKey = config('services.fcm.server_key');
        $this->fcmUrl = 'https://fcm.googleapis.com/fcm/send';
    }

    /**
     * Send order notification to driver
     */
    public function sendOrderNotification($driver, $order, $type = 'new_order')
    {
        try {
            $notification = $this->createNotification($driver, $order, $type);

            // Send push notification
            $this->sendPushNotification($driver, $notification);

            // Send email notification (optional)
            if ($driver->email_notifications_enabled) {
                $this->sendEmailNotification($driver, $notification);
            }

            // Send SMS notification (optional)
            if ($driver->sms_notifications_enabled) {
                $this->sendSMSNotification($driver, $notification);
            }

            return [
                'success' => true,
                'message' => 'Notification sent successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Send Order Notification Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send order status notification
     */
    public function sendOrderStatusNotification($recipient, $order, $status)
    {
        try {
            $notification = $this->createStatusNotification($recipient, $order, $status);

            // Send push notification
            $this->sendPushNotification($recipient, $notification);

            // Send email if enabled
            if ($recipient->email_notifications_enabled ?? true) {
                $this->sendEmailNotification($recipient, $notification);
            }

            return [
                'success' => true,
                'message' => 'Status notification sent successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Send Status Notification Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send cancellation notification
     */
    public function sendCancellationNotification($recipient, $order)
    {
        try {
            $notification = $this->createCancellationNotification($recipient, $order);

            // Send push notification
            $this->sendPushNotification($recipient, $notification);

            // Send email if enabled
            if ($recipient->email_notifications_enabled ?? true) {
                $this->sendEmailNotification($recipient, $notification);
            }

            return [
                'success' => true,
                'message' => 'Cancellation notification sent successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Send Cancellation Notification Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send push notification via FCM
     */
    protected function sendPushNotification($recipient, $notification)
    {
        if (!$recipient->fcm_token) {
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->fcmServerKey,
                'Content-Type' => 'application/json',
            ])->post($this->fcmUrl, [
                'to' => $recipient->fcm_token,
                'notification' => [
                    'title' => $notification->title,
                    'body' => $notification->message,
                    'icon' => 'ic_notification',
                    'sound' => 'default',
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                ],
                'data' => [
                    'notification_id' => $notification->id,
                    'order_id' => $notification->order_id,
                    'type' => $notification->type,
                    'created_at' => $notification->created_at->toISOString()
                ]
            ]);

            if ($response->successful()) {
                $notification->update([
                    'push_sent_at' => now(),
                    'push_status' => 'sent'
                ]);
                return true;
            }

            Log::error('FCM Push Notification Failed: ' . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error('FCM Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email notification
     */
    protected function sendEmailNotification($recipient, $notification)
    {
        try {
            if (!$recipient->email) {
                return false;
            }

            Mail::to($recipient->email)->send(new OrderNotificationMail($notification));

            $notification->update([
                'email_sent_at' => now(),
                'email_status' => 'sent'
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Email Notification Error: ' . $e->getMessage());
            $notification->update(['email_status' => 'failed']);
            return false;
        }
    }

    /**
     * Send SMS notification
     */
    protected function sendSMSNotification($recipient, $notification)
    {
        try {
            if (!$recipient->phone) {
                return false;
            }

            // Example using Twilio or other SMS service
            $smsService = config('services.sms.provider', 'twilio');
            
            switch ($smsService) {
                case 'twilio':
                    return $this->sendTwilioSMS($recipient, $notification);
                case 'nexmo':
                    return $this->sendNexmoSMS($recipient, $notification);
                default:
                    return false;
            }

        } catch (\Exception $e) {
            Log::error('SMS Notification Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send SMS via Twilio
     */
    protected function sendTwilioSMS($recipient, $notification)
    {
        try {
            $accountSid = config('services.twilio.account_sid');
            $authToken = config('services.twilio.auth_token');
            $fromNumber = config('services.twilio.from_number');

            $response = Http::withBasicAuth($accountSid, $authToken)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json", [
                    'From' => $fromNumber,
                    'To' => $recipient->phone,
                    'Body' => $notification->message
                ]);

            if ($response->successful()) {
                $notification->update([
                    'sms_sent_at' => now(),
                    'sms_status' => 'sent'
                ]);
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Twilio SMS Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create notification record
     */
    protected function createNotification($recipient, $order, $type)
    {
        $messages = $this->getNotificationMessages($type, $order);

        return Notification::create([
            'recipient_id' => $recipient->id,
            'recipient_type' => get_class($recipient),
            'order_id' => $order->id,
            'type' => $type,
            'title' => $messages['title'],
            'message' => $messages['message'],
            'data' => json_encode([
                'order_number' => $order->order_number,
                'pickup_address' => $order->pickup_address,
                'destination_address' => $order->destination_address,
                'estimated_fare' => $order->estimated_fare
            ]),
            'is_read' => false,
            'created_at' => now()
        ]);
    }

    /**
     * Create status notification
     */
    protected function createStatusNotification($recipient, $order, $status)
    {
        $messages = $this->getStatusMessages($status, $order);

        return Notification::create([
            'recipient_id' => $recipient->id,
            'recipient_type' => get_class($recipient),
            'order_id' => $order->id,
            'type' => 'status_update',
            'title' => $messages['title'],
            'message' => $messages['message'],
            'data' => json_encode([
                'order_number' => $order->order_number,
                'status' => $status,
                'driver_name' => $order->driver->name ?? null,
                'driver_phone' => $order->driver->phone ?? null
            ]),
            'is_read' => false,
            'created_at' => now()
        ]);
    }

    /**
     * Create cancellation notification
     */
    protected function createCancellationNotification($recipient, $order)
    {
        $recipientType = get_class($recipient);
        $isDriver = $recipientType === Driver::class;

        $title = 'Order Cancelled';
        $message = $isDriver 
            ? "Order #{$order->order_number} has been cancelled by the customer."
            : "Your order #{$order->order_number} has been cancelled.";

        if ($order->cancellation_reason) {
            $message .= " Reason: {$order->cancellation_reason}";
        }

        return Notification::create([
            'recipient_id' => $recipient->id,
            'recipient_type' => $recipientType,
            'order_id' => $order->id,
            'type' => 'order_cancelled',
            'title' => $title,
            'message' => $message,
            'data' => json_encode([
                'order_number' => $order->order_number,
                'cancellation_reason' => $order->cancellation_reason,
                'cancelled_by' => $order->cancelled_by
            ]),
            'is_read' => false,
            'created_at' => now()
        ]);
    }

    /**
     * Get notification messages by type
     */
    protected function getNotificationMessages($type, $order)
    {
        $messages = [
            'new_order' => [
                'title' => 'New Order Available',
                'message' => "New order from {$order->pickup_address} to {$order->destination_address}. Estimated fare: Rp " . number_format($order->estimated_fare, 0, ',', '.')
            ],
            'order_accepted' => [
                'title' => 'Order Accepted',
                'message' => "Your order #{$order->order_number} has been accepted by a driver."
            ],
            'driver_arrived' => [
                'title' => 'Driver Arrived',
                'message' => "Your driver has arrived at the pickup location."
            ],
            'trip_started' => [
                'title' => 'Trip Started',
                'message' => "Your trip has started. Have a safe journey!"
            ],
            'trip_completed' => [
                'title' => 'Trip Completed',
                'message' => "Your trip has been completed. Thank you for using our service!"
            ]
        ];

        return $messages[$type] ?? [
            'title' => 'Notification',
            'message' => 'You have a new notification.'
        ];
    }

    /**
     * Get status messages
     */
    protected function getStatusMessages($status, $order)
    {
        $messages = [
            'accepted' => [
                'title' => 'Order Accepted',
                'message' => "Your order #{$order->order_number} has been accepted by a driver."
            ],
            'driver_arrived' => [
                'title' => 'Driver Arrived',
                'message' => "Your driver has arrived at the pickup location."
            ],
            'in_progress' => [
                'title' => 'Trip Started',
                'message' => "Your trip has started. Have a safe journey!"
            ],
            'completed' => [
                'title' => 'Trip Completed',
                'message' => "Your trip has been completed. Thank you for using our service!"
            ]
        ];

        return $messages[$status] ?? [
            'title' => 'Status Update',
            'message' => "Your order status has been updated to {$status}."
        ];
    }

    /**
     * Get user notifications
     */
    public function getUserNotifications($userId, $limit = 20, $unreadOnly = false)
    {
        try {
            $query = Notification::where('recipient_id', $userId)
                ->where('recipient_type', User::class)
                ->orderBy('created_at', 'desc');

            if ($unreadOnly) {
                $query->where('is_read', false);
            }

            $notifications = $query->limit($limit)->get();

            return [
                'success' => true,
                'data' => $notifications,
                'unread_count' => Notification::where('recipient_id', $userId)
                    ->where('recipient_type', User::class)
                    ->where('is_read', false)
                    ->count()
            ];

        } catch (\Exception $e) {
            Log::error('Get User Notifications Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId = null)
    {
        try {
            $query = Notification::where('id', $notificationId);

            if ($userId) {
                $query->where('recipient_id', $userId);
            }

            $notification = $query->first();

            if (!$notification) {
                return [
                    'success' => false,
                    'error' => 'Notification not found'
                ];
            }

            $notification->update([
                'is_read' => true,
                'read_at' => now()
            ]);

            return [
                'success' => true,
                'message' => 'Notification marked as read'
            ];

        } catch (\Exception $e) {
            Log::error('Mark Notification As Read Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead($userId, $recipientType = null)
    {
        try {
            $query = Notification::where('recipient_id', $userId)
                ->where('is_read', false);

            if ($recipientType) {
                $query->where('recipient_type', $recipientType);
            }

            $count = $query->update([
                'is_read' => true,
                'read_at' => now()
            ]);

            return [
                'success' => true,
                'message' => "Marked {$count} notifications as read"
            ];

        } catch (\Exception $e) {
            Log::error('Mark All Notifications As Read Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send bulk notifications
     */
    public function sendBulkNotification($recipients, $title, $message, $data = [])
    {
        try {
            $notifications = [];
            $currentTime = now();

            foreach ($recipients as $recipient) {
                $notifications[] = [
                    'recipient_id' => $recipient->id,
                    'recipient_type' => get_class($recipient),
                    'type' => 'bulk',
                    'title' => $title,
                    'message' => $message,
                    'data' => json_encode($data),
                    'is_read' => false,
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime
                ];

                // Send push notification
                if ($recipient->fcm_token) {
                    $this->sendPushNotification($recipient, (object)[
                        'title' => $title,
                        'message' => $message,
                        'id' => null,
                        'order_id' => null
                    ]);
                }
            }

            Notification::insert($notifications);

            return [
                'success' => true,
                'message' => 'Bulk notifications sent successfully',
                'sent_count' => count($notifications)
            ];

        } catch (\Exception $e) {
            Log::error('Send Bulk Notification Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}