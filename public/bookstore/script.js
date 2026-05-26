// ===== EBOOK STORE - MAIN SCRIPT =====
let totalBooks = 0;
let shownBooks = 0;
// ===== DATA =====
// const booksData = [
  //{ id: 1, title: "The Art of Clean Code", author: "Robert C. Martin", category: "Programming", price: 299, originalPrice: 599, rating: 4.8, reviews: 2341, cover: "💻", badge: "bestseller", tags: ["coding", "javascript", "clean"], description: "A handbook of agile software craftsmanship. This book is packed with practical examples and covers writing clean, readable, and maintainable code.", pages: 431, publisher: "Prentice Hall", //language: "English", format: ["PDF", "EPUB", "MOBI"], isbn: "978-0-13-468599-1", date: "2020-01-01" }
//];
function setButtonLoading(button, text = "Processing") {
    if (!button) return;

    button.classList.add('btn-loading');
    button.disabled = true;

    const originalText = button.innerHTML;
    button.dataset.originalText = originalText;

    button.innerHTML = text;
}

function resetButton(button) {
    if (!button) return;

    button.classList.remove('btn-loading');
    button.disabled = false;

    if (button.dataset.originalText) {
        button.innerHTML = button.dataset.originalText;
    }
}
let page = 1;
let loading = false;
let hasMore = true;
// ===== STATE =====
let wishlist = JSON.parse(localStorage.getItem('ebookWishlist')) || [];
let currentPage = 1;
const booksPerPage = 8;
let currentView = 'grid';
let searchQuery = '';

// ===== INIT =====
document.addEventListener('DOMContentLoaded', () => {
  initParticles();
  initNavbar();
  initScrollTop();
  startCountdown(); 
  if (document.getElementById('books-container')) {
    loadInitialBooks();
  initInfiniteScroll();

  loadCart();
  initSearchOverlay();
  animateStats();
  }
  if (document.getElementById('cart-items-container')) {
    loadCart();
  }
  if (document.getElementById('book-detail-container')) {
    renderBookDetail();
  }
  if (document.getElementById('auth-tabs')) {
    initAuth();
  }
});
function loadInitialBooks() {
    page = 1;
    hasMore = true;

    fetchFilteredBooks(true); // ✅ USE SAME FUNCTION
}
function initInfiniteScroll() {
    const booksSection = document.getElementById("books-section");

    window.addEventListener("scroll", () => {

    if (loading || !hasMore) return;

    if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 200) {
        loadMoreBooks();
    }

});
}
function loadMoreBooks() {
    if (loading || !hasMore) return;

    loading = true;

    const loader = document.getElementById("scroll-loader");
    loader.style.display = "block";

    page++;

    fetchFilteredBooks(false); // ✅ SAME FILTER FUNCTION

    setTimeout(() => {
        loader.style.display = "none";
        loading = false;
    }, 500);
}
function renderBooksFromAPI(books) {
    const container = document.getElementById("books-container");

    books.forEach(book => {
        shownBooks++;
        const div = document.createElement("div");
        div.className = "book-card";

        div.onclick = () => {
            window.location.href = BASE_URL + `/ebook-detail/${book.id}`;
        };

        div.innerHTML = `
            <div class="book-cover">
                <img src="${book.page ? book.page.url + '/1.jpg' : '/no-image.png'}">
            </div>

            <div class="book-info">
                <div class="book-title">${book.name}</div>
                <div class="book-price">₹${book.price}</div>

                <button class="add-cart-btn" data-id="${book.id}" 
                    onclick="addToCart(${book.id}, event)">
                    Add to Cart
                </button>
            </div>
        `;

        container.appendChild(div);
    });
    updateResultsCount();
}
const signupForm = document.getElementById('signup-form');
if(signupForm){
    signupForm.addEventListener('submit', function () {
        const fname = document.getElementById('signup-fname').value;
        const lname = document.getElementById('signup-lname').value;
        document.getElementById('full-name').value = (fname + ' ' + lname).trim();
    });
}

