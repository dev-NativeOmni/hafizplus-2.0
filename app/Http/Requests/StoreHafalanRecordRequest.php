<?php

namespace App\Http\Requests;

use App\Models\Student;
use App\Models\Surah;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHafalanRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['super_admin', 'admin', 'teacher']) ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->user()?->hasRole('teacher')) {
            $this->merge([
                'teacher_id' => $this->user()->teacherProfile?->id,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'student_id' => [
                'required',
                'integer',
                Rule::exists('students', 'id')->whereNull('deleted_at'),
            ],
            'teacher_id' => [
                Rule::requiredIf(! $this->user()?->hasRole('teacher')),
                'nullable',
                'integer',
                Rule::exists('teacher_profiles', 'id'),
            ],
            'surah_id' => [
                'required',
                'integer',
                Rule::exists('surahs', 'id'),
            ],
            'surah_end_id' => [
                'nullable',
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
            ],
            'submission_type' => [
                'required',
                Rule::in(['new', 'continuation', 'revision']),
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
                'before_or_equal:today',
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $surahStart = Surah::find($this->input('surah_id'));
            $surahEndId = $this->input('surah_end_id') ?: $this->input('surah_id');
            $surahEnd = Surah::find($surahEndId);

            if ($surahStart && $surahEnd) {
                if ($surahEnd->number < $surahStart->number) {
                    $validator->errors()->add(
                        'surah_end_id',
                        'Surah akhir tidak boleh mendahului surah mulai.'
                    );
                }

                if ((int) $surahEndId === (int) $this->input('surah_id')) {
                    if ((int) $this->input('ayah_end') < (int) $this->input('ayah_start')) {
                        $validator->errors()->add(
                            'ayah_end',
                            'Ayat akhir harus lebih besar atau sama dengan ayat mulai.'
                        );
                    }
                }

                if ((int) $this->input('ayah_end') > $surahEnd->total_ayah) {
                    $validator->errors()->add(
                        'ayah_end',
                        'Ayat akhir tidak boleh melebihi jumlah ayat surah ' . $surahEnd->name_latin . ' (' . $surahEnd->total_ayah . ' ayat).'
                    );
                }
            }

            $student = Student::find($this->input('student_id'));

            if ($student && $student->status !== 'active') {
                $validator->errors()->add(
                    'student_id',
                    'Santri nonaktif tidak bisa menerima input setoran hafalan.'
                );
            }

            if ($student && ! $student->teacher_id) {
                $validator->errors()->add(
                    'student_id',
                    'Santri ini belum memiliki guru pembimbing.'
                );
            }

            if ($student && $this->user()?->hasRole('teacher')) {
                $teacherId = $this->user()?->teacherProfile?->id;

                if (! $teacherId || (int) $student->teacher_id !== (int) $teacherId) {
                    $validator->errors()->add(
                        'student_id',
                        'Guru hanya boleh input setoran untuk santri bimbingannya.'
                    );
                }
            }
        });
    }

    public function attributes(): array
    {
        return [
            'student_id' => 'santri',
            'surah_id' => 'surah mulai',
            'surah_end_id' => 'surah akhir',
            'ayah_start' => 'ayat mulai',
            'ayah_end' => 'ayat akhir',
            'submission_type' => 'jenis setoran',
            'score' => 'nilai',
            'status' => 'status setoran',
            'notes' => 'catatan',
            'submitted_at' => 'tanggal setoran',
        ];
    }
}