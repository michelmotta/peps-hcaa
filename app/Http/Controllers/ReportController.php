<?php

namespace App\Http\Controllers;

use App\Enums\LessonStatusEnum;
use App\Models\Lesson;
use App\Models\LessonUser;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReportController extends Controller
{

    public function reportByPeriod(Request $request)
    {
        $lessons = collect();
        $filter = [];

        if ($request->filled(['start_date', 'end_date'])) {
            $validated = $request->validate(
                [
                    'start_date' => 'required|date',
                    'end_date'   => [
                        'required',
                        'date',
                        'after_or_equal:start_date',
                        function ($attribute, $value, $fail) use ($request) {
                            $startDate = Carbon::parse($request->input('start_date'));
                            $endDate = Carbon::parse($value);

                            if ($startDate->diffInDays($endDate) > 365) {
                                $fail('Devido à quantidade de dados, o intervalo máximo permitido por relatório é de 1 ano.');
                            }
                        },
                    ],
                ],
                [
                    'end_date.after_or_equal' => 'A data final não pode ser anterior à data inicial do período.'
                ]
            );

            $startDate = Carbon::parse($validated['start_date'])->startOfDay();
            $endDate = Carbon::parse($validated['end_date'])->endOfDay();
            $filter = ['start_date' => $validated['start_date'], 'end_date' => $validated['end_date']];

            $lessons = Lesson::with([
                'teacher',
                'file',
                'specialties',
                'topics',
                'subscriptions' => function ($query) use ($startDate, $endDate) {
                    $query->where('lesson_user.finished', true)
                        ->whereBetween('lesson_user.finished_at', [$startDate, $endDate])
                        ->with('file');
                }
            ])
                ->withCount('subscriptions as total_subscriptions_count')
                ->whereHas('subscriptions', function ($query) use ($startDate, $endDate) {
                    $query->where('lesson_user.finished', true)
                        ->whereBetween('lesson_user.finished_at', [$startDate, $endDate]);
                })
                ->orderBy('name', 'asc')
                ->get();
        }

        return view('dashboard.reports.report_period', compact('lessons', 'filter'));
    }

    public function exportPeriodsPdf(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($validated['start_date'])->startOfDay();
        $endDate = Carbon::parse($validated['end_date'])->endOfDay();

        $lessons = Lesson::with([
            'teacher',
            'topics',
            'subscriptions' => function ($query) use ($startDate, $endDate) {
                $query->where('lesson_user.finished', true)
                    ->whereBetween('lesson_user.finished_at', [$startDate, $endDate]);
            }
        ])
            ->withCount('subscriptions as total_subscriptions_count')
            ->whereHas('subscriptions', function ($query) use ($startDate, $endDate) {
                $query->where('lesson_user.finished', true)
                    ->whereBetween('lesson_user.finished_at', [$startDate, $endDate]);
            })
            ->orderBy('name', 'asc')
            ->get();

        $pdf = Pdf::loadView('dashboard.reports.report_period_pdf', [
            'lessons'    => $lessons,
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ]);

        $fileName = 'relatorio-aulas-por-periodo-' . $startDate->format('Y-m-d') . '-a-' . $endDate->format('Y-m-d') . '.pdf';

        return $pdf->stream($fileName);
    }

    public function reportByStudent(Request $request)
    {
        $student = null;
        $completedWorkload = 0;
        $subscriptions = collect();

        $validated = $request->validate([
            'student_id' => 'sometimes|required|integer|exists:users,id',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ]);

        if (isset($validated['student_id'])) {
            $studentId = $validated['student_id'];

            $dateFilters = function ($query) use ($request) {
                $query->when($request->start_date, function ($q, $date) {
                    $q->whereDate('lesson_user.created_at', '>=', $date);
                })
                    ->when($request->end_date, function ($q, $date) {
                        $q->whereDate('lesson_user.created_at', '<=', $date);
                    });
            };

            $student = User::with(['file', 'certificates', 'lastLogin'])
                ->withCount([
                    'subscriptions' => $dateFilters,
                    'completedSubscriptions' => $dateFilters,
                    'pendingSubscriptions' => $dateFilters,
                ])
                ->findOrFail($studentId);

            $subscriptionsQuery = $student->subscriptions()->with(['file', 'specialties', 'teacher']);
            $dateFilters($subscriptionsQuery);

            $completedWorkloadQuery = $student->subscriptions()->where('lesson_user.finished', true);
            $dateFilters($completedWorkloadQuery);
            $completedWorkload = $completedWorkloadQuery->sum('lessons.workload');

            $subscriptions = $subscriptionsQuery->paginate(15)->withQueryString();
        }

        return view('dashboard.reports.report_student', compact('student', 'subscriptions', 'completedWorkload'));
    }

    public function exportStudentsPdf(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|integer|exists:users,id',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ]);

        $studentId = $validated['student_id'];

        $dateFilters = function ($query) use ($request) {
            $query->when($request->start_date, function ($q, $date) {
                $q->whereDate('lesson_user.created_at', '>=', $date);
            })
                ->when($request->end_date, function ($q, $date) {
                    $q->whereDate('lesson_user.created_at', '<=', $date);
                });
        };

        $student = User::with(['file', 'certificates', 'lastLogin'])
            ->withCount([
                'subscriptions' => $dateFilters,
                'completedSubscriptions' => $dateFilters,
                'pendingSubscriptions' => $dateFilters,
            ])
            ->findOrFail($studentId);

        $completedWorkloadQuery = $student->subscriptions()->where('lesson_user.finished', true);
        $dateFilters($completedWorkloadQuery);
        $completedWorkload = $completedWorkloadQuery->sum('lessons.workload');

        $subscriptionsQuery = $student->subscriptions()->with(['file', 'specialties', 'teacher']);
        $dateFilters($subscriptionsQuery);
        $subscriptions = $subscriptionsQuery->get();

        $pdf = Pdf::loadView('dashboard.reports.report_student_pdf', [
            'student'           => $student,
            'subscriptions'     => $subscriptions,
            'completedWorkload' => $completedWorkload,
            'start_date'        => $request->start_date,
            'end_date'          => $request->end_date,
        ]);

        $fileName = 'relatorio-' . Str::slug($student->name) . '.pdf';

        return $pdf->stream($fileName);
    }

    public function reportByTeacher(Request $request)
    {
        $teacher = null;
        $lessons = collect();
        $stats = [
            'created_lessons_count' => 0,
            'total_students' => 0,
            'status_counts' => collect(),
        ];

        $validated = $request->validate([
            'teacher_id' => 'sometimes|required|integer|exists:users,id',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ]);

        if (isset($validated['teacher_id'])) {
            $teacher = User::findOrFail($validated['teacher_id']);

            $lessonsQuery = $teacher->createdLessons()
                ->when($request->start_date, fn($q, $date) => $q->where('created_at', '>=', $date))
                ->when($request->end_date, fn($q, $date) => $q->where('created_at', '<=', $date));

            $filteredLessonIds = $lessonsQuery->pluck('id');

            if ($filteredLessonIds->isNotEmpty()) {
                $statusCounts = Lesson::whereIn('id', $filteredLessonIds)
                    ->toBase()
                    ->selectRaw('lesson_status, count(*) as count')
                    ->groupBy('lesson_status')
                    ->pluck('count', 'lesson_status');

                $stats['created_lessons_count'] = $filteredLessonIds->count();
                $stats['total_students'] = LessonUser::whereIn('lesson_id', $filteredLessonIds)->distinct('user_id')->count();
                $stats['status_counts'] = $statusCounts;

                $lessons = $lessonsQuery->with(['specialties', 'file'])
                    ->withCount(['subscriptions', 'completedSubscriptions'])
                    ->withAvg('subscriptions as average_score', 'lesson_user.score')
                    ->latest()
                    ->paginate(10)
                    ->withQueryString();
            }
        }

        return view('dashboard.reports.report_teacher', compact('teacher', 'lessons', 'stats'));
    }

    public function exportTeachersPdf(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
        ]);

        $teacher = User::findOrFail($request->teacher_id);
        $lessonIds = Lesson::where('user_id', $teacher->id)->pluck('id');

        $lessonsQuery = Lesson::whereIn('id', $lessonIds)
            ->with(['specialties', 'topics'])
            ->withCount('subscriptions');

        if ($request->filled('start_date')) {
            $lessonsQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $lessonsQuery->whereDate('created_at', '<=', $request->end_date);
        }

        $lessons = $lessonsQuery->orderBy('created_at', 'desc')->get();

        $stats = [
            'total_students' => LessonUser::whereIn('lesson_id', $lessonIds)
                ->distinct('user_id')
                ->count('user_id'),

            'created_lessons_count' => $lessons->count(),
            'status_counts' => [
                LessonStatusEnum::PUBLICADA->value => $lessons->where('lesson_status', LessonStatusEnum::PUBLICADA->value)->count(),
            ],
        ];

        $data = [
            'teacher'   => $teacher,
            'lessons'   => $lessons,
            'stats'     => $stats,
        ];

        $pdf = Pdf::loadView('dashboard.reports.report_teacher_pdf', $data);

        $fileName = 'relatorio-' . Str::slug($teacher->name) . '.pdf';

        return $pdf->stream($fileName);
    }

    public function reportByLesson(Request $request)
    {
        $lesson = null;
        $students = collect();

        $validated = $request->validate([
            'lesson_id' => 'nullable|integer|exists:lessons,id',
        ]);

        if (isset($validated['lesson_id'])) {
            $lesson = Lesson::with([
                'teacher',
                'specialties',
                'file',
                'topics',
            ])
                ->withCount([
                    'subscriptions',
                    'completedSubscriptions',
                ])
                ->withAvg('subscriptions as average_score', 'lesson_user.score')
                ->find($validated['lesson_id']);

            if ($lesson) {
                $students = $lesson->subscriptions()
                    ->with('file')
                    ->latest()
                    ->paginate(10);
            }
        }

        return view('dashboard.reports.report_lesson', [
            'lesson'   => $lesson,
            'students' => $students,
        ]);
    }

    public function exportLessonsPdf(Request $request)
    {
        $validated = $request->validate([
            'lesson_id' => 'required|integer|exists:lessons,id',
        ]);

        $lesson = Lesson::with([
            'teacher',
            'specialties',
            'file',
            'topics',
        ])
            ->withCount([
                'subscriptions',
                'completedSubscriptions',
            ])
            ->withAvg('subscriptions as average_score', 'lesson_user.score')
            ->find($validated['lesson_id']);

        if (!$lesson) {
            return redirect()->back()->with('error', 'Aula não encontrada para exportação.');
        }

        $students = $lesson->subscriptions()->with('file')->latest()->get();

        $pdf = PDF::loadView('dashboard.reports.report_lesson_pdf', [
            'lesson'   => $lesson,
            'students' => $students,
        ]);

        $fileName = 'relatorio-' . Str::slug($lesson->name) . '.pdf';

        return $pdf->stream($fileName);
    }
}
