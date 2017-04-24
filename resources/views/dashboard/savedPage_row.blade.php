            <div class="row page-block" data-id="{{ $page->id }}">
                <div class="small-10 columns">
                    <div class="page-thumbnail float-left">
                        <a href="{{ route('pages.preview', $page->slug) }}"><img src="{{ url('images/small180/'.$page->mainImageFullPath) }}" alt=""></a>
                    </div>
                    <div class="page-content float-left">
                        <h4><a href="{{ route('pages.preview', $page->slug) }}">{{ $page->name }}</a></h4>
                        <div class="page-info">
                            <div><span class="fa fa-star"></span>{{ $page->getPageType[0]->name }}</div>
                            <div><span class="fa fa-map-marker"></span>{{ $page->city->name.', '.$page->country->name }}</div>
                        </div>
                    </div>
                </div>
                <div class="small-2 columns actions">
                    <a href="{{ route('pages.preview', $page->slug) }}" class="button hollow" target="_blank">Edit / Publish</a>
                    <a data-open="delete-page-{{ $page->id }}" class="button hollow">Delete</a>
                </div>
                <div class="reveal" id="delete-page-{{ $page->id }}" data-reveal>
                    <h4>Delete</h4>
                    <p>Are you sure you want to delete this page?</p>
                    <p class="float-right delete-page-buttons">
                        <button type="button" class="button secondary" data-close>Cancel</button>
                        <button type="button" class="button alert delete-page-confirm" data-slug="{{ $page->slug }}" data-id="{{ $page->id }}">Delete</button>
                    </p>
                    <button class="close-button" data-close aria-label="Close modal" type="button">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>