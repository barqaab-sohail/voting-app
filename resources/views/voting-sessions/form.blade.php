@extends('layouts.admin')

@section('title', isset($session) ? 'Edit Voting Session' : 'Create Voting Session')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>{{ isset($session) ? 'Edit Voting Session' : 'Create New Voting Session' }}</h4>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ isset($session) ? route('voting-sessions.update', $session) : route('voting-sessions.store') }}">
            @csrf
            @if(isset($session))
            @method('PUT')
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title"
                            value="{{ old('title', $session->title ?? '') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="type" class="form-label">Voting Type</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="single_issue" {{ old('type', $session->type ?? '') == 'single_issue' ? 'selected' : '' }}>Single Issue (Yes/No)</option>
                            <option value="judge_panel" {{ old('type', $session->type ?? '') == 'judge_panel' ? 'selected' : '' }}>Panel of Judges</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $session->description ?? '') }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3" id="judges-count-group"
                        style="{{ old('type', $session->type ?? 'single_issue') == 'judge_panel' ? '' : 'display: none;' }}">
                        <label for="judges_count" class="form-label">Number of Judges to Select</label>
                        <input type="number" class="form-control" id="judges_count" name="judges_count"
                            min="1" value="{{ old('judges_count', $session->judges_count ?? 3) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="start_time" class="form-label">Start Time</label>
                        <input type="datetime-local" class="form-control" id="start_time" name="start_time"
                            value="{{ old('start_time', isset($session) ? $session->start_time->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="end_time" class="form-label">End Time</label>
                        <input type="datetime-local" class="form-control" id="end_time" name="end_time"
                            value="{{ old('end_time', isset($session) ? $session->end_time->format('Y-m-d\TH:i') : now()->addDay()->format('Y-m-d\TH:i')) }}" required>
                    </div>
                </div>
                @if(isset($session))
                <div class="col-md-6">
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                            {{ old('is_active', $session->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
                @endif
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('voting-sessions.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('type').addEventListener('change', function() {
        const judgesCountGroup = document.getElementById('judges-count-group');
        if (this.value === 'judge_panel') {
            judgesCountGroup.style.display = 'block';
        } else {
            judgesCountGroup.style.display = 'none';
        }
    });
</script>
@endpush
@endsection