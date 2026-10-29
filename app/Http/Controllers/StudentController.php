<?php

namespace App\Http\Controllers;

use App\Http\Requests\Student\StoreRequest;
use App\Http\Requests\Student\UpdateRequest;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Sistem Sekolah - Daftar Siswa';

        $search = $request->input('search');
        $classId = $request->query('class_id');
        $majorId = $request->query('major_id');

        $students = Student::with(['schoolClass', 'major'])
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                });
            })
            ->when($classId, fn ($query, $classId) => $query->where('class_id', $classId))
            ->when($majorId, fn ($query, $majorId) => $query->where('major_id', $majorId))
            ->paginate(5)
            ->withQueryString();

        $schoolClasses = SchoolClass::orderBy('name')->get();
        $majors = Major::orderBy('name')->get();

        return view('students.index', [
            'title' => $title,
            'students' => $students,
            'schoolClasses' => $schoolClasses,
            'majors' => $majors,
        ]);
    }
 
    public function show(Student $student)
    {
         $title ='Sistem Sekolah - Menampilkan Nama Siswa';

        return view('students.show', [
            'title' => $title,
            'student' => $student
        ]);
    }
   
    public function create()
    {
        $title = 'Sistem Sekolah - Membuat Daftar Siswa';

        $schoolClasses = SchoolClass::orderBy('name')->get();
        $majors = Major::orderBy('name')->get();

        return view('students.create', [
            'title' => $title,
            'schoolClasses' => $schoolClasses,
            'majors' => $majors,
        ]);
    }

    public function edit(Student $student)
    {
        $title = 'Sistem Sekolah - Mengubah Daftar Siswa';

        $schoolClasses = SchoolClass::orderBy('name')->get();
        $majors = Major::orderBy('name')->get();

        return view('students.edit', [
            'title' => $title,
            'student' => $student,
            'schoolClasses' => $schoolClasses,
            'majors' => $majors,
        ]);
    }
 
    public function store(StoreRequest $request)
    {
        $validatedrequest = $request->validated();

        Student::create($validatedrequest);
        return redirect()->route('students.index');
    }
 
    public function update(student $student, UpdateRequest $request)
    {
           $validatedrequest = $request->validated();

        $student->update($validatedrequest);
        return redirect()->route('students.index');
    }
 
    public function destroy(student $student)
    {
        $student->delete();

        return redirect()->route('students.index');
    }
 
 
}
