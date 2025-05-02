<?php

namespace App\Http\Controllers;

use App\Models\VotingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VotingSessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $sessions = VotingSession::with('creator')->latest()->get();
        return view('voting-sessions.index', compact('sessions'));
    }

    public function create()
    {
        $this->authorize('create', VotingSession::class);
        return view('voting-sessions.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', VotingSession::class);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:single_issue,judge_panel',
            'judges_count' => 'required_if:type,judge_panel|integer|min:1',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
        ]);

        VotingSession::create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'judges_count' => $request->type === 'judge_panel' ? $request->judges_count : null,
            'created_by' => Auth::id(),
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_active' => true,
        ]);

        return redirect()->route('voting-sessions.index')->with('success', 'Voting session created successfully.');
    }

    public function show(VotingSession $votingSession)
    {
        $hasVoted = $votingSession->votes()->where('user_id', auth()->id())->exists();
        $results = $this->getSessionResults($votingSession);
        $totalMembers = 15;
        $votesCount = 0;

        return view('voting-sessions.show', compact('votingSession', 'hasVoted', 'results', 'totalMembers', 'votesCount'));
    }

    public function edit(VotingSession $votingSession)
    {
        $this->authorize('update', $votingSession);
        return view('voting-sessions.edit', compact('votingSession'));
    }

    public function update(Request $request, VotingSession $votingSession)
    {
        $this->authorize('update', $votingSession);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:single_issue,judge_panel',
            'judges_count' => 'required_if:type,judge_panel|integer|min:1',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'is_active' => 'boolean',
        ]);

        $votingSession->update([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'judges_count' => $request->type === 'judge_panel' ? $request->judges_count : null,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_active' => $request->is_active ?? $votingSession->is_active,
        ]);

        return redirect()->route('voting-sessions.index')->with('success', 'Voting session updated successfully.');
    }

    public function destroy(VotingSession $votingSession)
    {
        $this->authorize('delete', $votingSession);
        $votingSession->delete();
        return redirect()->route('voting-sessions.index')->with('success', 'Voting session deleted successfully.');
    }

    private function getSessionResults(VotingSession $session)
    {
        if ($session->type === 'single_issue') {
            $yesCount = $session->votes()->where('vote_choice', true)->count();
            $noCount = $session->votes()->where('vote_choice', false)->count();
            $total = $session->votes()->count();

            return [
                'yes' => $yesCount,
                'no' => $noCount,
                'total' => $total,
                'yes_percentage' => $total > 0 ? round(($yesCount / $total) * 100, 2) : 0,
                'no_percentage' => $total > 0 ? round(($noCount / $total) * 100, 2) : 0,
            ];
        } else {
            $judges = User::where('is_judge_eligible', true)
                ->where('id', '!=', auth()->id())
                ->withCount(['selectedAsJudge' => function ($query) use ($session) {
                    $query->whereHas('vote', function ($q) use ($session) {
                        $q->where('session_id', $session->id);
                    });
                }])
                ->orderBy('selected_as_judge_count', 'desc')
                ->take($session->judges_count ?? 3)
                ->get();

            return [
                'judges' => $judges,
                'total_votes' => $session->votes()->count(),
            ];
        }
    }
}
