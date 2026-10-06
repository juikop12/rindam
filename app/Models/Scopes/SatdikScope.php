<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class SatdikScope implements Scope
{
    /**
     * Terapkan filter otomatis per Satdik bagi operator satuan.
     * Pimpinan, Super Admin, dan Tim ZI memiliki hak melihat seluruh Satdik.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();

        // Role dengan hak lintas Satdik
        $crossSatdikRoles = ['super_admin', 'pimpinan', 'kabag_diklat', 'kabag_dik', 'tim_zi'];
        if (in_array($user->role_code, $crossSatdikRoles)) {
            return;
        }

        // Jika user memiliki batasan Satdik tertentu
        if (!empty($user->satdik_id)) {
            $builder->where($model->getTable() . '.satdik_id', $user->satdik_id);
        }
    }
}
