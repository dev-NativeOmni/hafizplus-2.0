<?php

namespace App\Services;

use App\Models\HafalanRecord;
use App\Models\HafalanTarget;
use App\Models\MurajaahRecord;
use App\Models\ParentProfile;
use App\Models\Student;
use App\Models\Surah;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;


class StudentProgressService
{
    public function visibleStudentQuery(?User $user): Builder
    {
        $query = Student::query();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($this->userHasAnyRole($user, ['super_admin', 'admin', 'headmaster', 'supervisor', 'coordinator_tahfizh'])) {
            return $query;
        }

        if ($this->userHasAnyRole($user, ['teacher'])) {
            $teacherId = $user->teacherProfile?->id
                ?? TeacherProfile::query()->where('user_id', $user->id)->value('id');

            if (! $teacherId) {
                return $query->whereRaw('1 = 0');
            }

            // students.teacher_id selalu ada (confirmed dari migration)
            return $query->where('teacher_id', $teacherId);
        }

        if ($this->userHasAnyRole($user, ['parent'])) {
            $parentId = $user->parentProfile?->id
                ?? ParentProfile::query()->where('user_id', $user->id)->value('id');

            if (! $parentId) {
                return $query->whereRaw('1 = 0');
            }

            // Relasi parent-student via pivot table parent_student (confirmed dari migration)
            return $query->whereIn('id', function ($subQuery) use ($parentId) {
                $subQuery->select('student_id')
                    ->from('parent_student')
                    ->where('parent_id', $parentId);
            });
        }

        if ($this->userHasAnyRole($user, ['student'])) {
            // students.user_id selalu ada (confirmed dari migration)
            return $query->where('user_id', $user->id);
        }

        return $query->whereRaw('1 = 0');
    }

    public function buildRows(Collection $students): Collection
    {
        return $students
            ->map(fn (Student $student) => $this->calculate($student))
            ->values();
    }

    public function calculate(Student $student): array
    {
        $totalQuranAyahs = $this->totalQuranAyahs();
        $memorizedAyahs = $this->memorizedAyahCount($student);

        $progressPercent = $totalQuranAyahs > 0
            ? round(($memorizedAyahs / $totalQuranAyahs) * 100, 2)
            : 0;

        $hafalanRecordsQuery = HafalanRecord::query()
            ->where('student_id', $student->id);

        $murajaahRecordsQuery = MurajaahRecord::query()
            ->where('student_id', $student->id);

        $targetQuery = HafalanTarget::query()
            ->where('student_id', $student->id);

        $latestHafalan = (clone $hafalanRecordsQuery)
            ->with('surah')
            ->latest('submitted_at')
            ->latest()
            ->first();

        $latestMurajaah = (clone $murajaahRecordsQuery)
            ->with('surah')
            ->latest('reviewed_at')
            ->latest()
            ->first();

        $activeTargetStatuses = ['active', 'planned', 'in_progress'];

        return [
            'student' => $student,
            'student_id' => $student->id,
            'student_name' => $student->name,
            'student_number' => $student->student_number ?? null,
            'class_room_name' => $student->classRoom?->name,
            'program_name' => $student->classRoom?->program?->name,

            'total_quran_ayahs' => $totalQuranAyahs,
            'memorized_ayahs' => $memorizedAyahs,
            'remaining_ayahs' => max(0, $totalQuranAyahs - $memorizedAyahs),
            'progress_percent' => $progressPercent,

            'total_hafalan_records' => (clone $hafalanRecordsQuery)->count(),
            'passed_hafalan_records' => (clone $hafalanRecordsQuery)
                ->where('status', 'passed')
                ->count(),
            'repeat_hafalan_records' => (clone $hafalanRecordsQuery)
                ->whereIn('status', ['repeat', 'needs_improvement'])
                ->count(),

            'total_murajaah_records' => (clone $murajaahRecordsQuery)->count(),
            'average_hafalan_score' => round((float) (clone $hafalanRecordsQuery)->avg('score'), 2),
            'average_murajaah_score' => $this->averageMurajaahScore($student),

            'total_targets' => (clone $targetQuery)->count(),
            'active_targets' => (clone $targetQuery)
                ->whereIn('status', $activeTargetStatuses)
                ->count(),
            'completed_targets' => (clone $targetQuery)
                ->where('status', 'completed')
                ->count(),
            'missed_targets' => (clone $targetQuery)
                ->where('status', 'missed')
                ->count(),
            'overdue_targets' => (clone $targetQuery)
                ->whereIn('status', $activeTargetStatuses)
                ->whereDate('target_date', '<', today())
                ->count(),

            'latest_hafalan_surah' => $latestHafalan?->surah?->name_latin
                ?? $latestHafalan?->surah?->name
                ?? null,
            'latest_hafalan_ayah' => $latestHafalan
                ? $latestHafalan->ayah_start . ' - ' . $latestHafalan->ayah_end
                : null,
            'latest_hafalan_date' => $latestHafalan?->submitted_at,

            'latest_murajaah_surah' => $latestMurajaah?->surah?->name_latin
                ?? $latestMurajaah?->surah?->name
                ?? null,
            'latest_murajaah_ayah' => $latestMurajaah
                ? $latestMurajaah->ayah_start . ' - ' . $latestMurajaah->ayah_end
                : null,
            'latest_murajaah_date' => $latestMurajaah?->reviewed_at,
        ];
    }

