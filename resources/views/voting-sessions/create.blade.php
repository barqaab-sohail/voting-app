@extends('layouts.admin')

@section('title', 'Create Voting Session')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Create New Voting Session</h4>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('voting-sessions.store') }}">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                        id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="type" class="form-label">Voting Type</label>
                    <select class="form-select @error('type') is-invalid @enderror"
                        id="type" name="type" required>
                        <option value="">Select Type</option>
                        <option value="single_issue" {{ old('type') == 'single_issue' ? 'selected' : '' }}>
                            Single Issue (Yes/No)
                        </option>
                        <option value="judge_panel" {{ old('type') == 'judge_panel' ? 'selected' : '' }}>
                            Panel of Judges
                        </option>
                    </select>
                    @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror"
                    id="description" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row mb-3" id="judges-count-container" style="display: none;">
                <div class="col-md-6">
                    <label for="judges_count" class="form-label">Number of Judges to Select</label>
                    <input type="number" class="form-control @error('judges_count') is-invalid @enderror"
                        id="judges_count" name="judges_count" min="1" value="{{ old('judges_count', 3) }}">
                    @error('judges_count')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="start_time" class="form-label">Start Time</label>
                    <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror"
                        id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                    @error('start_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="end_time" class="form-label">End Time</label>
                    <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror"
                        id="end_time" name="end_time" value="{{ old('end_time') }}" required>
                    @error('end_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('voting-sessions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Session
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const judgesCountContainer = document.getElementById('judges-count-container');

        typeSelect.addEventListener('change', function() {
            if (this.value === 'judge_panel') {
                judgesCountContainer.style.display = 'block';
                document.getElementById('judges_count').required = true;
            } else {
                judgesCountContainer.style.display = 'none';
                document.getElementById('judges_count').required = false;
            }
        });

        // Trigger change event on page load if there's a value
        if (typeSelect.value === 'judge_panel') {
            judgesCountContainer.style.display = 'block';
        }
    });
</script>
@endpush
@endsection