@extends('layouts.admin')

@section('title', 'Voting Sessions')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h4>Voting Sessions</h4>

            <a href="{{ route('voting-sessions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Session
            </a>

        </div>
    </div>
    <div class="card-body">
        <table class="table table-striped">
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
                        <span class="badge bg-success">Active</span>
                        @elseif(now() < $session->start_time)
                            <span class="badge bg-info">Upcoming</span>
                            @else
                            <span class="badge bg-secondary">Ended</span>
                            @endif
                    </td>
                    <td>{{ $session->start_time->format('Y-m-d H:i') }}</td>
                    <td>{{ $session->end_time->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('voting-sessions.show', $session) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i>
                        </a>
                        @can('update', $session)
                        <a href="{{ route('voting-sessions.edit', $session) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>
@endsection