    public function summaryFromRows(Collection $rows): array
    {
        return [
            'total_students' => $rows->count(),
            'total_memorized_ayahs' => (int) $rows->sum('memorized_ayahs'),
            'total_hafalan_records' => (int) $rows->sum('total_hafalan_records'),
            'total_murajaah_records' => (int) $rows->sum('total_murajaah_records'),
            'total_active_targets' => (int) $rows->sum('active_targets'),
            'total_overdue_targets' => (int) $rows->sum('overdue_targets'),
            'average_progress_percent' => round((float) $rows->avg('progress_percent'), 2),
            'average_hafalan_score' => round((float) $rows->avg('average_hafalan_score'), 2),
            'average_murajaah_score' => round((float) $rows->avg('average_murajaah_score'), 2),
        ];
    }

    private function memorizedAyahCount(Student $student): int
    {
        $records = HafalanRecord::query()
            ->where('student_id', $student->id)
            ->where('status', 'passed')
            ->whereNotNull('surah_id')
            ->whereNotNull('ayah_start')
            ->whereNotNull('ayah_end')
            ->get(['surah_id', 'ayah_start', 'ayah_end'])
            ->groupBy('surah_id');

        if ($records->isEmpty()) {
            return 0;
        }

        $surahTotals = Surah::query()
            ->pluck('total_ayah', 'id');

        $total = 0;

        foreach ($records as $surahId => $surahRecords) {
            $surahTotalAyah = (int) ($surahTotals[$surahId] ?? 0);

            if ($surahTotalAyah <= 0) {
                continue;
            }

            $intervals = [];

            foreach ($surahRecords as $record) {
                $start = max(1, (int) $record->ayah_start);
                $end = min($surahTotalAyah, (int) $record->ayah_end);

                if ($start <= $end) {
                    $intervals[] = [$start, $end];
                }
            }

            $total += $this->countMergedIntervals($intervals);
        }

        return $total;
    }

    private function countMergedIntervals(array $intervals): int
    {
        if (empty($intervals)) {
            return 0;
        }

        usort($intervals, fn (array $a, array $b) => $a[0] <=> $b[0]);

        $merged = [];

        foreach ($intervals as [$start, $end]) {
            if (empty($merged)) {
                $merged[] = [$start, $end];

                continue;
            }

            $lastIndex = count($merged) - 1;

            if ($start <= $merged[$lastIndex][1] + 1) {
                $merged[$lastIndex][1] = max($merged[$lastIndex][1], $end);
            } else {
                $merged[] = [$start, $end];
            }
        }

        $count = 0;

        foreach ($merged as [$start, $end]) {
            $count += ($end - $start + 1);
        }

        return $count;
    }

    private function averageMurajaahScore(Student $student): float
    {
        // murajaah_records.overall_score selalu ada (confirmed dari migration)
        return round((float) MurajaahRecord::query()
            ->where('student_id', $student->id)
            ->avg('overall_score'), 2);
    }

    private function totalQuranAyahs(): int
    {
        $total = (int) Surah::query()->sum('total_ayah');

        return $total > 0 ? $total : 6236;
    }

    private function userHasAnyRole(User $user, array $roles): bool
    {
        foreach ($roles as $role) {
            if (method_exists($user, 'hasRole') && $user->hasRole($role)) {
                return true;
            }

            if (($user->role?->name ?? null) === $role) {
                return true;
            }
        }

        return false;
    }
}