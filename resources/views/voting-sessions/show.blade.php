@extends('layouts.admin')

@section('title', $votingSession->title)

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3>{{ $votingSession->title }}</h3>
            <div>
                <span class="badge bg-{{ $votingSession->isActive() ? 'success' : 'secondary' }} me-2">
                    {{ $votingSession->isActive() ? 'Active' : 'Ended' }}
                </span>
                @can('update', $votingSession)
                <a href="{{ route('voting-sessions.edit', $votingSession) }}" class="btn btn-sm btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                @endcan
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <p><strong>Description:</strong> {{ $votingSession->description ?? 'No description' }}</p>
                <p><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $votingSession->type)) }}</p>
                @if($votingSession->type === 'judge_panel')
                <p><strong>Judges to Select:</strong> {{ $votingSession->judges_count }}</p>
                @endif
            </div>
            <div class="col-md-6">
                <p><strong>Created By:</strong> {{ $votingSession->creator->name }}</p>
                <p><strong>Start Time:</strong> {{ $votingSession->start_time->format('Y-m-d H:i') }}</p>
                <p><strong>End Time:</strong> {{ $votingSession->end_time->format('Y-m-d H:i') }}</p>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Voting Status</h5>
                    </div>
                    <div class="card-body">
                        <p>Total Members: {{ $totalMembers }}</p>
                        <p>Members Voted: {{ $votesCount }}</p>
                        <div class="progress mb-3">
                            <div class="progress-bar" role="progressbar"
                                style="width: {{ $totalMembers > 0 ? round(($votesCount / $totalMembers) * 100) : 0 }}%"
                                aria-valuenow="{{ $votesCount }}"
                                aria-valuemin="0"
                                aria-valuemax="{{ $totalMembers }}">
                                {{ $totalMembers > 0 ? round(($votesCount / $totalMembers) * 100) : 0 }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        @if($votingSession->isActive())
                        <form action="{{ route('voting-sessions.update', $votingSession) }}" method="POST" class="mb-3">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="is_active" value="0">
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-stop"></i> End Voting Early
                            </button>
                        </form>
                        @endif
                        <a href="#" class="btn btn-secondary w-100 mb-3">
                            <i class="fas fa-envelope"></i> Send Reminder
                        </a>
                        <a href="#" class="btn btn-info w-100">
                            <i class="fas fa-download"></i> Export Results
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5>Results</h5>
            </div>
            <div class="card-body">
                @if($votingSession->type === 'single_issue')
                <div class="row">
                    <div class="col-md-6">
                        <h6>Vote Distribution</h6>
                        <canvas id="voteChart" height="200"></canvas>
                    </div>
                    <div class="col-md-6">
                        <h6>Vote Details</h6>
                        <p>Total Votes: {{ $results['total'] }}</p>
                        <p>Yes Votes: {{ $results['yes'] }} ({{ $results['yes_percentage'] }}%)</p>
                        <p>No Votes: {{ $results['no'] }} ({{ $results['no_percentage'] }}%)</p>
                    </div>
                </div>
                @else
                <h6>Top {{ $votingSession->judges_count }} Judges</h6>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Name</th>
                                <th>Votes Received</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results['judges'] as $index => $judge)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $judge->name }}</td>
                                <td>{{ $judge->selected_as_judge_count }}</td>
                                <td>
                                    {{ $results['total_votes'] > 0 ? round(($judge->selected_as_judge_count / $results['total_votes']) * 100, 2) : 0 }}%
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p>Total Votes Cast: {{ $results['total_votes'] }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

@if($votingSession->type === 'single_issue')
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('voteChart').getContext('2d');
    const voteChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Yes', 'No'],
            datasets: [{
                data: [{
                    {
                        $results['yes']
                    }
                }, {
                    {
                        $results['no']
                    }
                }],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(255, 99, 132, 0.7)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Vote Distribution'
                }
            }
        }
    });
</script>
@endpush
@endif
@endsection