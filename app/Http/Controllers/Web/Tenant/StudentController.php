<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Data Murid (sekolah) / Data Murid-Santri (pondok) -- tabel `students`. */
class StudentController extends TenantAdminController
{
    public function index(Request $request)
    {
        $students = Student::with('classRoom:id,name')
            ->where('company_id', $this->cid())
            ->when($request->filled('q'), fn ($q) => $q->where(function ($w) use ($request) {
                $w->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('nis', 'like', '%' . $request->q . '%')
                  ->orWhere('nisn', 'like', '%' . $request->q . '%');
            }))
            ->when($request->filled('class_id'), fn ($q) => $q->where('class_id', $request->class_id))
            ->when($request->status === 'nonaktif', fn ($q) => $q->where('is_active', false),
                fn ($q) => $q->where('is_active', true))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return $this->view('students.index', [
            'students' => $students,
            'classes'  => $this->classes(),
            'total'    => Student::where('company_id', $this->cid())->where('is_active', true)->count(),
        ]);
    }

    public function create()
    {
        return $this->view('students.form', [
            'student' => new Student(['is_active' => true, 'gender' => 'L']),
            'classes' => $this->classes(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['company_id'] = $this->cid();

        Student::create($data);

        return $this->to('students.index')->with('success', 'Murid berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        return $this->view('students.form', [
            'student' => $this->find($id),
            'classes' => $this->classes(),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $student = $this->find($id);
        $student->update($this->validated($request, $student));

        return $this->to('students.index')->with('success', 'Data murid diperbarui.');
    }

    public function destroy(int $id)
    {
        $this->find($id)->delete(); // soft delete, histori absen tetap aman

        return $this->to('students.index')->with('success', 'Murid dihapus.');
    }

    // ------------------------------------------------------------------

    private function find(int $id): Student
    {
        return Student::where('company_id', $this->cid())->findOrFail($id);
    }

    private function classes()
    {
        return ClassRoom::where('company_id', $this->cid())->where('is_active', true)
            ->orderBy('grade_level')->orderBy('name')->get(['id', 'name', 'academic_year']);
    }

    private function validated(Request $request, ?Student $student = null): array
    {
        $cid = $this->cid();

        $data = $request->validate([
            'nis'         => ['required', 'string', 'max:50',
                Rule::unique('students', 'nis')->where('company_id', $cid)->ignore($student?->id)],
            'nisn'        => ['nullable', 'string', 'max:50'],
            'name'        => ['required', 'string', 'max:255'],
            'gender'      => ['required', Rule::in(['L', 'P'])],
            'class_id'    => ['nullable', Rule::exists('class_rooms', 'id')->where('company_id', $cid)],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'birth_date'  => ['nullable', 'date'],
            'address'     => ['nullable', 'string', 'max:1000'],
            'enrolled_at' => ['nullable', 'date'],
        ]);

        $data['is_boarding'] = $this->company()->hasBoarding() ? $request->boolean('is_boarding') : false;
        $data['is_active']   = $request->boolean('is_active', true);

        return $data;
    }
}
