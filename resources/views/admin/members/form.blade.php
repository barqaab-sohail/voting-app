@extends('layouts.admin')

@section('title', isset($member) ? 'Edit Member' : 'Add Member')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>{{ isset($member) ? 'Edit Member' : 'Add New Member' }}</h4>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ isset($member) ? route('admin.members.update', $member) : route('admin.members.store') }}">
            @csrf
            @if(isset($member))
            @method('PUT')
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ old('name', $member->name ?? '') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="{{ old('email', $member->email ?? '') }}" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone"
                            value="{{ old('phone', $member->phone ?? '') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="login_method" class="form-label">Login Method</label>
                        <select class="form-select" id="login_method" name="login_method" required>
                            <option value="email" {{ old('login_method', $member->login_method ?? 'email') == 'email' ? 'selected' : '' }}>Email Only</option>
                            <option value="google" {{ old('login_method', $member->login_method ?? '') == 'google' ? 'selected' : '' }}>Google Only</option>
                            <option value="both" {{ old('login_method', $member->login_method ?? '') == 'both' ? 'selected' : '' }}>Both Email and Google</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row" id="google-id-row"
                style="{{ (old('login_method', $member->login_method ?? 'email') == 'google' || old('login_method', $member->login_method ?? '') == 'both') ? '' : 'display: none;' }}">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="allowed_google_id" class="form-label">Allowed Google ID</label>
                        <input type="text" class="form-control" id="allowed_google_id" name="allowed_google_id"
                            value="{{ old('allowed_google_id', $member->allowed_google_id ?? '') }}"
                            placeholder="Enter Google ID for this user">
                        <small class="text-muted">Required if login method includes Google</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            {{ isset($member) ? 'New Password (leave blank to keep current)' : 'Password' }}
                        </label>
                        <input type="password" class="form-control" id="password" name="password"
                            {{ !isset($member) ? 'required' : '' }}>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                            {{ !isset($member) ? 'required' : '' }}>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_admin" name="is_admin"
                            {{ old('is_admin', $member->is_admin ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_admin">Administrator</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_judge_eligible" name="is_judge_eligible"
                            {{ old('is_judge_eligible', $member->is_judge_eligible ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_judge_eligible">Judge Eligible</label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.members.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('login_method').addEventListener('change', function() {
        const googleIdRow = document.getElementById('google-id-row');
        if (this.value === 'google' || this.value === 'both') {
            googleIdRow.style.display = 'flex';
        } else {
            googleIdRow.style.display = 'none';
        }
    });
</script>
@endpush
@endsection