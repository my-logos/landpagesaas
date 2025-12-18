@auth
@if(!auth()->user()->isAdmin())
<a href="{{ route('user.support.index') }}" class="fixed-help-icon" title="{{ $t('messages.support_help') ?? 'Support & Help' }}">
    <i class="fa-solid fa-question"></i>
</a>
@endif
@endauth