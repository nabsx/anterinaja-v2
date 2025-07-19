@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">User Details</h1>
            <nav class="flex mt-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-blue-600">
                            <i class="fas fa-home mr-1"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <a href="{{ route('admin.users') }}" class="text-gray-700 hover:text-blue-600">Users</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-gray-500">{{ $user->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.users') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition duration-150 ease-in-out">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Users
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
            <div class="flex">
                <div class="py-1">
                    <i class="fas fa-check-circle mr-2"></i>
                </div>
                <div>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
                <div class="ml-auto">
                    <button type="button" class="text-green-700 hover:text-green-900" onclick="this.parentElement.parentElement.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
            <div class="flex">
                <div class="py-1">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                </div>
                <div>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
                <div class="ml-auto">
                    <button type="button" class="text-red-700 hover:text-red-900" onclick="this.parentElement.parentElement.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Information Card -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">User Information</h3>
                    <div class="relative">
                        <button type="button" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" onclick="toggleDropdown('userActions')">
                            Actions
                            <i class="fas fa-chevron-down ml-2"></i>
                        </button>
                        <div id="userActions" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                            <div class="py-1">
                                <button type="button" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="openModal('editUserModal')">
                                    <i class="fas fa-edit mr-2"></i>Edit User
                                </button>
                                <button type="button" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="toggleUserStatus({{ $user->id }})">
                                    <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }} mr-2"></i>
                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }} User
                                </button>
                                @if($user->id !== auth()->id())
                                <hr class="my-1">
                                <button type="button" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50" onclick="deleteUser({{ $user->id }})">
                                    <i class="fas fa-trash mr-2"></i>Delete User
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-6">
                    <div class="text-center mb-6">
                        @if($user->profile_picture)
                            <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                 class="w-24 h-24 rounded-full mx-auto object-cover" alt="Profile Picture">
                        @else
                            <div class="w-24 h-24 rounded-full bg-blue-500 flex items-center justify-center mx-auto">
                                <span class="text-white font-bold text-2xl">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <h4 class="mt-4 text-xl font-semibold text-gray-900">{{ $user->name }}</h4>
                        <div class="mt-2 flex justify-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : ($user->role === 'driver' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ ucfirst($user->role) }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-500">Email:</span>
                            <span class="text-gray-900">{{ $user->email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-500">Phone:</span>
                            <span class="text-gray-900">{{ $user->phone }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-500">Address:</span>
                            <span class="text-gray-900 text-right">{{ $user->address ?? 'Not provided' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-500">City:</span>
                            <span class="text-gray-900">{{ $user->city ?? 'Not provided' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-500">Joined:</span>
                            <span class="text-gray-900">{{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-500">Last Login:</span>
                            <span class="text-gray-900">{{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-500">Email Verified:</span>
                            <span>
                                @if($user->email_verified_at)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Verified
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Not Verified
                                    </span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Driver Information (if user is a driver) -->
        @if($user->role === 'driver' && $user->driver)
        <div class="lg:col-span-2">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Driver Information</h3>
                </div>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-500">License Number:</span>
                                <span class="text-gray-900">{{ $user->driver->license_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-500">Vehicle Type:</span>
                                <span class="text-gray-900">{{ $user->driver->vehicleType->name ?? 'Not set' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-500">Vehicle Plate:</span>
                                <span class="text-gray-900">{{ $user->driver->vehicle_plate }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-500">Vehicle Model:</span>
                                <span class="text-gray-900">{{ $user->driver->vehicle_model ?? 'Not provided' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-500">Vehicle Color:</span>
                                <span class="text-gray-900">{{ $user->driver->vehicle_color ?? 'Not provided' }}</span>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-500">Status:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $user->driver->status === 'available' ? 'bg-green-100 text-green-800' : ($user->driver->status === 'busy' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($user->driver->status) }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-500">Verified:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $user->driver->is_verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $user->driver->is_verified ? 'Verified' : 'Pending' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-500">Online:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $user->driver->is_online ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $user->driver->is_online ? 'Online' : 'Offline' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-500">Balance:</span>
                                <span class="text-gray-900 font-semibold">Rp {{ number_format($user->driver->balance, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-500">Emergency Contact:</span>
                                <span class="text-gray-900">{{ $user->driver->emergency_contact ?? 'Not provided' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Customer Orders -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
                </div>
                <div class="px-6 py-6">
                    @if($user->orders && $user->orders->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($user->orders->take(10) as $order)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $order->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ Str::limit($order->pickup_address, 30) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ Str::limit($order->destination_address, 30) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($order->fare_amount, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($user->orders->count() > 10)
                            <div class="text-center mt-4">
                                <a href="{{ route('admin.orders', ['customer_id' => $user->id]) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    View All Orders
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-shopping-cart text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-500">No orders found for this user.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Driver Orders (if user is a driver) -->
    @if($user->role === 'driver' && $user->driver && $user->driver->orders)
    <div class="mt-6">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Driver Orders</h3>
            </div>
            <div class="px-6 py-6">
                @if($user->driver->orders->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Earning</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($user->driver->orders->take(10) as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $order->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->customer->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ Str::limit($order->pickup_address, 25) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ Str::limit($order->destination_address, 25) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($order->fare_amount, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($order->driver_earning, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($user->driver->orders->count() > 10)
                        <div class="text-center mt-4">
                            <a href="{{ route('admin.orders', ['driver_id' => $user->driver->id]) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                View All Orders
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-car text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500">No orders found for this driver.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Edit User</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('editUserModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" id="name" name="name" value="{{ $user->name }}" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" value="{{ $user->email }}" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="text" id="phone" name="phone" value="{{ $user->phone }}" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                        <select id="role" name="role" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="customer" {{ $user->role === 'customer' ? 'selected' : '' }}>Customer</option>
                            <option value="driver" {{ $user->role === 'driver' ? 'selected' : '' }}>Driver</option>
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400" onclick="closeModal('editUserModal')">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    dropdown.classList.toggle('hidden');
}

function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}

function toggleUserStatus(userId) {
    if (confirm('Are you sure you want to change this user\'s status?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/users/${userId}/toggle-status`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/users/${userId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const dropdowns = document.querySelectorAll('[id$="Actions"]');
    dropdowns.forEach(dropdown => {
        if (!dropdown.contains(event.target) && !event.target.closest('button[onclick*="toggleDropdown"]')) {
            dropdown.classList.add('hidden');
        }
    });
});
</script>
@endsection
