<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserTopicQuizRequest;
use App\Http\Requests\UpdateUserTopicQuizRequest;
use App\Models\Quiz;
use App\Models\Topic;
use App\Models\UserTopicQuiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserTopicQuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreUserTopicQuizRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(UserTopicQuiz $userTopicQuiz)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserTopicQuiz $userTopicQuiz)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserTopicQuizRequest $request, UserTopicQuiz $userTopicQuiz)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserTopicQuiz $userTopicQuiz)
    {
        //
    }
}
