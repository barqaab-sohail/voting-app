@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Voting Sessions</h2>
        @can('create', App\Models\VotingSession::class)
        <a href="{{ route('voting-sessions.create') }}" class="btn btn-primary float-right">Create New Session</a>
        @endcan
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sessions as $session)
                <tr>
                    <td>{{ $session->title }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $session->type)) }}</td>
                    <td>
                        @if($session->isActive())
                        <span class="badge badge-success">Active</span>
                        @elseif(now() < $session->start_time)
                            <span class="badge badge-info">Upcoming</span>
                            @else
                            <span class="badge badge-secondary">Ended</span>
                            @endif
                    </td>
                    <td>{{ $session->start_time->format('Y-m-d H:i') }}</td>
                    <td>{{ $session->end_time->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('voting-sessions.show', $session) }}" class="btn btn-sm btn-info">View</a>
                        @can('update', $session)
                        <a href="{{ route('voting-sessions.edit', $session) }}" class="btn btn-sm btn-primary">Edit</a>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection