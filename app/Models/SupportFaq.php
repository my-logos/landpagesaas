<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportFaq extends Model
{
    use HasFactory;

    protected $table = 'support_faq';

    protected $fillable = [
        'question_en',
        'question_ar',
        'answer_en',
        'answer_ar',
        'keywords_en',
        'keywords_ar',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'keywords_en' => 'array',
        'keywords_ar' => 'array',
        'is_active' => 'boolean',
    ];

    public function getQuestionAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->question_ar : $this->question_en;
    }

    public function getAnswerAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->answer_ar : $this->answer_en;
    }

    public function getKeywordsAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->keywords_ar ?? []) : ($this->keywords_en ?? []);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}

