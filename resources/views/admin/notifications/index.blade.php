@extends('layouts.admin')

@section('title', 'Notifications Management')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Notifications Management</h1>
                    <p class="text-gray-600 mt-1">Send notifications and manage communication</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button onclick="openNotificationModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>Send Notification
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="p-6">
        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Broadcast to All</p>
                        <p class="text-sm text-gray-600">Send to all active users</p>
                    </div>
                </div>
                <button onclick="openBroadcastModal('all')" class="w-full mt-4 bg-blue-50 text-blue-700 py-2 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                    Send Broadcast
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-motorcycle text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Notify Drivers</p>
                        <p class="text-sm text-gray-600">Send to all drivers</p>
                    </div>
                </div>
                <button onclick="openBroadcastModal('driver')" class="w-full mt-4 bg-green-50 text-green-700 py-2 rounded-lg hover:bg-green-100 transition-colors duration-200">
                    Send to Drivers
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-friends text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Notify Customers</p>
                        <p class="text-sm text-gray-600">Send to all customers</p>
                    </div>
                </div>
                <button onclick="openBroadcastModal('customer')" class="w-full mt-4 bg-purple-50 text-purple-700 py-2 rounded-lg hover:bg-purple-100 transition-colors duration-200">
                    Send to Customers
                </button>
            </div>
        </div>

        <!-- Notification Templates -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-8">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Quick Templates</h2>
                <p class="text-gray-600 text-sm mt-1">Pre-defined notification templates</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors duration-200 cursor-pointer" onclick="useTemplate('maintenance')">
                        <div class="flex items-center space-x-3 mb-2">
                            <i class="fas fa-tools text-orange-600"></i>
                            <p class="font-medium text-gray-900">System Maintenance</p>
                        </div>
                        <p class="text-sm text-gray-600">Notify users about scheduled maintenance</p>
                    </div>

                    <div class="p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors duration-200 cursor-pointer" onclick="useTemplate('promotion')">
                        <div class="flex items-center space-x-3 mb-2">
                            <i class="fas fa-gift text-green-600"></i>
                            <p class="font-medium text-gray-900">Promotion Alert</p>
                        </div>
                        <p class="text-sm text-gray-600">Announce special offers and discounts</p>
                    </div>

                    <div class="p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors duration-200 cursor-pointer" onclick="useTemplate('update')">
                        <div class="flex items-center space-x-3 mb-2">
                            <i class="fas fa-mobile-alt text-blue-600"></i>
                            <p class="font-medium text-gray-900">App Update</p>
                        </div>
                        <p class="text-sm text-gray-600">Inform about new app features</p>
                    </div>

                    <div class="p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors duration-200 cursor-pointer" onclick="useTemplate('policy')">
                        <div class="flex items-center space-x-3 mb-2">
                            <i class="fas fa-shield-alt text-purple-600"></i>
                            <p class="font-medium text-gray-900">Policy Update</p>
                        </div>
                        <p class="text-sm text-gray-600">Notify about policy changes</p>
                    </div>

                    <div class="p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors duration-200 cursor-pointer" onclick="useTemplate('welcome')">
                        <div class="flex items-center space-x-3 mb-2">
                            <i class="fas fa-hand-wave text-yellow-600"></i>
                            <p class="font-medium text-gray-900">Welcome Message</p>
                        </div>
                        <p class="text-sm text-gray-600">Welcome new users to the platform</p>
                    </div>

                    <div class="p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors duration-200 cursor-pointer" onclick="useTemplate('reminder')">
                        <div class="flex items-center space-x-3 mb-2">
                            <i class="fas fa-bell text-red-600"></i>
                            <p class="font-medium text-gray-900">Reminder</p>
                        </div>
                        <p class="text-sm text-gray-600">Send important reminders</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Notifications -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Notifications</h2>
                    <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        View All History
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <!-- Sample notification entries -->
                    <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-bell text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <p class="font-medium text-gray-900">System Maintenance Notification</p>
                                <span class="text-xs text-gray-500">2 hours ago</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">Sent to all users about scheduled maintenance on Sunday</p>
                            <div class="flex items-center space-x-4 mt-2">
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Delivered: 1,234</span>
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">Read: 987</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-gift text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <p class="font-medium text-gray-900">Weekend Promotion Alert</p>
                                <span class="text-xs text-gray-500">1 day ago</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">Special weekend discount notification sent to customers</p>
                            <div class="flex items-center space-x-4 mt-2">
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Delivered: 856</span>
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">Read: 623</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-motorcycle text-purple-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <p class="font-medium text-gray-900">Driver Incentive Program</p>
                                <span class="text-xs text-gray-500">3 days ago</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">New incentive program announcement for drivers</p>
                            <div class="flex items-center space-x-4 mt-2">
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Delivered: 342</span>
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">Read: 298</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notification Modal -->
<div id="notificationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">Send Notification</h2>
                <button onclick="closeNotificationModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <form action="{{ route('admin.notifications.send') }}" method="POST" class="p-6">
            @csrf
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Send To</label>
                    <select name="user_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all">All Users</option>
                        <option value="customer">Customers Only</option>
                        <option value="driver">Drivers Only</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <input type="text" name="title" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Notification title">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                    <textarea name="message" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Notification message"></textarea>
                </div>

                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="schedule" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="schedule" class="text-sm text-gray-700">Schedule for later</label>
                </div>

                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeNotificationModal()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        Send Notification
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function openNotificationModal() {
    document.getElementById('notificationModal').classList.remove('hidden');
    document.getElementById('notificationModal').classList.add('flex');
}

function closeNotificationModal() {
    document.getElementById('notificationModal').classList.add('hidden');
    document.getElementById('notificationModal').classList.remove('flex');
}

function openBroadcastModal(type) {
    openNotificationModal();
    document.querySelector('select[name="user_type"]').value = type;
}

function useTemplate(type) {
    const templates = {
        maintenance: {
            title: 'Scheduled System Maintenance',
            message: 'We will be performing scheduled maintenance on Sunday from 2:00 AM to 4:00 AM. The service may be temporarily unavailable during this time.'
        },
        promotion: {
            title: 'Special Weekend Promotion!',
            message: 'Enjoy 20% off on all rides this weekend! Use code WEEKEND20 and save on your next trip.'
        },
        update: {
            title: 'New App Update Available',
            message: 'Update your app to the latest version to enjoy new features and improved performance.'
        },
        policy: {
            title: 'Policy Update Notice',
            message: 'We have updated our terms of service and privacy policy. Please review the changes in your account settings.'
        },
        welcome: {
            title: 'Welcome to AnterinAja!',
            message: 'Thank you for joining AnterinAja! Get started by booking your first ride and enjoy our reliable service.'
        },
        reminder: {
            title: 'Important Reminder',
            message: 'This is a friendly reminder about your upcoming booking. Please be ready at the pickup location.'
        }
    };

    if (templates[type]) {
        openNotificationModal();
        document.querySelector('input[name="title"]').value = templates[type].title;
        document.querySelector('textarea[name="message"]').value = templates[type].message;
    }
}
</script>
@endsection
