@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <!-- Control Panel Section -->
    <div class="control-panel-section">
        <h2 class="control-panel-title">{{ $t('messages.control_panel') ?? 'Control Panel' }}</h2>
    </div>

    <!-- Header -->
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">{{ $t('messages.manage_faq') ?? 'إدارة الأسئلة الشائعة' }}</h1>
        </div>
        <div class="page-header-right">
            <button type="button" class="btn btn-primary" onclick="document.getElementById('faqForm').style.display='block'">
                <i class="fa-solid fa-plus"></i>
                {{ $t('messages.add_faq') ?? 'إضافة سؤال' }}
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        <i class="fa-solid fa-check-circle"></i>
        {{ session('success') }}
    </div>
    @endif

    <!-- Add/Edit FAQ Form -->
    <div id="faqForm" class="faq-form-card" style="display: {{ old('_token') ? 'block' : 'none' }};">
        <h2>{{ $t('messages.add_faq') ?? 'Add FAQ' }}</h2>
        <form method="POST" action="{{ route('admin.support.faq.store') }}" class="faq-form">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label for="question_en">{{ $t('messages.question') ?? 'Question' }} (EN) <span class="required">*</span></label>
                    <input type="text" id="question_en" name="question_en" class="form-control" value="{{ old('question_en') }}" required maxlength="500">
                </div>
                <div class="form-group">
                    <label for="question_ar">{{ $t('messages.question') ?? 'Question' }} (AR) <span class="required">*</span></label>
                    <input type="text" id="question_ar" name="question_ar" class="form-control" value="{{ old('question_ar') }}" required maxlength="500">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="answer_en">{{ $t('messages.answer') ?? 'Answer' }} (EN) <span class="required">*</span></label>
                    <textarea id="answer_en" name="answer_en" class="form-control" rows="6" required maxlength="5000">{{ old('answer_en') }}</textarea>
                </div>
                <div class="form-group">
                    <label for="answer_ar">{{ $t('messages.answer') ?? 'Answer' }} (AR) <span class="required">*</span></label>
                    <textarea id="answer_ar" name="answer_ar" class="form-control" rows="6" required maxlength="5000">{{ old('answer_ar') }}</textarea>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="keywords_en">{{ $t('messages.keywords') ?? 'Keywords' }} (EN) <small>{{ $t('messages.comma_separated') ?? 'Comma separated' }}</small></label>
                    <input type="text" id="keywords_en" name="keywords_en" class="form-control" value="{{ old('keywords_en') }}" placeholder="keyword1, keyword2, keyword3">
                </div>
                <div class="form-group">
                    <label for="keywords_ar">{{ $t('messages.keywords') ?? 'Keywords' }} (AR) <small>{{ $t('messages.comma_separated') ?? 'Comma separated' }}</small></label>
                    <input type="text" id="keywords_ar" name="keywords_ar" class="form-control" value="{{ old('keywords_ar') }}" placeholder="كلمة1، كلمة2، كلمة3">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="sort_order">{{ $t('messages.sort_order') ?? 'Sort Order' }}</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        {{ $t('messages.is_active') ?? 'Is Active' }}
                    </label>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">{{ $t('messages.save') ?? 'Save' }}</button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('faqForm').style.display='none'">{{ $t('messages.cancel') ?? 'Cancel' }}</button>
            </div>
        </form>
    </div>

    <!-- FAQ List -->
    <div class="faq-admin-list">
        @forelse($faqs as $faq)
        <div class="faq-admin-item">
            <div class="faq-admin-header">
                <div class="faq-admin-info">
                    <h3>{{ $faq->question_en }}</h3>
                    <p class="faq-admin-question-ar">{{ $faq->question_ar }}</p>
                    <div class="faq-admin-meta">
                        <span class="badge {{ $faq->is_active ? 'badge-success' : 'badge-secondary' }}">
                            {{ $faq->is_active ? ($t('messages.active') ?? 'Active') : ($t('messages.inactive') ?? 'Inactive') }}
                        </span>
                        <span class="faq-sort-order">{{ $t('messages.sort_order') ?? 'Sort Order' }}: {{ $faq->sort_order }}</span>
                    </div>
                </div>
                <div class="faq-admin-actions">
                    <button type="button" class="btn btn-sm btn-info" onclick="editFaq({{ $faq->id }})">{{ $t('messages.edit') ?? 'Edit' }}</button>
                    <form method="POST" action="{{ route('admin.support.faq.delete', $faq) }}" style="display:inline;" onsubmit="return confirm('{{ $t('messages.confirm_delete') ?? 'Are you sure?' }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">{{ $t('messages.delete') ?? 'Delete' }}</button>
                    </form>
                </div>
            </div>
            <div id="editForm{{ $faq->id }}" class="faq-edit-form" style="display: none;">
                <form method="POST" action="{{ route('admin.support.faq.update', $faq) }}" class="faq-form">
                    @csrf
                    @method('PUT')
                    <div class="form-row">
                        <div class="form-group">
                            <label>{{ $t('messages.question') ?? 'Question' }} (EN) <span class="required">*</span></label>
                            <input type="text" name="question_en" class="form-control" value="{{ $faq->question_en }}" required maxlength="500">
                        </div>
                        <div class="form-group">
                            <label>{{ $t('messages.question') ?? 'Question' }} (AR) <span class="required">*</span></label>
                            <input type="text" name="question_ar" class="form-control" value="{{ $faq->question_ar }}" required maxlength="500">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>{{ $t('messages.answer') ?? 'Answer' }} (EN) <span class="required">*</span></label>
                            <textarea name="answer_en" class="form-control" rows="6" required maxlength="5000">{{ $faq->answer_en }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>{{ $t('messages.answer') ?? 'Answer' }} (AR) <span class="required">*</span></label>
                            <textarea name="answer_ar" class="form-control" rows="6" required maxlength="5000">{{ $faq->answer_ar }}</textarea>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>{{ $t('messages.keywords') ?? 'Keywords' }} (EN)</label>
                            <input type="text" name="keywords_en" class="form-control" value="{{ implode(', ', $faq->keywords_en ?? []) }}" placeholder="keyword1, keyword2">
                        </div>
                        <div class="form-group">
                            <label>{{ $t('messages.keywords') ?? 'Keywords' }} (AR)</label>
                            <input type="text" name="keywords_ar" class="form-control" value="{{ implode('، ', $faq->keywords_ar ?? []) }}" placeholder="كلمة1، كلمة2">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>{{ $t('messages.sort_order') ?? 'Sort Order' }}</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ $faq->sort_order }}" min="0">
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="is_active" value="1" {{ $faq->is_active ? 'checked' : '' }}>
                                {{ $t('messages.is_active') ?? 'Is Active' }}
                            </label>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">{{ $t('messages.update') ?? 'Update' }}</button>
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('editForm{{ $faq->id }}').style.display='none'">{{ $t('messages.cancel') ?? 'Cancel' }}</button>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <i class="fa-solid fa-question-circle"></i>
            <h3>{{ $t('messages.no_faqs') ?? 'No FAQs found' }}</h3>
        </div>
        @endforelse
    </div>
</div>

<script>
    function editFaq(id) {
        const editForm = document.getElementById('editForm' + id);
        const allEditForms = document.querySelectorAll('.faq-edit-form');
        allEditForms.forEach(form => {
            if (form.id !== 'editForm' + id) {
                form.style.display = 'none';
            }
        });
        editForm.style.display = editForm.style.display === 'none' ? 'block' : 'none';
    }
</script>

<style>
    .faq-form-card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .faq-admin-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .faq-admin-item {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 20px;
    }

    .faq-admin-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        flex-wrap: wrap;
    }

    .faq-admin-info {
        flex: 1;
    }

    .faq-admin-info h3 {
        margin: 0 0 8px 0;
        color: #1f2b6b;
        font-size: 16px;
    }

    .faq-admin-question-ar {
        color: #6b7280;
        font-size: 14px;
        margin: 0 0 12px 0;
    }

    .faq-admin-meta {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .faq-sort-order {
        color: #9ca3af;
        font-size: 12px;
    }

    .faq-admin-actions {
        display: flex;
        gap: 8px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection