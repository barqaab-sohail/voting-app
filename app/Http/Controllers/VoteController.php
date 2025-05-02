<?php

namespace App\Http\Controllers;

use App\Models\VotingSession;
use App\Models\Vote;
use App\Models\JudgeSelection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class VoteController extends Controller
{
    public function show(VotingSession $votingSession)
    {
        // Check if voting session is active
        if (!$votingSession->isActive()) {
            return redirect()->route('voting-sessions.index')
                ->with('error', 'This voting session is not active.');
        }

        // Check if user has already voted
        $hasVoted = $votingSession->votes()->where('user_id', Auth::id())->exists();

        // Get eligible judges for judge panel voting
        $eligibleJudges = null;
        if ($votingSession->type === 'judge_panel') {
            $eligibleJudges = User::where('is_judge_eligible', true)
                ->where('id', '!=', Auth::id())
                ->get();
        }

        return view('votes.show', compact('votingSession', 'hasVoted', 'eligibleJudges'));
    }

    public function store(Request $request, VotingSession $votingSession)
    {
        // Validate session is active
        if (!$votingSession->isActive()) {
            return back()->with('error', 'This voting session has ended.');
        }

        // Check if user has already voted
        if ($votingSession->votes()->where('user_id', Auth::id())->exists()) {
            return back()->with('error', 'You have already voted in this session.');
        }

        // Handle different vote types
        if ($votingSession->type === 'single_issue') {
            $request->validate([
                'vote_choice' => 'required|boolean'
            ]);

            $vote = Vote::create([
                'session_id' => $votingSession->id,
                'user_id' => Auth::id(),
                'vote_choice' => $request->vote_choice
            ]);
        } elseif ($votingSession->type === 'judge_panel') {
            $request->validate([
                'judges' => ['required', 'array', 'size:' . $votingSession->judges_count],
                'judges.*' => [
                    'required',
                    'exists:users,id',
                    Rule::notIn([Auth::id()]),
                    function ($attribute, $value, $fail) {
                        if (!User::find($value)->is_judge_eligible) {
                            $fail('The selected judge is not eligible.');
                        }
                    }
                ],
            ]);

            $vote = Vote::create([
                'session_id' => $votingSession->id,
                'user_id' => Auth::id()
            ]);

            foreach ($request->judges as $judgeId) {
                JudgeSelection::create([
                    'vote_id' => $vote->id,
                    'selected_judge_id' => $judgeId
                ]);
            }
        }

        return redirect()->route('voting-sessions.show', $votingSession)
            ->with('success', 'Your vote has been submitted successfully!');
    }
}
