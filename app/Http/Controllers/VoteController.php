<?php

namespace App\Http\Controllers;

use App\Models\VotingSession;
use App\Models\Vote;
use App\Models\JudgeSelection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class VoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, VotingSession $votingSession)
    {
        if (!$votingSession->isActive()) {
            return back()->with('error', 'Voting session is not active.');
        }

        if ($votingSession->votes()->where('user_id', auth()->id())->exists()) {
            return back()->with('error', 'You have already voted in this session.');
        }

        if ($votingSession->type === 'single_issue') {
            $request->validate([
                'vote_choice' => 'required|boolean',
            ]);

            Vote::create([
                'session_id' => $votingSession->id,
                'user_id' => auth()->id(),
                'vote_choice' => $request->vote_choice,
            ]);
        } else {
            $request->validate([
                'judges' => ['required', 'array', 'size:' . $votingSession->judges_count],
                'judges.*' => [
                    'required',
                    'exists:users,id',
                    Rule::notIn([auth()->id()]),
                    function ($attribute, $value, $fail) {
                        if (!User::find($value)->is_judge_eligible) {
                            $fail('The selected judge is not eligible.');
                        }
                    }
                ],
            ], [
                'judges.size' => 'You must select exactly ' . $votingSession->judges_count . ' judges.',
                'judges.*.not_in' => 'You cannot select yourself as a judge.',
            ]);

            $vote = Vote::create([
                'session_id' => $votingSession->id,
                'user_id' => auth()->id(),
            ]);

            foreach ($request->judges as $judgeId) {
                JudgeSelection::create([
                    'vote_id' => $vote->id,
                    'selected_judge_id' => $judgeId,
                ]);
            }
        }

        return redirect()->route('voting-sessions.show', $votingSession)
            ->with('success', 'Your vote has been submitted successfully.');
    }
}
