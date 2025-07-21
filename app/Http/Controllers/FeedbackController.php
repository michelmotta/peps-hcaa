<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Http\Requests\UpdateFeedbackRequest;
use App\Models\Feedback;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Lesson $lesson)
    {
        $searchTerm = $request->input('q');

        $baseQuery = Feedback::query()->where('lesson_id', $lesson->id);

        $allFeedbacks = $baseQuery->get();

        $totalFeedbacks = $allFeedbacks->count();
        $averageRating = $totalFeedbacks > 0 ? $allFeedbacks->avg('rating') : 0;

        $ratingsCount = $allFeedbacks->groupBy('rating')->map->count();

        $positiveCount = ($ratingsCount[5] ?? 0) + ($ratingsCount[4] ?? 0);
        $negativeCount = ($ratingsCount[2] ?? 0) + ($ratingsCount[1] ?? 0);

        $positivePercentage = $totalFeedbacks > 0 ? ($positiveCount / $totalFeedbacks) * 100 : 0;
        $negativePercentage = $totalFeedbacks > 0 ? ($negativeCount / $totalFeedbacks) * 100 : 0;

        $feedbacksQuery = $baseQuery->when(
            $searchTerm,
            fn($q) => $q->whereIn('id', Feedback::search($searchTerm)->keys())
        )->latest();

        $feedbacks = $feedbacksQuery->paginate(10)->withQueryString();

        return view('dashboard.feedbacks.index', compact(
            'lesson',
            'feedbacks',
            'averageRating',
            'totalFeedbacks',
            'ratingsCount',
            'positivePercentage',
            'negativePercentage'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Lesson $lesson)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:1000',
        ]);

        Feedback::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'lesson_id' => $lesson->id,
            ],
            [
                'rating' => $validated['rating'],
                'comentario' => $validated['comentario'] ?? null,
            ]
        );

        return response()->json(['message' => 'Avaliação enviada com sucesso!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Feedback $feedback)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Feedback $feedback)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFeedbackRequest $request, Feedback $feedback)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Feedback $feedback)
    {
        //
    }
}
