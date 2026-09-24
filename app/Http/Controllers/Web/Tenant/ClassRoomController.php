<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Data Kelas -- tabel `class_rooms`. */
class ClassRoomController extends TenantAdminController
{
    public function index(Request $request)
    {
        $year = $request->get('year');

        $classes = ClassRoom::with('homeroomTeacher:id,name')
            ->withCount(['students as students_count' => fn ($q) => $q->where('is_active', true)])
            ->where('company_id', $this->cid())
            ->when($year, fn ($q) => $q->where('academic_year', $year))
            ->orderByDesc('is_active')->orderBy('grade_level')->orderBy('name')
            ->get();

        return $this->view('classes.index', [
            'classes' => $classes,
            'years'   => ClassRoom::where('company_id', $this->cid())->distinct()->orderByDesc('academic_year')->pluck('academic_year'),
            'year'    => $year,
        ]);
    }

    public function create()
    {
        return $this->view('classes.form', [
            'class'    => new ClassRoom(['is_active' => true, 'academic_year' => $this->defaultYear()]),
            'teachers' => $this->teachers(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['company_id'] = $this->cid();
        ClassRoom::create($data);

        return $this->to('classes.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        return $this->view('classes.form', [
            'class'    => $this->find($id),
            'teachers' => $this->teachers(),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $class = $this->find($id);
        $class->update($this->validated($request, $class));

        return $this->to('classes.index')->with('success', 'Kelas diperbarui.');
    }

    public function destroy(int $id)
    {
        $class = $this->find($id);

        if ($class->students()->exists()) {
            return $this->to('classes.index')->with('error', 'Kelas masih berisi murid. Pindahkan muridnya dulu.');
        }

        $class->delete();

        return $this->to('classes.index')->with('success', 'Kelas dihapus.');
    }

    // ------------------------------------------------------------------

    private function find(int $id): ClassRoom
    {
        return ClassRoom::where('company_id', $this->cid())->findOrFail($id);
    }

    private function teachers()
    {
        return User::where('company_id', $this->cid())
            ->whereIn('role', ['teacher', 'ustadz'])->orderBy('name')->get(['id', 'name']);
    }

    private function defaultYear(): string
    {
        $y = (int) now()->format('Y');

        return now()->month >= 7 ? "$y/" . ($y + 1) : ($y - 1) . "/$y";
    }

    private function validated(Request $request, ?ClassRoom $class = null): array
    {
        $cid = $this->cid();

        $data = $request->validate([
            'name'                => ['required', 'string', 'max:50',
                Rule::unique('class_rooms', 'name')
                    ->where('company_id', $cid)
                    ->where('academic_year', $request->academic_year)
                    ->ignore($class?->id)],
            'grade_level'         => ['required', 'integer', 'between:1,12'],
            'academic_year'       => ['required', 'string', 'max:20'],
            'homeroom_teacher_id' => ['nullable', Rule::exists('users', 'id')->where('company_id', $cid)],
        ], ['name.unique' => 'Nama kelas itu sudah dipakai di tahun ajaran yang sama.']);

        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
