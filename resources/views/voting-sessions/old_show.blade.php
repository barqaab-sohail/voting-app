@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>{{ $votingSession->title }}</h2>
        <span class="badge badge-{{ $votingSession->isActive() ? 'success' : 'secondary' }}">
            {{ $votingSession->isActive() ? 'Active' : 'Ended' }}
        </span>
    </div>
    <div class="card-body">
        <p><strong>Description:</strong> {{ $votingSession->description ?? 'No description' }}</p>
        <p><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $votingSession->type)) }}</p>
        <p><strong>Time:</strong> {{ $votingSession->start_time->format('Y-m-d H:i') }} to {{ $votingSession->end_time->format('Y-m-d H:i') }}</p>

        @if($votingSession->type === 'judge_panel')
        <p><strong>Number of Judges to Select:</strong> {{ $votingSession->judges_count }}</p>
        @endif

        <hr>

        @if(!$votingSession->isActive())
        <div class="alert alert-info">
            This voting session has ended. Below are the final results.
        </div>
        @endif

        @if($hasVoted)
        <div class="alert alert-success">
            You have already voted in this session.
        </div>
        @endif

        @if($votingSession->isActive() && !$hasVoted)
        <div class="card mb-4">
            <div class="card-header">
                <h4>Cast Your Vote</h4>
            </div>
            <div class="card-body">
                @if($votingSession->type === 'single_issue')
                <form method="POST" action="{{ route('votes.store', $votingSession) }}">
                    @csrf
                    <div class="form-group">
                        <label>Your Vote:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="vote_choice" id="vote_yes" value="1" required>
                            <label class="form-check-label" for="vote_yes">Yes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="vote_choice" id="vote_no" value="0" required>
                            <label class="form-check-label" for="vote_no">No</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Vote</button>
                </form>
                @else
                <form method="POST" action="{{ route('votes.store', $votingSession) }}">
                    @csrf
                    <div class="form-group">
                        <label>Select {{ $votingSession->judges_count }} Judges (excluding yourself):</label>
                        @foreach($eligibleJudges as $judge)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="judges[]" id="judge_{{ $judge->id }}" value="{{ $judge->id }}">
                            <label class="form-check-label" for="judge_{{ $judge->id }}">{{ $judge->name }}</label>
                        </div>
                        @endforeach
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Vote</button>
                </form>

                @push('scripts')
                <script>
                    const maxSelection = {
                        {
                            $votingSession - > judges_count
                        }
                    };
                    const checkboxes = document.querySelectorAll('input[name="judges[]"]');

                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            const checkedCount = document.querySelectorAll('input[name="judges[]"]:checked').length;
                            if (checkedCount >= maxSelection) {
                                checkboxes.forEach(cb => {
                                    if (!cb.checked) cb.disabled = true;
                                });
                            } else {
                                checkboxes.forEach(cb => cb.disabled = false);
                            }
                        });
                    });
                </script>
                @endpush
                @endif
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h4>Results</h4>
            </div>
            <div class="card-body">
                @if($votingSession->type === 'single_issue')
                <div class="row">
                    <div class="col-md-6">
                        <h5>Vote Distribution</h5>
                        <canvas id="voteChart" width="400" height="400"></canvas>
                    </div>
                    <div class="col-md-6">
                        <h5>Vote Details</h5>
                        <p>Total Votes: {{ $results['total'] }}</p>
                        <p>Yes Votes: {{ $results['yes'] }} ({{ $results['yes_percentage'] }}%)</p>
                        <p>No Votes: {{ $results['no'] }} ({{ $results['no_percentage'] }}%)</p>
                    </div>
                </div>

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
                @else
                <h5>Top {{ $votingSession->judges_count }} Judges</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Name</th>
                                <th>Votes Received</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results['judges'] as $index => $judge)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $judge->name }}</td>
                                <td>{{ $judge->selected_as_judge_count }}</td>
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
@endsection