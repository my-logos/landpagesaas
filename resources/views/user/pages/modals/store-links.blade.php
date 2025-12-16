<div class="modal-overlay" id="storeLinksModal">
    <div class="modal-container modal-large">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fa-solid fa-link"></i>
                {{ $t('messages.your_store_links') ?? 'Your Store Links' }}
            </h3>
            <button type="button" class="modal-close" data-close-modal>
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            @if(isset($publishedPages) && $publishedPages->count() > 0)
            <div class="store-links-list">
                @foreach($publishedPages as $page)
                <div class="store-link-item">
                    <div class="store-link-info">
                        <h4 class="store-link-title">{{ $page->title }}</h4>
                        <div class="store-link-url-container">
                            <input type="text" 
                                   class="store-link-url" 
                                   value="{{ route('page.show', $page->id) }}" 
                                   readonly 
                                   id="page-url-{{ $page->id }}">
                            <button type="button" 
                                    class="btn-copy-link" 
                                    data-copy-url="#page-url-{{ $page->id }}"
                                    data-copy-success="{{ $t('messages.link_copied') ?? 'Link copied!' }}">
                                <i class="fa-solid fa-copy"></i>
                                <span>{{ $t('messages.copy_link') ?? 'Copy Link' }}</span>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="store-links-empty">
                <i class="fa-solid fa-link-slash"></i>
                <p>{{ $t('messages.no_published_pages') ?? 'No published pages yet. Publish a page to get its link.' }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
