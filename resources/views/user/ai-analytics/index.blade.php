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
            <h1 class="page-title">{{ $t('messages.control_panel') ?? 'لوحة التحكم' }}</h1>
        </div>
        <div class="page-header-right">
            <div class="date-range-selector">
                <form method="GET" action="{{ route('user.ai-analytics.index') }}" class="date-filter-form">
                    <div class="date-input-group">
                        <label>{{ $t('messages.time_period') ?? 'الفترة الزمنية:' }}:</label>
                        <select name="time_period" class="date-input">
                            <option value="7" {{ $timePeriod == '7' ? 'selected' : '' }}>{{ $t('messages.last_7_days') ?? 'آخر 7 أيام' }}</option>
                            <option value="30" {{ $timePeriod == '30' ? 'selected' : '' }}>{{ $t('messages.last_30_days') ?? 'آخر 30 يوم' }}</option>
                            <option value="90" {{ $timePeriod == '90' ? 'selected' : '' }}>{{ $t('messages.last_90_days') ?? 'آخر 90 يوم' }}</option>
                            <option value="365" {{ $timePeriod == '365' ? 'selected' : '' }}>{{ $t('messages.last_year') ?? 'آخر سنة' }}</option>
                        </select>
                    </div>
                    <div class="date-input-group">
                        <label>{{ $t('messages.operation_type') ?? 'نوع العملية:' }}:</label>
                        <select name="operation_type" class="date-input">
                            <option value="all" {{ $operationType == 'all' ? 'selected' : '' }}>{{ $t('messages.all_operations') ?? 'جميع العمليات' }}</option>
                            <option value="page_generation" {{ $operationType == 'page_generation' ? 'selected' : '' }}>{{ $t('messages.page_generation') ?? 'إنشاء الصفحات' }}</option>
                            <option value="content_generation" {{ $operationType == 'content_generation' ? 'selected' : '' }}>{{ $t('messages.content_generation') ?? 'إنشاء المحتوى' }}</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-apply-date-filter">
                        {{ $t('messages.apply') ?? 'تطبيق' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-icon-green">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ $t('messages.failed_operations') ?? 'العمليات الفاشلة' }}</div>
                <div class="stat-value">{{ $failedOperations }}</div>
                <div class="stat-subvalue">{{ $t('messages.average_attempts') ?? 'متوسط المحاولات' }}: {{ $averageAttempts }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-purple">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ $t('messages.average_time') ?? 'متوسط الوقت' }}</div>
                <div class="stat-value">{{ number_format($averageTime, 2) }} {{ $t('messages.seconds_short') ?? 'ث' }}</div>
                <div class="stat-subvalue">{{ $t('messages.median') ?? 'الوسيط' }}: {{ number_format($medianTime, 2) }} {{ $t('messages.seconds_short') ?? 'ث' }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-green">
                <i class="fa-solid fa-check"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ $t('messages.success_rate') ?? 'معدل النجاح' }}</div>
                <div class="stat-value">{{ number_format($successRate, 2) }}%</div>
                <div class="stat-subvalue">{{ $successfulOperations }} {{ $t('messages.successful_operation') ?? 'عملية ناجحة' }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-blue">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ $t('messages.total_operations') ?? 'إجمالي العمليات' }}</div>
                <div class="stat-value">{{ $totalOperations }}</div>
                <div class="stat-subvalue">{{ $t('messages.in_last_days', ['days' => $timePeriod]) ?? 'في آخر ' . $timePeriod . ' يوم' }}</div>
            </div>
        </div>
    </div>

    <!-- Performance Trends Chart -->
    <div class="charts-grid single-column">
        <div class="chart-card">
            <h3 class="chart-title">{{ $t('messages.performance_trends') ?? 'اتجاهات الأداء' }}</h3>
            <div class="chart-placeholder">
                <canvas id="performanceTrendsChart"
                    data-chart-data="{{ json_encode([
                                'dates' => $dates ?? [],
                                'averageTimeData' => $averageTimeData ?? [],
                                'successRateData' => $successRateData ?? []
                            ]) }}"
                    data-average-time-label="{{ $t('messages.average_time_seconds') ?? 'Average Time (seconds)' }}"
                    data-success-rate-label="{{ $t('messages.success_rate_percent') ?? 'Success Rate (%)' }}"></canvas>
            </div>
        </div>
    </div>
</div>

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@push('scripts')
<script src="{{ asset('js/user-ai-analytics.js') }}"></script>
@endpush
@endsection