// ===== PARTICLES =====
function initParticles() {
  const container = document.querySelector('.particles-bg');
  if (!container) return;
  for (let i = 0; i < 18; i++) {
    const p = document.createElement('div');
    p.classList.add('particle');
    p.style.cssText = `
      left: ${Math.random() * 100}%;
      width: ${Math.random() * 4 + 2}px;
      height: ${Math.random() * 4 + 2}px;
      animation-duration: ${Math.random() * 15 + 10}s;
      animation-delay: ${Math.random() * 10}s;
      opacity: ${Math.random() * 0.4 + 0.1};
    `;
    container.appendChild(p);
  }
}

// ===== NAVBAR =====
function initNavbar() {
  const navbar = document.querySelector('.navbar-custom');
  if (!navbar) return;
  window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 50);
  });
}

// ===== SCROLL TOP =====
function initScrollTop() {
  const btn = document.querySelector('.scroll-top');
  if (!btn) return;
  window.addEventListener('scroll', () => {
    btn.classList.toggle('visible', window.scrollY > 400);
  });
}
function scrollToTop() {
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ===== CART =====
function saveWishlist() { localStorage.setItem('ebookWishlist', JSON.stringify(wishlist)); }

let isAdding = false;

function addToCart(bookId, event) {
    if(event) event.stopPropagation();
    
    if(isAdding) return; 
    isAdding = true;
    
    fetch(BASE_URL + "/cart/add", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ id: bookId })
    })
    .then(res => res.json())
    .then(data => {
        updateCartUI(data);
        updateAllButtons(data.cart);
        showToast("Added to cart");
    })
    .finally(() => {
        isAdding = false; // ✅ unlock
    });
}

function updateCart(id, type) {

    fetch(BASE_URL + "/cart/update", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ id, type })
    })
    .then(res => res.json())
    .then(data => {
        updateCartUI(data);
        renderCartPage(data.cart);
        updateAllButtons(data.cart);
    });
}

function updateCartUI(data){

    const badges = document.querySelectorAll(".cart-badge");

    badges.forEach(badge => {
        if(data.cartCount > 0){
            badge.style.display = "block";
            badge.innerText = data.cartCount;
        } else {
            badge.style.display = "none";
        }
    });

}

function loadCart() {
    fetch(BASE_URL + "/cart/data")
    .then(res => res.json())
    .then(data => {

        console.log("CART DATA ", data); // 

        updateCartUI(data);
        renderCartPage(data.cart);
        updateAllButtons(data.cart);
    });
}

function toggleWishlist(bookId, e) {
  if (e) e.stopPropagation();
  const idx = wishlist.indexOf(bookId);
  const book = booksData.find(b => b.id === bookId);
  if (idx > -1) {
    wishlist.splice(idx, 1);
    showToast(`Removed from wishlist.`, 'error');
  } else {
    wishlist.push(bookId);
    showToast(`"${book.title}" added to wishlist!`, 'success');
  }
  saveWishlist();
  document.querySelectorAll(`[data-wishlist="${bookId}"]`).forEach(btn => {
    btn.classList.toggle('active', wishlist.includes(bookId));
    btn.innerHTML = wishlist.includes(bookId) ? '<i class="bi bi-heart-fill"></i>' : '<i class="bi bi-heart"></i>';
  });
}

function updateAllButtons(cart){

    if(!cart || typeof cart !== "object") return;

    document.querySelectorAll('.add-cart-btn, .cart-btn').forEach(btn => {

        const bookId = btn.dataset.id;

        if(!bookId) return;

        const item = cart[bookId] || cart[String(bookId)];

        if(item){

            const qty = item.quantity;

            btn.innerHTML = `
                <div class="d-flex align-items-center justify-content-center gap-2">

                    <span onclick="event.stopPropagation(); updateCart(${bookId}, 'decrease')" 
                          class="btn btn-sm btn-light">-</span>

                    <span>${qty}</span>

                    <span onclick="event.stopPropagation(); updateCart(${bookId}, 'increase')" 
                          class="btn btn-sm btn-light">+</span>

                </div>
            `;

        } else {

            btn.innerHTML = `<i class="bi bi-cart-plus"></i> Add to Cart`;

        }

    });

}

function goToDetail(bookId) {
  window.location.href = `ebook-detail/${bookId}`;
}

