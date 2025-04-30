@extends('layouts.admin')

@section('title', 'Manage Members')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h4>Group Members</h4>
            <a href="{{ route('admin.members.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Member
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Login Method</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone ?? 'N/A' }}</td>
                    <td>
                        @if($user->login_method === 'email')
                        <span class="badge bg-primary">Email</span>
                        @elseif($user->login_method === 'google')
                        <span class="badge bg-danger">Google</span>
                        @else
                        <span class="badge bg-success">Both</span>
                        @endif
                    </td>
                    <td>
                        @if($user->is_admin)
                        <span class="badge bg-dark">Admin</span>
                        @else
                        <span class="badge bg-secondary">Member</span>
                        @endif
                        @if($user->is_judge_eligible)
                        <span class="badge bg-info">Judge Eligible</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.members.edit', $user) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.members.destroy', $user) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $users->links() }}
    </div>
</div>
@endsection