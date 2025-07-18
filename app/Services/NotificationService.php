<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Order;
use App\Models\Driver;

class NotificationService
{
    /**
     * Send order notification to driver
     */
    public function sendOrderNotification($driver, $order, $type = 'new_order')
    {
        try {
            $message = $this->buildOrderMessage($order, $type);

            // Here you would integrate with your preferred notification service
            // For example: FCM, Pusher, SMS, etc.

            Log::info("Notification sent to driver {$driver->id}: {$message}");

            return true;
        } catch (\Exception $e) {
            Log::error('Notification Service Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send order status notification
     */
    public function sendOrderStatusNotification($user, $order, $status)
    {
        try {
            $message = $this->buildStatusMessage($order, $status);

            // Send notification based on user preference
            if ($user instanceof User) {
                $this->sendUserNotification($user, $message);
            } elseif ($user instanceof Driver) {
                $this->sendDriverNotification($user, $message);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Status Notification Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send cancellation notification
     */
    public function sendCancellationNotification($user, $order)
    {
        try {
            $message = "Order #{$order->order_number} has been cancelled";

            if ($user instanceof User) {
                $this->sendUserNotification($user, $message);
            } elseif ($user instanceof Driver) {
                $this->sendDriverNotification($user, $message);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Cancellation Notification Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send push notification
     */
    public function sendPushNotification($token, $title, $body, $data = [])
    {
        try {
            // Implement FCM or your preferred push notification service
            // This is a placeholder implementation

            Log::info("Push notification sent: {$title} - {$body}");

            return true;
        } catch (\Exception $e) {
            Log::error('Push Notification Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send SMS notification
     */
    public function sendSMS($phone, $message)
    {
        try {
            // Implement SMS service (Twilio, AWS SNS, etc.)
            // This is a placeholder implementation

            Log::info("SMS sent to {$phone}: {$message}");

            return true;
        } catch (\Exception $e) {
            Log::error('SMS Notification Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email notification
     */
    public function sendEmail($email, $subject, $message)
    {
        try {
            // Implement email service
            // This is a placeholder implementation

            Log::info("Email sent to {$email}: {$subject}");

            return true;
        } catch (\Exception $e) {
            Log::error('Email Notification Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Build order message
     */
    protected function buildOrderMessage($order, $type)
    {
        switch ($type) {
            case 'new_order':
                return "New order available! From {$order->pickup_address} to {$order->destination_address}";
            case 'order_accepted':
                return "Order #{$order->order_number} has been accepted";
            case 'driver_arrived':
                return "Driver has arrived at pickup location";
            case 'order_started':
                return "Your order has started";
            case 'order_completed':
                return "Order #{$order->order_number} has been completed";
            default:
                return "Order update: {$type}";
        }
    }

    /**
     * Build status message
     */
    protected function buildStatusMessage($order, $status)
    {
        $statusMessages = [
            'pending' => 'Order is pending driver assignment',
            'accepted' => 'Order has been accepted by driver',
            'driver_arrived' => 'Driver has arrived at pickup location',
            'picked_up' => 'Order has been picked up',
            'in_progress' => 'Order is in progress',
            'completed' => 'Order has been completed',
            'cancelled' => 'Order has been cancelled'
        ];

        return $statusMessages[$status] ?? "Order status updated to {$status}";
    }

    /**
     * Send notification to user
     */
    protected function sendUserNotification($user, $message)
    {
        // Implement user notification logic
        Log::info("User notification sent to {$user->id}: {$message}");
    }

    /**
     * Send notification to driver
     */
    public function sendDriverNotification($driver, $title, $message = null)
    {
        // If message is not provided, use title as message (backward compatibility)
        if ($message === null) {
            $message = $title;
            $title = 'Notification';
        }

        // Handle case where $driver is an ID
        if (is_numeric($driver)) {
            $driver = Driver::find($driver);
        }

        // Handle case where $driver is a user_id (from driver->user_id)
        if ($driver instanceof User) {
            $driver = $driver->driver; // Assuming you have a relationship
        }

        if (!$driver) {
            Log::error("Driver not found for notification");
            return false;
        }

        Log::info("Driver notification sent to {$driver->id}: {$title} - {$message}");
        
        // Add your actual notification logic here (FCM, email, SMS, etc.)
        // Example:
        // $this->sendPushNotification($driver->fcm_token, $title, $message);
        
        return true;
    }

    /**
     * Send bulk notifications
     */
    public function sendBulkNotifications($recipients, $message)
    {
        try {
            foreach ($recipients as $recipient) {
                if (is_array($recipient)) {
                    $this->sendPushNotification(
                        $recipient['token'],
                        $recipient['title'],
                        $message,
                        $recipient['data'] ?? []
                    );
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Bulk Notification Error: ' . $e->getMessage());
            return false;
        }
    }
}