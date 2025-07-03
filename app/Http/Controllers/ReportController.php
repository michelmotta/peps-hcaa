<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{

    public function reportByStudent(Request $request)
    {
        $student = null;
        $subscriptions = collect();

        if ($request->filled('student_id')) {
            $student = User::with('file', 'certificates')->find($request->student_id);

            if ($student) {
                $subscriptions = $student->subscriptions()
                    ->with('file', 'specialty')
                    ->paginate(15)
                    ->withQueryString();
            }
        }

        return view('dashboard.reports.report_student', [
            'student' => $student,
            'subscriptions' => $subscriptions,
        ]);
    }

    public function reportByLesson(Request $request)
    {
        $lesson = null;
        $subscriptions = collect();

        if ($request->filled('lesson_id') && is_numeric($request->lesson_id)) {
            $lesson = Lesson::with(['file', 'specialty', 'certificates'])
                ->find($request->lesson_id);

            if ($lesson) {
                $subscriptions = $lesson->subscriptions()
                    ->with('file')
                    ->withPivot(['score', 'finished', 'finished_at', 'created_at'])
                    ->paginate(10)
                    ->withQueryString();
            }
        }

        return view('dashboard.reports.report_lesson', [
            'lesson' => $lesson,
            'subscriptions' => $subscriptions,
        ]);
    }
}
