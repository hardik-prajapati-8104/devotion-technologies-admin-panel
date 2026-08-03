<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CareerApplication extends Model
{
    use SoftDeletes;

    public const STATUSES = [
        'new'         => 'New',
        'reviewing'   => 'Reviewing',
        'shortlisted' => 'Shortlisted',
        'interview'   => 'Interview',
        'selected'    => 'Selected',
        'rejected'    => 'Rejected',
    ];

    protected $fillable = [
        'career_id', 'applicant_name', 'email', 'phone', 'resume_path',
        'cover_letter', 'status', 'ip_address',
    ];

    public function career()
    {
        return $this->belongsTo(Career::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }
}