// ===== FILTERS =====
function initFilters() {
  const categoryInputs = document.querySelectorAll('.filter-category');
  const ratingInputs = document.querySelectorAll('.filter-rating');
  const priceInputs = document.querySelectorAll('.filter-price');
  const formatInputs = document.querySelectorAll('.filter-format');
  const applyBtn = document.getElementById('apply-filters');
  const resetBtn = document.getElementById('reset-filters');

  
  if (resetBtn) {
    resetBtn.addEventListener('click', () => {
      document.querySelectorAll('.filter-category, .filter-rating, .filter-format').forEach(i => i.checked = false);
      document.getElementById('min-price') && (document.getElementById('min-price').value = '');
      document.getElementById('max-price') && (document.getElementById('max-price').value = '');
      searchQuery = '';
      const sInput = document.getElementById('search-overlay-input');
      if (sInput) sInput.value = '';
      currentPage = 1;
      updateResultsCount();
      showToast('Filters cleared!', 'success');
    });
  }
}


function updateResultsCount() {
    document.getElementById("showing-count").innerText = shownBooks;
    document.getElementById("total-count").innerText = totalBooks;
}

// ===== SEARCH OVERLAY =====
function initSearchOverlay() {
  const overlay = document.getElementById('search-overlay');
  const input = document.getElementById('search-overlay-input');
  const results = document.getElementById('search-results');
  if (!overlay || !input) return;
  input.addEventListener('input', () => {
    searchQuery = input.value.trim();
    if (searchQuery.length < 2) { results.innerHTML = ''; return; }
    const matches = booksData.filter(b => b.title.toLowerCase().includes(searchQuery.toLowerCase()) || b.author.toLowerCase().includes(searchQuery.toLowerCase())).slice(0, 6);
    results.innerHTML = matches.length ? matches.map(b => `
      <div class="search-result-item" onclick="goToDetail(${b.id}); closeSearch();">
        <div class="search-result-icon">${b.cover}</div>
        <div class="search-result-info">
          <div class="title">${b.title}</div>
          <div class="author">${b.author} · ${b.category}</div>
        </div>
        <div class="search-result-price">${b.price === 0 ? 'FREE' : '₹' + b.price}</div>
      </div>`).join('') : `<div style="padding:1.5rem;color:var(--text-muted);text-align:center">No results for "${searchQuery}"</div>`;
  });
  input.addEventListener('keydown', e => {
    if (e.key === 'Enter') { closeSearch(); document.getElementById('books-section')?.scrollIntoView({ behavior: 'smooth' }); }
    if (e.key === 'Escape') closeSearch();
  });
}

function openSearch() {
  const overlay = document.getElementById('search-overlay');
  if (overlay) { overlay.classList.add('active'); document.getElementById('search-overlay-input')?.focus(); }
}

function closeSearch() {
  const overlay = document.getElementById('search-overlay');
  if (overlay) { overlay.classList.remove('active'); document.getElementById('search-results').innerHTML = ''; }
}


// ===== TOAST =====
function showToast(message, type = 'success') {
  const container = document.querySelector('.toast-container-custom') || createToastContainer();
  const toast = document.createElement('div');
  toast.className = `toast-custom ${type === 'error' ? 'error' : ''}`;
  toast.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'x-circle-fill'}"></i> ${message}`;
  container.appendChild(toast);
  setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateX(100%)'; toast.style.transition = 'all 0.3s ease'; setTimeout(() => toast.remove(), 300); }, 3000);
}

function createToastContainer() {
  const c = document.createElement('div');
  c.className = 'toast-container-custom';
  document.body.appendChild(c);
  return c;
}

