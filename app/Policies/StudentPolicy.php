<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    /**
     * Izinkan melihat daftar siswa
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Izinkan melihat detail umum siswa
     */
    public function view(User $user, Student $student): bool
    {
        return $user->canAccessSatdik($student->satdik_id);
    }

    /**
     * Izinkan membuat siswa baru
     */
    public function create(User $user): bool
    {
        return $user->canModifyData() && ($user->isSuperAdmin() || $user->isOperatorDanrindam() || $user->isOperatorSatdik());
    }

    /**
     * Izinkan memperbarui data siswa
     */
    public function update(User $user, Student $student): bool
    {
        return $user->canManageSatdik($student->satdik_id);
    }

    /**
     * Izinkan menghapus siswa
     */
    public function delete(User $user, Student $student): bool
    {
        return $user->canManageSatdik($student->satdik_id);
    }

    /**
     * Kebijakan khusus: Membuka data pribadi sensitif terproteksi (SIPANDU-WBK)
     */
    public function viewSensitiveData(User $user, Student $student): bool
    {
        return $user->canAccessSatdik($student->satdik_id);
    }
}
