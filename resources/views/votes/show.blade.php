@extends('layouts.app')

@section('title', 'Cast Your Vote - ' . $votingSession->title)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>{{ $votingSession->title }}</h4>
                </div>

                <div class="card-body">
                    @if($hasVoted)
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> You have already voted in this session.
                    </div>
                    <a href="{{ route('voting-sessions.show', $votingSession) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Session
                    </a>
                    @else
                    <div class="mb-4">
                        <p class="lead">{{ $votingSession->description }}</p>
                        <p><strong>Voting Type:</strong> {{ ucfirst(str_replace('_', ' ', $votingSession->type)) }}</p>
                        <p><strong>Deadline:</strong> {{ $votingSession->end_time->format('F j, Y g:i A') }}</p>
                    </div>

                    @if($votingSession->type === 'single_issue')
                    <form method="POST" action="{{ route('votes.store', $votingSession) }}">
                        @csrf
                        <div class="mb-4">
                            <h5>Your Vote:</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="vote_choice" id="vote_yes" value="1" required>
                                <label class="form-check-label" for="vote_yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="vote_choice" id="vote_no" value="0" required>
                                <label class="form-check-label" for="vote_no">No</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-vote-yea"></i> Submit Vote
                        </button>
                    </form>
                    @elseif($votingSession->type === 'judge_panel')
                    <form method="POST" action="{{ route('votes.store', $votingSession) }}" id="judgeSelectionForm">
                        @csrf
                        <div class="mb-4">
                            <h5>Select {{ $votingSession->judges_count }} Judges:</h5>
                            <p class="text-muted">You cannot select yourself. Choose {{ $votingSession->judges_count }} eligible members.</p>

                            <div class="list-group">
                                @foreach($eligibleJudges as $judge)
                                <div class="list-group-item">
                                    <div class="form-check">
                                        <input class="form-check-input judge-checkbox"
                                            type="checkbox"
                                            name="judges[]"
                                            id="judge_{{ $judge->id }}"
                                            value="{{ $judge->id }}">
                                        <label class="form-check-label" for="judge_{{ $judge->id }}">
                                            {{ $judge->name }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <small id="selectionHelp" class="form-text text-muted mt-2">
                                Selected: <span id="selectedCount">0</span>/{{ $votingSession->judges_count }}
                            </small>
                        </div>
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                            <i class="fas fa-vote-yea"></i> Submit Vote
                        </button>
                    </form>
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($votingSession->type === 'judge_panel')
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const maxSelection = {
            {
                $votingSession - > judges_count
            }
        };
        const checkboxes = document.querySelectorAll('.judge-checkbox');
        const selectedCount = document.getElementById('selectedCount');
        const submitBtn = document.getElementById('submitBtn');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checkedCount = document.querySelectorAll('.judge-checkbox:checked').length;
                selectedCount.textContent = checkedCount;

                // Enable/disable submit button
                submitBtn.disabled = checkedCount !== maxSelection;

                // Disable excess checkboxes
                if (checkedCount >= maxSelection) {
                    checkboxes.forEach(cb => {
                        if (!cb.checked) cb.disabled = true;
                    });
                } else {
                    checkboxes.forEach(cb => cb.disabled = false);
                }
            });
        });
    });
</script>
@endpush
@endif
@endsection