// ===== ANIMATE STATS =====
function animateStats() {
  const stats = document.querySelectorAll('.hero-stat-num');
  stats.forEach(el => {
    const target = parseInt(el.dataset.target || el.textContent.replace(/\D/g, ''));
    const suffix = el.dataset.suffix || '';
    let start = 0;
    const duration = 1800;
    const startTime = performance.now();
    const update = (now) => {
      const elapsed = now - startTime;
      const progress = Math.min(elapsed / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.round(eased * target).toLocaleString() + suffix;
      if (progress < 1) requestAnimationFrame(update);
    };
    requestAnimationFrame(update);
  });
}

// ===== COUNTDOWN =====
function startCountdown() {
  const end = new Date();
  end.setHours(23, 59, 59);
  function update() {
    const now = new Date();
    let diff = Math.floor((end - now) / 1000);
    if (diff < 0) diff = 0;
    const h = Math.floor(diff / 3600);
    const m = Math.floor((diff % 3600) / 60);
    const s = diff % 60;
    const hEl = document.getElementById('cd-hours');
    const mEl = document.getElementById('cd-minutes');
    const sEl = document.getElementById('cd-seconds');
    if (hEl) hEl.textContent = String(h).padStart(2, '0');
    if (mEl) mEl.textContent = String(m).padStart(2, '0');
    if (sEl) sEl.textContent = String(s).padStart(2, '0');
  }
  setInterval(update, 1000);
  update();
}

// ===== RENDER CART =====
function renderCartPage(cart){

    const container = document.getElementById("cart-items-container");
    if(!container) return;

    container.innerHTML = "";

    let subtotal = 0;
    let totalQty = 0;

    Object.keys(cart).forEach(id => {

        let item = cart[id];

        subtotal += item.price * item.quantity;
        totalQty += item.quantity;

        container.innerHTML += `
<div class="cart-item d-flex align-items-center justify-content-between mb-3 p-3" 
     style="background:#ffffff;border-radius:12px">

    <div class="d-flex align-items-center gap-3">

        <img src="${item.image ?? ''}" 
             style="width:60px;height:80px;object-fit:cover;border-radius:6px">

        <div>
            <div style="font-weight:600">${item.name}</div>
            <small style="color:#aaa">₹${item.price}</small>
        </div>

    </div>

    <div class="d-flex align-items-center gap-2">

        <button onclick="updateCart(${id}, 'decrease')" 
                class="btn btn-sm btn-light">-</button>

        <span>${item.quantity}</span>

        <button onclick="updateCart(${id}, 'increase')" 
                class="btn btn-sm btn-light">+</button>

    </div>

</div>
`;
    });

    // ✅ UPDATE SUMMARY
    const tax = subtotal * 0.18;
    const total = subtotal + tax;

    document.getElementById("summary-qty").innerText = totalQty + " items";
    document.getElementById("summary-subtotal").innerText = "₹" + subtotal;
    document.getElementById("summary-tax").innerText = "₹" + tax.toFixed(2);
    document.getElementById("summary-total").innerText = "₹" + total.toFixed(2);
}

function applyCoupon() {
  const input = document.getElementById('coupon-input');
  const code = input?.value.trim().toUpperCase();
  const codes = { 'BOOK50': 50, 'READ20': 20, 'EBOOK10': 10 };
  if (codes[code]) {
    showToast(`Coupon "${code}" applied! ${codes[code]}% off!`, 'success');
  } else {
    showToast('Invalid coupon code.', 'error');
  }
}

function checkout() {
    fetch(BASE_URL + "/cart/data")
    .then(res => res.json())
    .then(data => {
        if(Object.keys(data.cart).length === 0){
            showToast('Cart is empty!', 'error');
            return;
        }

        showToast('Redirecting to checkout...', 'success');
        setTimeout(() => {
            window.location.href = BASE_URL + "/checkout";
        }, 800);
    });
}

// ===== BOOK DETAIL =====
function renderBookDetail() {
  const container = document.getElementById('book-detail-container');
  if (!container) return;
  const id = window.location.pathname.split('/').pop();
const book = booksData.find(b => b.id == id);
  
  const isFree = book.price === 0;
  const discount = book.originalPrice > 0 ? Math.round((1 - book.price / book.originalPrice) * 100) : 0;
  const isWishlisted = wishlist.includes(book.id);
  const reviews = [
    { name: 'Priya S.', rating: 5, date: 'Jan 2024', text: 'Absolutely brilliant! One of the best books I have read this year. Highly recommend to everyone.' },
    { name: 'Rahul M.', rating: 4, date: 'Dec 2023', text: 'Great content and well written. A few chapters felt a bit long but overall very informative.' },
    { name: 'Anita K.', rating: 5, date: 'Nov 2023', text: 'Completely changed my perspective. Will definitely buy more from this author!' }
  ];
  document.getElementById('breadcrumb-title').textContent = book.title;
  container.innerHTML = `
    <div class="row g-4">
      <div class="col-lg-3 col-md-4">
        <div class="book-cover-detail">
          <span class="book-cover-detail-img">${book.cover}</span>
          <button class="book-preview-btn" onclick="showPreview()"><i class="bi bi-eye"></i> Preview Sample</button>
          <div class="book-share-row">
            <button class="share-btn" title="Share"><i class="bi bi-share"></i></button>
            <button class="share-btn" title="Twitter"><i class="bi bi-twitter-x"></i></button>
            <button class="share-btn" title="Facebook"><i class="bi bi-facebook"></i></button>
            <button class="share-btn" title="WhatsApp"><i class="bi bi-whatsapp"></i></button>
          </div>
        </div>
      </div>
      <div class="col-lg-6 col-md-8">
        <div class="breadcrumb-custom" id="breadcrumb-title-section" style="display:none"></div>
        <div class="detail-tags">
          ${book.tags.map(t => `<span class="detail-tag" onclick="filterByTagAndGo('${t}')">#${t}</span>`).join('')}
          ${book.badge ? `<span class="book-badge badge-${book.badge}" style="position:static">${book.badge.charAt(0).toUpperCase() + book.badge.slice(1)}</span>` : ''}
        </div>
        <h1 class="book-detail-title">${book.title}</h1>
        <div class="book-detail-author">by <span>${book.author}</span> &nbsp;|&nbsp; <span>${book.category}</span></div>
        <div class="rating-row">
          <span class="rating-big">${book.rating}</span>
          <div class="stars-big">${renderStars(book.rating)}</div>
          <span class="review-count">${book.reviews.toLocaleString()} ratings</span>
        </div>
        <div class="price-section">
          <div class="price-row">
            <span class="price-big">${isFree ? 'FREE' : '₹' + book.price}</span>
            ${book.originalPrice > 0 ? `<span class="price-original">₹${book.originalPrice}</span>` : ''}
            ${discount > 0 ? `<span class="price-discount">${discount}% OFF</span>` : ''}
          </div>
          <div class="formats-row">
            ${book.format.map((f, i) => `<button class="format-btn ${i === 0 ? 'active' : ''}" onclick="selectFormat(this, '${f}')">${f}</button>`).join('')}
          </div>
          <div class="action-buttons">
            <button class="btn-buy-now" onclick="buyNow(${book.id})"><i class="bi bi-lightning-fill"></i> Buy Now</button>
            <button class="btn-add-cart-detail" onclick="addToCart(${book.id}, event)"><i class="bi bi-cart-plus"></i> Add to Cart</button>
            <button class="wishlist-btn ${isWishlisted ? 'active' : ''}" data-wishlist="${book.id}" onclick="toggleWishlist(${book.id}, event)" style="position:static;opacity:1;background:var(--bg-card2);border:1.5px solid var(--border);width:48px;height:48px;border-radius:var(--radius-sm);">
              <i class="bi bi-heart${isWishlisted ? '-fill' : ''}"></i>
            </button>
          </div>
        </div>
        <div class="book-meta-grid">
          <div class="meta-item"><div class="meta-label">Publisher</div><div class="meta-value">${book.publisher}</div></div>
          <div class="meta-item"><div class="meta-label">Pages</div><div class="meta-value">${book.pages}</div></div>
          <div class="meta-item"><div class="meta-label">Language</div><div class="meta-value">${book.language}</div></div>
          <div class="meta-item"><div class="meta-label">ISBN</div><div class="meta-value" style="font-size:0.78rem">${book.isbn}</div></div>
        </div>
        <div class="description-section">
          <h4><i class="bi bi-file-text"></i> About this Book</h4>
          <div class="description-text collapsed" id="desc-text">${book.description} Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</div>
          <button class="read-more-btn" onclick="toggleDesc()">Read more <i class="bi bi-chevron-down"></i></button>
        </div>
        <div class="description-section">
          <h4><i class="bi bi-star"></i> Customer Reviews</h4>
          <div class="row g-3 mb-3">
            <div class="col-auto">
              <div style="font-size:3rem;font-weight:800;color:#ffc107;font-family:'Poppins',sans-serif">${book.rating}</div>
              <div class="stars-big">${renderStars(book.rating)}</div>
              <div style="font-size:0.8rem;color:var(--text-muted)">${book.reviews.toLocaleString()} reviews</div>
            </div>
            <div class="col">
              ${[5,4,3,2,1].map(n => `<div class="rating-bar-row"><span class="rating-bar-label">${n}★</span><div class="rating-bar-track"><div class="rating-bar-fill" style="width:${n === 5 ? 65 : n === 4 ? 20 : n === 3 ? 8 : n === 2 ? 4 : 3}%"></div></div><span class="rating-bar-count">${n === 5 ? 65 : n === 4 ? 20 : n === 3 ? 8 : n === 2 ? 4 : 3}%</span></div>`).join('')}
            </div>
          </div>
          ${reviews.map(r => `
            <div class="review-card">
              <div class="review-header">
                <div class="reviewer-avatar">${r.name[0]}</div>
                <div>
                  <div class="reviewer-name">${r.name}</div>
                  <div class="review-date">${r.date}</div>
                </div>
                <div class="stars-big" style="margin-left:auto">${renderStars(r.rating)}</div>
              </div>
              <div class="review-text">${r.text}</div>
              <div class="review-helpful">Helpful? <button class="helpful-btn">👍 Yes</button> <button class="helpful-btn">👎 No</button></div>
            </div>`).join('')}
        </div>
      </div>
      <div class="col-lg-3 d-none d-lg-block">
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:1.5rem;position:sticky;top:90px;">
          <h5 style="font-weight:700;margin-bottom:1rem;font-size:0.95rem">You may also like</h5>
          ${booksData.filter(b => b.category === book.category && b.id !== book.id).slice(0, 4).map(b => `
            <div onclick="goToDetail(${b.id})" style="display:flex;gap:0.8rem;padding:0.8rem 0;border-bottom:1px solid var(--border);cursor:pointer;transition:var(--transition);border-radius:var(--radius-sm)">
              <span style="font-size:2rem">${b.cover}</span>
              <div>
                <div style="font-size:0.82rem;font-weight:600;color:var(--text-white)">${b.title}</div>
                <div style="font-size:0.75rem;color:var(--text-muted)">${b.author}</div>
                <div style="font-size:0.85rem;color:var(--primary);font-weight:700">${b.price === 0 ? 'FREE' : '₹' + b.price}</div>
              </div>
            </div>`).join('')}
        </div>
      </div>
    </div>`;
}

function toggleDesc() {
  const el = document.getElementById('desc-text');
  const btn = document.querySelector('.read-more-btn');
  el.classList.toggle('collapsed');
  btn.innerHTML = el.classList.contains('collapsed') ? 'Read more <i class="bi bi-chevron-down"></i>' : 'Read less <i class="bi bi-chevron-up"></i>';
}

function selectFormat(btn, format) {
  document.querySelectorAll('.format-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}

function buyNow(bookId) {
  // Go directly to checkout with this single book (bypasses cart)
  window.location.href = `checkout.html?buynow=${bookId}`;
}

function showPreview() {
  showToast('Preview feature coming soon!', 'success');
}

function filterByTagAndGo(tag) {
  searchQuery = tag;
  window.location.href = `index.html?search=${tag}`;
}

// ===== AUTH =====
function initAuth() {
  const tabs = document.querySelectorAll('.auth-tab');
  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
      document.getElementById(tab.dataset.target)?.classList.add('active');
    });
  });

  const pwInput = document.getElementById('signup-password');
  if (pwInput) {
    pwInput.addEventListener('input', () => {
      const val = pwInput.value;
      const fill = document.querySelector('.strength-fill');
      if (!fill) return;
      fill.className = 'strength-fill';
      if (val.length > 8 && /[A-Z]/.test(val) && /[0-9]/.test(val)) fill.classList.add('strength-strong');
      else if (val.length > 5) fill.classList.add('strength-medium');
      else if (val.length > 0) fill.classList.add('strength-weak');
    });
  }

  const loginForm = document.getElementById('login-form-el');
  if (loginForm) {
    loginForm.addEventListener('submit', e => {
      e.preventDefault();
      showToast('Logged in successfully! Welcome back!', 'success');
      setTimeout(() => window.location.href = 'index.html', 1800);
    });
  }
  const signupForm = document.getElementById('signup-form-el');
  if (signupForm) {
    signupForm.addEventListener('submit', e => {
      e.preventDefault();
      showToast('Account created! Welcome to BookVault!', 'success');
      setTimeout(() => window.location.href = 'index.html', 1800);
    });
  }
}

function togglePassword(id) {
  const input = document.getElementById(id);
  if (!input) return;
  input.type = input.type === 'password' ? 'text' : 'password';
}

// ===== URL PARAMS =====
window.addEventListener('load', () => {
  const params = new URLSearchParams(window.location.search);
  const searchParam = params.get('search');
  if (searchParam && document.getElementById('books-container')) {
    searchQuery = searchParam;
  }
});

function formatIndianPhone(input) {
    let phone = input.replace(/\D/g, '');

    // Case 1: Already 12 digits with 91
    if (phone.length === 12 && phone.startsWith("91")) {
        return phone;
    }

    // Case 2: 10 digit number → add 91
    if (phone.length === 10) {
        return "91" + phone;
    }

    // Case 3: invalid
    return null;
}
function sendOtp(btn) {
    if (btn && btn.disabled) return;
    setButtonLoading(btn, "Sending...");
    let input = document.getElementById("phone").value.trim();
    let phone = formatIndianPhone(input);

    if (!phone) {
        resetButton(btn);
        showToast("Enter valid 10 digit number", "error");
        return;
    }

    console.log("Final Phone:", phone);

    fetch("/send-otp", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ phone })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            resetButton(btn);
            showToast(data.message || "Error sending OTP", "error");
            return;
        }

        document.getElementById("step-phone").style.display = "none";
        document.getElementById("step-otp").style.display = "block";

        startResendTimer();
    })
    .catch(() => {
        showToast("Server error", "error");
    })
    .finally(() => {
        resetButton(btn);
    });
}
function verifyOtp(btn) {
    if (btn && btn.disabled) return;
    setButtonLoading(btn, "Verifying...");
    let input = document.getElementById("phone").value.trim();
    let phone = formatIndianPhone(input); // ✅ SAME FUNCTION

    fetch("/verify-otp", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            phone: phone, // ✅ FIXED
            otp: document.getElementById("otp").value
        })
    })
    .then(res => res.json())
    .then(data => {

        if (!data.success) {
            resetButton(btn);
            showToast("Invalid OTP", "error");
            return;
        }

        if (data.type === "login") {
            window.location.href = "/";
        } else {
            document.getElementById("step-otp").style.display = "none";
            document.getElementById("step-register").style.display = "block";
        }
    })
    .finally(() => {
        resetButton(btn);
    });
}
function completeProfile(btn) {
    if (btn && btn.disabled) return;
    setButtonLoading(btn, "Creating...");
    let input = document.getElementById("phone").value.trim();
    let phone = formatIndianPhone(input); // ✅ FIX

    fetch("/complete-profile", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            name: document.getElementById("name").value,
            email: document.getElementById("email").value || null,
            password: document.getElementById("password").value,
            phone: phone // ✅ FIX
        })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            showToast(data.message || "Something went wrong", "error");
            return;
        }

        showToast("Account created successfully!", "success");
        setTimeout(() => window.location.href = "/", 1000);
    })
    .catch(() => {
        showToast("Server error", "error");
    })
    .finally(() => {
        resetButton(btn);
    });
}
function startResendTimer() {
    let time = 30;
    const timer = document.getElementById("timer");

    const interval = setInterval(() => {
        time--;
        timer.innerText = time;

        if (time <= 0) {
            clearInterval(interval);
            document.getElementById("resend-text").innerHTML =
                '<a href="#" onclick="sendOtp()">Resend OTP</a>';
        }
    }, 1000);
}
