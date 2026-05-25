<?php

namespace App\Http\Controllers;

use App\Models\HafalanRecord;
use App\Models\Student;
use App\Models\Surah;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class HafalanRecordController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $hafalanRecords = HafalanRecord::query()
            ->with([
                'student.classRoom.program',
                'teacher.user',
                'surah',
            ])
            ->when($this->userHasRole($user, 'teacher'), function ($query) use ($user) {
                $query->where('teacher_id', $user->teacherProfile?->id);
            })
            ->when($request->filled('student_id'), function ($query) use ($request) {
                $query->where('student_id', $request->integer('student_id'));
            })
            ->when($request->filled('surah_id'), function ($query) use ($request) {
                $query->where('surah_id', $request->integer('surah_id'));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status')->toString());
            })
            ->latest('submitted_at')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('hafalan-records.index', array_merge(
            [
                'hafalanRecords' => $hafalanRecords,
            ],
            $this->formData($user)
        ));
    }

    public function create(Request $request): View
    {
        return view('hafalan-records.create', $this->formData($request->user()));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);

        HafalanRecord::query()->create($validated);

        return redirect()
            ->route('hafalan-records.index')
            ->with('success', 'Data hafalan berhasil ditambahkan.');
    }

    public function show(HafalanRecord $hafalanRecord): View
    {
        $hafalanRecord->load([
            'student.classRoom.program',
            'teacher.user',
            'surah',
        ]);

        return view('hafalan-records.show', [
            'hafalanRecord' => $hafalanRecord,
        ]);
    }

    public function edit(Request $request, HafalanRecord $hafalanRecord): View
    {
        $this->authorizeTeacherRecord($request, $hafalanRecord);

        return view('hafalan-records.edit', array_merge(
            [
                'hafalanRecord' => $hafalanRecord,
            ],
            $this->formData($request->user())
        ));
    }

    public function update(Request $request, HafalanRecord $hafalanRecord): RedirectResponse
    {
        $this->authorizeTeacherRecord($request, $hafalanRecord);

        $validated = $this->validatedData($request);

        $hafalanRecord->update($validated);

        return redirect()
            ->route('hafalan-records.index')
            ->with('success', 'Data hafalan berhasil diperbarui.');
    }

    public function destroy(Request $request, HafalanRecord $hafalanRecord): RedirectResponse
    {
        $this->authorizeTeacherRecord($request, $hafalanRecord);

        $hafalanRecord->delete();

        return redirect()
            ->route('hafalan-records.index')
            ->with('success', 'Data hafalan berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        $user = $request->user();

        $validated = $request->validate([
            'student_id' => [
                'required',
                'integer',
                Rule::exists('students', 'id')->whereNull('deleted_at'),
            ],
            'teacher_id' => [
                Rule::requiredIf(! $this->userHasRole($user, 'teacher')),
                'nullable',
                'integer',
                Rule::exists('teacher_profiles', 'id'),
            ],
            'surah_id' => [
                'required',
                'integer',
                Rule::exists('surahs', 'id'),
            ],
            'ayah_start' => [
                'required',
                'integer',
                'min:1',
            ],
            'ayah_end' => [
                'required',
                'integer',
                'min:1',
                'gte:ayah_start',
            ],
            'submission_type' => [
                'required',
                Rule::in(['new', 'continue', 'revision']),
            ],
            'score' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
            'status' => [
                'required',
                Rule::in(['passed', 'repeat', 'needs_improvement']),
            ],
            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'submitted_at' => [
                'required',
                'date',
            ],
        ]);

        $teacherId = $this->resolveTeacherId($request);
        $student = Student::query()->findOrFail((int) $validated['student_id']);
        $surah = Surah::query()->findOrFail((int) $validated['surah_id']);

        if ($this->userHasRole($user, 'teacher') && (int) $student->teacher_id !== (int) $teacherId) {
            abort(403, 'Santri ini bukan bimbingan guru yang sedang login.');
        }

        if ((int) $validated['ayah_end'] > (int) $surah->total_ayah) {
            throw ValidationException::withMessages([
                'ayah_end' => 'Ayat akhir tidak boleh melebihi total ayat surah ' . $surah->name_latin . '.',
            ]);
        }

        $validated['teacher_id'] = $teacherId;

        return $validated;
    }

    private function formData(User $user): array
    {
        $students = Student::query()
            ->with([
                'classRoom.program',
                'teacher.user',
            ])
            ->where('status', 'active')
            ->when($this->userHasRole($user, 'teacher'), function ($query) use ($user) {
                $query->where('teacher_id', $user->teacherProfile?->id);
            })
            ->orderBy('name')
            ->get();

        $teachers = TeacherProfile::query()
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->where('status', 'active');
            })
            ->get()
            ->sortBy(fn (TeacherProfile $teacher) => $teacher->user?->name)
            ->values();

        $surahs = Surah::query()
            ->orderBy('number')
            ->get();

        return [
            'students' => $students,
            'teachers' => $teachers,
            'surahs' => $surahs,
        ];
    }

    private function resolveTeacherId(Request $request): int
    {
        $user = $request->user();

        if ($this->userHasRole($user, 'teacher')) {
            $teacherId = $user->teacherProfile?->id;

            if (! $teacherId) {
                abort(403, 'Akun guru belum memiliki teacher profile.');
            }

            return (int) $teacherId;
        }

        return (int) $request->integer('teacher_id');
    }

    private function authorizeTeacherRecord(Request $request, HafalanRecord $hafalanRecord): void
    {
        $user = $request->user();

        if ($this->userHasRole($user, 'teacher') && (int) $hafalanRecord->teacher_id !== (int) $user->teacherProfile?->id) {
            abort(403);
        }
    }

    private function userHasRole(?User $user, string|array $roles): bool
    {
        foreach ((array) $roles as $role) {
            if ($user?->hasRole($role)) {
                return true;
            }
        }

        return false;
    }
}