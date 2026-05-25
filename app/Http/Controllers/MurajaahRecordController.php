<?php

namespace App\Http\Controllers;

use App\Models\MurajaahRecord;
use App\Models\Student;
use App\Models\Surah;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MurajaahRecordController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $murajaahRecords = MurajaahRecord::query()
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
            ->latest('reviewed_at')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('murajaah-records.index', array_merge(
            [
                'murajaahRecords' => $murajaahRecords,
            ],
            $this->formData($user)
        ));
    }

    public function create(Request $request): View
    {
        return view('murajaah-records.create', $this->formData($request->user()));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);

        MurajaahRecord::query()->create($validated);

        return redirect()
            ->route('murajaah-records.index')
            ->with('success', 'Data murajaah berhasil ditambahkan.');
    }

    public function show(MurajaahRecord $murajaahRecord): View
    {
        $murajaahRecord->load([
            'student.classRoom.program',
            'teacher.user',
            'surah',
        ]);

        return view('murajaah-records.show', [
            'murajaahRecord' => $murajaahRecord,
        ]);
    }

    public function edit(Request $request, MurajaahRecord $murajaahRecord): View
    {
        $this->authorizeTeacherRecord($request, $murajaahRecord);

        return view('murajaah-records.edit', array_merge(
            [
                'murajaahRecord' => $murajaahRecord,
            ],
            $this->formData($request->user())
        ));
    }

    public function update(Request $request, MurajaahRecord $murajaahRecord): RedirectResponse
    {
        $this->authorizeTeacherRecord($request, $murajaahRecord);

        $validated = $this->validatedData($request);

        $murajaahRecord->update($validated);

        return redirect()
            ->route('murajaah-records.index')
            ->with('success', 'Data murajaah berhasil diperbarui.');
    }

    public function destroy(Request $request, MurajaahRecord $murajaahRecord): RedirectResponse
    {
        $this->authorizeTeacherRecord($request, $murajaahRecord);

        $murajaahRecord->delete();

        return redirect()
            ->route('murajaah-records.index')
            ->with('success', 'Data murajaah berhasil dihapus.');
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
            'fluency_score' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
            'tajwid_score' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
            'makhraj_score' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
            'overall_score' => [
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
            'reviewed_at' => [
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

        if (
            blank($validated['overall_score'] ?? null)
            && filled($validated['fluency_score'] ?? null)
            && filled($validated['tajwid_score'] ?? null)
            && filled($validated['makhraj_score'] ?? null)
        ) {
            $validated['overall_score'] = round((
                (float) $validated['fluency_score']
                + (float) $validated['tajwid_score']
                + (float) $validated['makhraj_score']
            ) / 3, 2);
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

    private function authorizeTeacherRecord(Request $request, MurajaahRecord $murajaahRecord): void
    {
        $user = $request->user();

        if ($this->userHasRole($user, 'teacher') && (int) $murajaahRecord->teacher_id !== (int) $user->teacherProfile?->id) {
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