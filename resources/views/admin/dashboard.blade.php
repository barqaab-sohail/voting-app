@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Total Members</h5>
                <p class="card-text display-4">{{ $totalMembers }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">Active Sessions</h5>
                <p class="card-text display-4">{{ $activeSessions }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info mb-3">
            <div class="card-body">
                <h5 class="card-title">Total Votes</h5>
                <p class="card-text display-4">{{ $totalVotes }}</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h4>Recent Voting Sessions</h4>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>End Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentSessions as $session)
                <tr>
                    <td>{{ $session->title }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $session->type)) }}</td>
                    <td>
                        @if($session->isActive())
                        <span class="badge bg-success">Active</span>
                        @elseif(now() < $session->start_time)
                            <span class="badge bg-info">Upcoming</span>
                            @else
                            <span class="badge bg-secondary">Ended</span>
                            @endif
                    </td>
                    <td>{{ $session->end_time->format('M d, Y H:i') }}</td>
                    <td>
                        <a href="{{ route('voting-sessions.show', $session) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection