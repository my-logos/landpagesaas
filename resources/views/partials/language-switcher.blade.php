@if(isset($availableLanguages) && $availableLanguages->isNotEmpty())
@if(isset($hasMultipleLanguages) && $hasMultipleLanguages)
{{-- Dropdown for more than 2 languages --}}
<div class="language-dropdown">
    <button class="language-dropdown-toggle" type="button">
        <i class="fa-solid fa-globe"></i>
        <span>{{ isset($currentLanguage) ? ($currentLanguage->native_name ?? $currentLanguage->name ?? 'Language') : 'Language' }}</span>
        <i class="fa-solid fa-chevron-down"></i>
    </button>
    <div class="language-dropdown-menu">
        @foreach($availableLanguages as $lang)
        <a href="{{ route('set.language', $lang->code) }}" class="language-dropdown-item {{ $locale === $lang->code ? 'active' : '' }}">
            <span>{{ $lang->native_name ?? $lang->name }}</span>
            @if($locale === $lang->code)
            <i class="fa-solid fa-check"></i>
            @endif
        </a>
        @endforeach
    </div>
</div>
@else
{{-- Simple toggle for 2 languages --}}
@if(isset($nextLang) && $nextLang)
<a href="{{ route('set.language', $nextLang->code) }}" class="language-toggle">
    <i class="fa-solid fa-globe"></i>
    <span>{{ $nextLang->native_name ?? $nextLang->name }}</span>
</a>
@endif
@endif
@endif