@extends('layouts.student')
@section('title', 'Ebooks')
@section('nav_worksheets', 'active')

@push('styles')
<style>
.ebooks-page {
    padding: 8px 12px 120px;
    max-width: 700px;
    width: 100%;
    box-sizing: border-box;
    overflow-x: hidden;
    margin: 0 auto;
}
.eb-page-title {
    font-family: 'Bubblegum Sans', cursive;
    font-size: clamp(22px, 6vw, 32px);
    color: #5E4D3B;
    margin-bottom: 4px;
}
.eb-page-sub {
    font-size: 13px;
    font-weight: 700;
    color: #8D7E6A;
    margin-bottom: 24px;
}

/* ── Three-column grid — fixed equal columns ── */
.eb-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    align-items: start;
    position: relative;
    z-index: 10;
}

/* ── Each card wrapper ── */
.eb-card {
    border-radius: 18px;
    overflow: visible;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    position: relative;
}

/* Blue */
.eb-card.blue {
    background: linear-gradient(175deg, #6BBFFF 0%, #3B9EE8 100%);
    box-shadow: 0 8px 0 #1A6BAA, 0 10px 20px rgba(33,117,176,0.3), inset 0 1px 0 rgba(255,255,255,0.35);
}
.eb-card.blue:not(.is-open):hover { transform: translateY(-4px); box-shadow: 0 12px 0 #1A6BAA, 0 16px 28px rgba(33,117,176,0.3); }
.eb-card.blue.is-open { box-shadow: 0 4px 16px rgba(33,117,176,0.25); }

/* Orange */
.eb-card.orange {
    background: linear-gradient(175deg, #FFAA6A 0%, #E8803B 100%);
    box-shadow: 0 8px 0 #A84C18, 0 10px 20px rgba(176,85,32,0.3), inset 0 1px 0 rgba(255,255,255,0.35);
}
.eb-card.orange:not(.is-open):hover { transform: translateY(-4px); box-shadow: 0 12px 0 #A84C18, 0 16px 28px rgba(176,85,32,0.3); }
.eb-card.orange.is-open { box-shadow: 0 4px 16px rgba(176,85,32,0.25); }

/* Green */
.eb-card.green {
    background: linear-gradient(175deg, #7DDBA8 0%, #4CBF88 100%);
    box-shadow: 0 8px 0 #1E7A50, 0 10px 20px rgba(39,138,91,0.3), inset 0 1px 0 rgba(255,255,255,0.35);
}
.eb-card.green:not(.is-open):hover { transform: translateY(-4px); box-shadow: 0 12px 0 #1E7A50, 0 16px 28px rgba(39,138,91,0.3); }
.eb-card.green.is-open { box-shadow: 0 4px 16px rgba(39,138,91,0.25); }

/* ── Toggle button — fixed height, transparent, inside colored card ── */
.eb-toggle {
    width: 100%;
    height: 52px;
    border: none;
    background: transparent;
    padding: 0 8px;
    font-family: 'Bubblegum Sans', cursive;
    font-size: clamp(12px, 3vw, 15px);
    font-weight: 900;
    color: #fff;
    cursor: pointer;
    display: flex;
    align-items: center;
    /* NO justify-content:center — let flex children fill width so min-width:0 works */
    gap: 4px;
    text-shadow: 0 1px 4px rgba(0,0,0,0.2);
    letter-spacing: 0.2px;
    position: relative;
    z-index: 2;
    line-height: 1;
    overflow: hidden;
}

/* Clear (✕) button inside the toggle when a filter is active */
.eb-clear {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 16px;
    height: 16px;
    background: rgba(255,255,255,0.3);
    border-radius: 50%;
    font-size: 10px;
    font-family: 'Quicksand', sans-serif;
    font-weight: 900;
    color: #fff;
    text-decoration: none;
    flex-shrink: 0;
    line-height: 1;
    transition: background 0.15s;
    margin-left: 2px;
}
.eb-clear:hover { background: rgba(255,255,255,0.55); color: #fff; }

/* Chevron */
.eb-toggle .chev {
    display: inline-block;
    font-size: 10px;
    transition: transform 0.25s;
    flex-shrink: 0;
}
.eb-card.is-open .eb-toggle .chev { transform: rotate(180deg); }

/* Label — takes remaining width, truncates with ellipsis, centred */
.eb-label {
    flex: 1;
    min-width: 0;           /* key: lets flex child shrink below content size */
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    text-align: center;
}

/* ── Dropdown panel — absolutely positioned to overlay ── */
.eb-panel {
    display: none;
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    right: 0;
    border-radius: 16px;
    padding: 0 0 12px;
    flex-direction: column;
    z-index: 200;
    box-shadow: 0 12px 32px rgba(0,0,0,0.18);
}
.eb-panel.open { display: flex; }

.eb-card.blue   .eb-panel { background: linear-gradient(175deg, #6BBFFF 0%, #3B9EE8 100%); }
.eb-card.orange .eb-panel { background: linear-gradient(175deg, #FFAA6A 0%, #E8803B 100%); }
.eb-card.green  .eb-panel { background: linear-gradient(175deg, #7DDBA8 0%, #4CBF88 100%); }

/* ── Search bar inside panel ── */
.eb-search {
    margin: 10px 10px 0;
    display: flex;
    align-items: center;
    background: rgba(255,255,255,0.92);
    border-radius: 999px;
    padding: 5px 10px;
    gap: 6px;
}
.eb-search input {
    border: none;
    background: transparent;
    outline: none;
    font-family: 'Quicksand', sans-serif;
    font-size: 12px;
    font-weight: 700;
    color: #5E4D3B;
    width: 100%;
}
.eb-search input::placeholder { color: #A09080; }
.eb-search .search-icon { font-size: 12px; flex-shrink: 0; }

/* ── Scrollable list wrapper — fixed height ── */
.eb-list-wrap {
    max-height: 150px;      /* fixed height, scrollable */
    overflow-y: auto;
    margin: 6px 10px 0;
    border-radius: 10px;
    background: rgba(255,255,255,0.92);
    scrollbar-width: thin;
    scrollbar-color: rgba(0,0,0,0.15) transparent;
}
.eb-list-wrap::-webkit-scrollbar { width: 4px; }
.eb-list-wrap::-webkit-scrollbar-track { background: transparent; }
.eb-list-wrap::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 4px; }

/* List inside the scrollable wrapper */
.eb-list {
    padding: 4px 0;
    list-style: none;
    margin: 0;
}
.eb-list li a {
    display: block;
    padding: 8px 12px;
    font-size: clamp(11px, 2.8vw, 13px);
    font-weight: 800;
    color: #5E4D3B;
    text-decoration: none;
    border-radius: 8px;
    transition: background 0.15s;
    font-family: 'Quicksand', sans-serif;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.eb-list li a:hover { background: rgba(0,0,0,0.06); }
.eb-list li a.is-active { background: rgba(0,0,0,0.09); font-weight: 900; }
.eb-card.blue .eb-list li a::before { content: '• '; color: #3B9EE8; }
.eb-list li.eb-no-results {
    padding: 8px 12px;
    font-size: 11px;
    font-weight: 700;
    color: #A09080;
    font-family: 'Quicksand', sans-serif;
    text-align: center;
}

/* Heartbeat animation — 2 quick pulses then ~1s rest */
@keyframes heartbeat {
    0%   { transform: scale(1);    }
    9%   { transform: scale(1.20); }  /* first beat peak */
    18%  { transform: scale(1);    }  /* between beats   */
    27%  { transform: scale(1.13); }  /* second beat peak */
    36%  { transform: scale(1);    }  /* back to rest    */
    100% { transform: scale(1);    }  /* hold rest ~1s   */
}

/* Illustration */
.eb-illustration {
    text-align: center;
    padding: 12px 10px 0;
}
.eb-illustration img {
    width: 80%;
    max-width: 110px;
    object-fit: contain;
    filter: drop-shadow(0 6px 14px rgba(0,0,0,0.18));
    animation: heartbeat 1.8s ease-in-out infinite;
    transform-origin: center bottom;
}

/* ── Results section ── */
.eb-results-wrap {
    position: relative;
    z-index: 1;
    margin-top: 28px;
}
.eb-results-title {
    font-family: 'Bubblegum Sans', cursive;
    font-size: clamp(18px, 4.5vw, 24px);
    color: #5E4D3B;
    margin-bottom: 14px;
}
.ebook-card {
    background: rgba(255,255,255,0.82);
    border-radius: 20px;
    padding: 14px 14px;
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 10px;
    box-shadow: 0 6px 0 rgba(0,0,0,0.06), 0 8px 16px rgba(0,0,0,0.05);
    transition: transform 0.15s;
    text-decoration: none;
    color: #5E4D3B;
    width: 100%;
    box-sizing: border-box;
    overflow: hidden;
    min-width: 0;
}
.ebook-card:hover { transform: translateY(-3px); }
.ebook-cover {
    width: 52px; height: 64px;
    border-radius: 8px; object-fit: cover;
    flex-shrink: 0; box-shadow: 0 4px 0 rgba(0,0,0,0.1);
}
.ebook-cover-placeholder {
    width: 52px; height: 64px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 28px; flex-shrink: 0;
}
.ebook-info { flex: 1; min-width: 0; }
.ebook-title {
    font-family: 'Bubblegum Sans', cursive;
    font-size: clamp(14px, 3.5vw, 18px);
    margin-bottom: 4px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.ebook-meta {
    font-size: 11px;
    font-weight: 700;
    color: #8D7E6A;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ebook-tag {
    display: inline-block; background: #FFF3CC;
    border-radius: 999px; padding: 2px 10px;
    font-size: 10px; font-weight: 900;
    color: #A07800; margin-top: 4px;
}
.btn-read {
    background: linear-gradient(135deg, #9DE182, #5CAA44);
    color: #fff; border: none; border-radius: 999px; padding: 8px 16px;
    font-family: 'Quicksand', sans-serif; font-size: 12px; font-weight: 900;
    cursor: pointer; box-shadow: 0 4px 0 #3A7A28;
    transform: translateY(-2px); transition: all 0.15s;
    white-space: nowrap; text-decoration: none; display: inline-block;
}
.btn-read:hover  { transform: translateY(-4px); box-shadow: 0 6px 0 #3A7A28; color: #fff; }
.btn-read:active { transform: translateY(0);    box-shadow: 0 1px 0 #3A7A28; }
.eb-empty {
    text-align: center; padding: 36px 20px;
    color: #8D7E6A; font-size: 15px; font-weight: 700;
}
</style>
@endpush

@section('content')
@php
    $activePub = request('publisher', '');
    $activeCls = request('class', '');      // maps to `standard` column in DB
    $activeSub = request('subject', '');

    // Clear-one-filter URLs (preserve the other two active filters)
    $clearPubUrl = route('student.ebooks', array_filter(['class' => $activeCls, 'subject' => $activeSub]));
    $clearClsUrl = route('student.ebooks', array_filter(['publisher' => $activePub, 'subject' => $activeSub]));
    $clearSubUrl = route('student.ebooks', array_filter(['publisher' => $activePub, 'class' => $activeCls]));
@endphp
<div class="ebooks-page">

    <div class="eb-page-title">📚 Ebooks</div>
    <div class="eb-page-sub">Browse by publication, class or subject</div>

    <!-- ── Three toggle cards ── -->
    <div class="eb-grid">

        {{-- Publications --}}
        <div class="eb-card blue" id="card-pub">
            <button class="eb-toggle" onclick="toggleCard('pub')">
                <span class="eb-label">{{ $activePub ?: 'Publications' }}</span>
                @if($activePub)
                    <a class="eb-clear" href="{{ $clearPubUrl }}" onclick="event.stopPropagation();" title="Remove">✕</a>
                @endif
                <span class="chev">▼</span>
            </button>
            <div class="eb-panel" id="panel-pub">
                <div class="eb-search">
                    <span class="search-icon">🔍</span>
                    <input type="text" placeholder="Search publications..." oninput="searchList(this, 'list-pub')">
                </div>
                <div class="eb-list-wrap">
                    <ul class="eb-list" id="list-pub">
                        @foreach($publications as $pub)
                        <li data-name="{{ strtolower($pub) }}">
                            <a href="{{ route('student.ebooks', array_filter(['publisher' => $pub, 'class' => $activeCls, 'subject' => $activeSub])) }}"
                               data-close="pub"
                               class="{{ $activePub === $pub ? 'is-active' : '' }}">
                                {{ $pub }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="eb-illustration">
                    <img src="{{ asset('uploads/images/icons/Publication.png') }}" alt="Publications" fetchpriority="high" loading="eager" decoding="async">
                </div>
            </div>
        </div>

        {{-- Classes --}}
        <div class="eb-card orange" id="card-cls">
            <button class="eb-toggle" onclick="toggleCard('cls')">
                <span class="eb-label">{{ $activeCls ? 'Class '.$activeCls : 'Classes' }}</span>
                @if($activeCls)
                    <a class="eb-clear" href="{{ $clearClsUrl }}" onclick="event.stopPropagation();" title="Remove">✕</a>
                @endif
                <span class="chev">▼</span>
            </button>
            <div class="eb-panel" id="panel-cls">
                <div class="eb-search">
                    <span class="search-icon">🔍</span>
                    <input type="text" placeholder="Search classes..." oninput="searchList(this, 'list-cls')">
                </div>
                <div class="eb-list-wrap">
                    <ul class="eb-list" id="list-cls">
                        @foreach($standards as $std)
                        <li data-name="{{ strtolower('class '.$std) }}">
                            <a href="{{ route('student.ebooks', array_filter(['publisher' => $activePub, 'class' => $std, 'subject' => $activeSub])) }}"
                               data-close="cls"
                               class="{{ $activeCls == $std ? 'is-active' : '' }}">
                                Class {{ $std }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="eb-illustration">
                    <img src="{{ asset('uploads/images/icons/class.png') }}" alt="Classes" fetchpriority="high" loading="eager" decoding="async">
                </div>
            </div>
        </div>

        {{-- Subjects --}}
        <div class="eb-card green" id="card-sub">
            <button class="eb-toggle" onclick="toggleCard('sub')">
                <span class="eb-label">{{ $activeSub ?: 'Subjects' }}</span>
                @if($activeSub)
                    <a class="eb-clear" href="{{ $clearSubUrl }}" onclick="event.stopPropagation();" title="Remove">✕</a>
                @endif
                <span class="chev">▼</span>
            </button>
            <div class="eb-panel" id="panel-sub">
                <div class="eb-search">
                    <span class="search-icon">🔍</span>
                    <input type="text" placeholder="Search subjects..." oninput="searchList(this, 'list-sub')">
                </div>
                <div class="eb-list-wrap">
                    <ul class="eb-list" id="list-sub">
                        @foreach($subjects as $subject)
                        <li data-name="{{ strtolower($subject) }}">
                            <a href="{{ route('student.ebooks', array_filter(['publisher' => $activePub, 'class' => $activeCls, 'subject' => $subject])) }}"
                               data-close="sub"
                               class="{{ $activeSub === $subject ? 'is-active' : '' }}">
                                {{ $subject }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="eb-illustration">
                    <img src="{{ asset('uploads/images/icons/subject.png') }}" alt="Subjects" fetchpriority="high" loading="eager" decoding="async">
                </div>
            </div>
        </div>

    </div><!-- /eb-grid -->

    <!-- ── Ebook listing ── -->
    <div class="eb-results-wrap">
        <div class="eb-results-title">📖 Available Ebooks</div>

        @forelse($ebooks as $ebook)
        <div class="ebook-card">
            <div class="ebook-cover-placeholder" style="background: linear-gradient(135deg,#A8E8FF,#8BDDFF);">
                📘
            </div>
            <div class="ebook-info">
                <div class="ebook-title">{{ $ebook->name }}</div>
                <div class="ebook-meta">
                    @if($ebook->publication)<span>📖 {{ $ebook->publication }}</span>@endif
                    @if($ebook->subject)<span> · {{ $ebook->subject }}</span>@endif
                </div>
                <div style="display:flex; gap:6px; flex-wrap:wrap; margin-top:4px;">
                    @if($ebook->standard)
                        <span class="ebook-tag">🎓 Class {{ $ebook->standard }}</span>
                    @endif
                    @if($ebook->series)
                        <span class="ebook-tag" style="background:#E8F5FF;color:#1A6BAA;">{{ $ebook->series }}</span>
                    @endif
                </div>
            </div>
            <div style="display: flex; gap: 8px;">
                @if(in_array($ebook->id, $assignedEbookIds))
                    <button type="button" class="btn-read" style="background: #9E9E9E; box-shadow: 0 4px 0 #757575; cursor: default;">Assigned</button>
                @else
                <form action="{{ route('student.ebooks.assign', $ebook->id) }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-read" style="background: linear-gradient(135deg, #FFB347, #FF7B00); box-shadow: 0 4px 0 #CC6200;">Assign</button>
                </form>
                @endif
                <a class="btn-read" href="{{ route('student.ebooks.show', $ebook->id) }}">Open →</a>
            </div>
        </div>
        @empty
        <div class="eb-empty">
            <div style="font-size:48px;margin-bottom:12px;">📚</div>
            @if($activePub || $activeCls || $activeSub)
                No ebooks found for the selected filters. Try a different combination!
            @else
                No ebooks available yet. Check back soon!
            @endif
        </div>
        @endforelse
    </div>

</div>
@endsection

@push('scripts')
<script>
function toggleCard(id) {
    const card  = document.getElementById('card-' + id);
    const panel = document.getElementById('panel-' + id);
    const isOpen = card.classList.contains('is-open');

    // If closing, clear search input
    if (isOpen) {
        const input = panel.querySelector('.eb-search input');
        if (input) { input.value = ''; searchList(input, 'list-' + id); }
    }

    card.classList.toggle('is-open', !isOpen);
    panel.classList.toggle('open', !isOpen);

    // Focus search input when opening
    if (!isOpen) {
        setTimeout(() => panel.querySelector('.eb-search input')?.focus(), 50);
    }
}

// Live search — shows/hides list items matching the query
function searchList(input, listId) {
    const q    = input.value.trim().toLowerCase();
    const list = document.getElementById(listId);
    if (!list) return;

    const items = list.querySelectorAll('li[data-name]');
    let visible = 0;

    items.forEach(function(li) {
        const name = li.dataset.name || '';
        const show = !q || name.includes(q);
        li.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    // No-results message
    let noRes = list.querySelector('.eb-no-results');
    if (visible === 0) {
        if (!noRes) {
            noRes = document.createElement('li');
            noRes.className = 'eb-no-results';
            noRes.textContent = 'No results';
            list.appendChild(noRes);
        }
        noRes.style.display = '';
    } else if (noRes) {
        noRes.style.display = 'none';
    }
}

// Auto-close panel when a filter item is clicked (before navigation)
document.querySelectorAll('.eb-list a[data-close]').forEach(function(link) {
    link.addEventListener('click', function() {
        const id = this.dataset.close;
        document.getElementById('card-' + id)?.classList.remove('is-open');
        document.getElementById('panel-' + id)?.classList.remove('open');
    });
});

// Close panels when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.eb-card')) {
        document.querySelectorAll('.eb-card').forEach(c => c.classList.remove('is-open'));
        document.querySelectorAll('.eb-panel').forEach(p => p.classList.remove('open'));
    }
});
</script>
@endpush

