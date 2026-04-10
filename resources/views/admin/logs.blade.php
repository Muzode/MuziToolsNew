@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card fade-in-up">
                    <div class="card-header">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-history mr-2"></i> Activity Logs
                        </h3>
                        <div class="card-tools">
                            <a href="{{ url('/admin/dashboard') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-tachometer-alt mr-1"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search Form (Server-side) -->
                        <form method="GET" action="{{ route('admin.logs') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Search</label>
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Search by user, action, or description..."
                                            value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">Filter by Action</label>
                                        <select name="action" class="form-control">
                                            <option value="">All Actions</option>
                                            <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>
                                                Create</option>
                                            <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>
                                                Update</option>
                                            <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>
                                                Delete</option>
                                            <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>
                                                Login</option>
                                            <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>
                                                Logout</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="d-flex">
                                            <button type="submit" class="btn btn-primary mr-2">
                                                <i class="fas fa-search mr-1"></i> Filter
                                            </button>
                                            @if (request('search') || request('action'))
                                                <a href="{{ route('admin.logs') }}" class="btn btn-secondary">
                                                    <i class="fas fa-undo mr-1"></i> Reset
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-transparent">
                                <thead>
                                    <tr>
                                        <th>
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'no', 'order' => request('order') == 'asc' ? 'desc' : 'asc']) }}"
                                                class="text-decoration-none text-dark">
                                                No
                                                @if (request('sort') == 'no')
                                                    <i
                                                        class="fas fa-sort-{{ request('order') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'user', 'order' => request('order') == 'asc' ? 'desc' : 'asc']) }}"
                                                class="text-decoration-none text-dark">
                                                User
                                                @if (request('sort') == 'user')
                                                    <i
                                                        class="fas fa-sort-{{ request('order') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>Role</th>
                                        <th>
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'action', 'order' => request('order') == 'asc' ? 'desc' : 'asc']) }}"
                                                class="text-decoration-none text-dark">
                                                Action
                                                @if (request('sort') == 'action')
                                                    <i
                                                        class="fas fa-sort-{{ request('order') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>Description</th>
                                        <th>
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'date', 'order' => request('order') == 'asc' ? 'desc' : 'asc']) }}"
                                                class="text-decoration-none text-dark">
                                                Date & Time
                                                @if (request('sort') == 'date' || !request('sort'))
                                                    <i
                                                        class="fas fa-sort-{{ request('order', 'desc') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                                @endif
                                            </a>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $index => $log)
                                        <tr>
                                            <td>{{ ($logs->currentPage() - 1) * $logs->perPage() + $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-soft-primary p-2 mr-2"
                                                        style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-user fa-sm text-primary"></i>
                                                    </div>
                                                    {{ $log->user ? $log->user->name : 'System/Unknown' }}
                                                </div>
                                            </td>
                                            <td>
                                                @if ($log->user)
                                                    @php
                                                        $roleClass = match ($log->user->role) {
                                                            'admin' => 'danger',
                                                            'petugas' => 'warning',
                                                            default => 'info',
                                                        };
                                                        $roleIcon = match ($log->user->role) {
                                                            'admin' => 'fas fa-shield-alt',
                                                            'petugas' => 'fas fa-user-tie',
                                                            default => 'fas fa-user',
                                                        };
                                                    @endphp
                                                    <span class="text-muted badge badge-{{ $roleClass }}">
                                                        <i class="{{ $roleIcon }} mr-1"></i>
                                                        {{ ucfirst($log->user->role) }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">
                                                        <i class="fas fa-question mr-1"></i> N/A
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $actionClass = match ($log->action) {
                                                        'create' => 'success',
                                                        'update' => 'info',
                                                        'delete' => 'danger',
                                                        'login' => 'primary',
                                                        'logout' => 'secondary',
                                                        default => 'secondary',
                                                    };
                                                    $actionIcon = match ($log->action) {
                                                        'create' => 'fas fa-plus',
                                                        'update' => 'fas fa-edit',
                                                        'delete' => 'fas fa-trash',
                                                        'login' => 'fas fa-sign-in-alt',
                                                        'logout' => 'fas fa-sign-out-alt',
                                                        default => 'fas fa-bell',
                                                    };
                                                @endphp
                                                <span class="text-muted badge badge-{{ $actionClass }}">
                                                    <i class="{{ $actionIcon }} mr-1"></i>
                                                    {{ ucfirst($log->action) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-muted small">
                                                    <i class="fas fa-file-alt mr-1 fs-5"></i>
                                                    {{ $log->description ?? '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="font-weight-bold small">
                                                        <i class="far fa-calendar-alt mr-1"></i>
                                                        {{ $log->created_at ? $log->created_at->format('d/m/Y') : '-' }}
                                                    </span>
                                                    <span class="text-muted small">
                                                        <i class="far fa-clock mr-1"></i>
                                                        {{ $log->created_at ? $log->created_at->format('H:i:s') : '-' }}
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <i class="fas fa-inbox fa-3x mb-3 d-block text-muted"></i>
                                                <p class="text-muted mb-0">No activity logs found</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Info and Pagination -->
                        @if (method_exists($logs, 'links') && $logs->total() > 0)
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="text-muted small">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }}
                                    of {{ $logs->total() }} entries
                                </div>
                                <div>
                                    {{-- Gunakan Bootstrap 4 template --}}
                                    {{ $logs->appends(request()->query())->links('pagination::bootstrap-4') }} </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
@endpush

@push('scripts')
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#logs-table').DataTable({
                "order": [
                    [6, "desc"]
                ], // Sort by date descending
                "pageLength": 25,
                "language": {
                    "lengthMenu": "Show _MENU_ entries per page",
                    "zeroRecords": "No records found",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "No entries available",
                    "infoFiltered": "(filtered from _MAX_ total entries)"
                }
            });
        });
    </script>
@endpush
