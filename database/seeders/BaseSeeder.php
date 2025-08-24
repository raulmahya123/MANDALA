<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocType;
use App\Models\Department;
class BaseSeeder extends Seeder {
  public function run() {
    // Doc Types default
    $types = [
      ['name'=>'SOP','slug'=>'sop'],
      ['name'=>'IK','slug'=>'ik'],
      ['name'=>'Form','slug'=>'form'],
    ];
    foreach ($types as $t) DocType::firstOrCreate(['slug'=>$t['slug']],$t);

    // Departemen contoh
    $depts = [
      ['name'=>'HRGA Dept.','slug'=>'hrga'],
      ['name'=>'Engineering Dept.','slug'=>'engineering'],
      ['name'=>'Finance & Accounting','slug'=>'finance-accounting'],
    ];
    foreach ($depts as $d) {
      $dep = Department::firstOrCreate(['slug'=>$d['slug']],$d);
      $dep->docTypes()->syncWithoutDetaching(
        DocType::pluck('id')->mapWithKeys(fn($id)=>[$id=>['is_active'=>true,'sort_order'=>0]])->toArray()
      );
    }
  }
}
