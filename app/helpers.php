<?php
// app/helpers.php

use App\Models\AuditLog;
use Illuminate\Support\Facades\Notification;

/**
 * Helper sederhana untuk Audit Log.
 * Contoh pakai:
 *   audit()->by($user)->on($model)->action('form.submit')->meta(['ip'=>request()->ip()])->log();
 */
if (!function_exists('audit')) {
    function audit() {
        return new class {
            private $user;
            private $model;
            private $action;
            private $meta;

            public function by($user){ $this->user = $user; return $this; }
            public function on($model){ $this->model = $model; return $this; }
            public function action(string $a){ $this->action = $a; return $this; }
            public function meta(array $m){ $this->meta = $m; return $this; }

            public function log(): void {
                try {
                    AuditLog::create([
                        'user_id'       => $this->user?->id,
                        'action'        => $this->action ?? 'unknown',
                        'auditable_type'=> $this->model ? get_class($this->model) : null,
                        'auditable_id'  => $this->model->id ?? null,
                        'meta'          => $this->meta ?? [],
                    ]);
                } catch (\Throwable $e) {
                    logger()->warning('audit() failed', ['error' => $e->getMessage()]);
                }
            }
        };
    }
}

/**
 * Kirim notifikasi ke admin departemen saat form di-submit.
 * Aman bila class Notification belum ada / mail belum dikonfigurasi.
 */
if (!function_exists('notifyApprovers')) {
    function notifyApprovers(\App\Models\FormDefinition $form, \App\Models\FormEntry $entry): void
    {
        try {
            $admins = \App\Models\User::whereHas('adminDepartments', function ($q) use ($form) {
                $q->where('departments.id', $form->department_id);
            })->get();

            if ($admins->isNotEmpty() && class_exists(\App\Notifications\FormSubmitted::class)) {
                Notification::send($admins, new \App\Notifications\FormSubmitted($entry));
            }
        } catch (\Throwable $e) {
            logger()->warning('notifyApprovers failed', ['error' => $e->getMessage()]);
        }
    }
}

/**
 * Kirim notifikasi ke pengirim ketika entry di-approve/reject.
 */
if (!function_exists('notifySubmitter')) {
    function notifySubmitter(\App\Models\FormEntry $entry, string $action): void
    {
        try {
            if (class_exists(\App\Notifications\FormReviewed::class)) {
                $entry->user?->notify(new \App\Notifications\FormReviewed($entry, $action));
            }
        } catch (\Throwable $e) {
            logger()->warning('notifySubmitter failed', ['error' => $e->getMessage()]);
        }
    }
}
