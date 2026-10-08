window.isGenerating = false; // Flag to track generation process

// Handle Back Button with Custom Quit Modal
window.handleBackButton = function(event, fallbackUrl) {
  event.preventDefault();
  document.getElementById('quit-modal').classList.remove('hidden');
};

window.closeQuitModal = function() {
  document.getElementById('quit-modal').classList.add('hidden');
};

// Add a history state so we can intercept the browser back button
// Browsers require user interaction before allowing history manipulation
let backTrapInitialized = false;
function initBackTrap() {
  if (backTrapInitialized) return;
  backTrapInitialized = true;
  history.pushState({ trap: true }, null, location.href);
}

window.addEventListener('touchstart', initBackTrap, { once: true });
window.addEventListener('click', initBackTrap, { once: true });

window.addEventListener('popstate', function(event) {
  // Always prevent navigating away by pushing the state back and showing modal
  history.pushState({ trap: true }, null, location.href);
  document.getElementById('quit-modal').classList.remove('hidden');
});


document.addEventListener("DOMContentLoaded", () => {
  
  // --- Global State & Config ---\
  const API_BASE_URL = "https://autoai.aceqr.space/test-paper"; // Your backend URL
  let currentStep = 1;
  let publicationName = "My Awesome Publication";
  let audioContext = null;
  let alertModal = null; // To store modal instance
  let paramModal = null; // --- ADDED: To store param input modal ---

  // --- State for Step 1 (Chapter Selection) ---
  let ebookBaseUrl = ""; // The base_url (e.g., https://.../myebook/75)
  let subjectName = "";
  let subjectClass = "";
  let fetchedChaptersData = []; // Processed {title, start_page, end_page}
  
  // --- State for Step 2 (Format) ---\
  let selectedChapters = []; // Array of chapter TITLES
  let formatData = null; // { total, mcq, short, ... }
  
  // --- State for Step 3 (Review) ---\
  let paperQuestions = []; // ordered (what appears on the right & final)
  let aiQuestionPool = []; // "window.__AI_POOL" replacement

  // --- Page Refs ---\
  const page1 = document.getElementById("page-1-chapter-selection");
  const page2 = document.getElementById("page-2-format");
  const page3 = document.getElementById("page-3-review");
  const page4 = document.getElementById("page-4-final");
  const wizardDots = document.querySelectorAll(
    ".wizard-steps-vertical .step-dot"
  );

  // --- Page 1 Refs ---
  const subjectCardContainer = document.getElementById(
    "subject-card-container"
  );
  const fetchQuestionsBtn = document.getElementById("fetchQuestionsBtn");

  // --- Page 2 Refs ---
  const totalQuestionsEl = document.getElementById("totalQuestions");
  const numMCQ = document.getElementById("numMCQ");
  const numShort = document.getElementById("numShort");
  const numLong = document.getElementById("numLong");
  const numTF = document.getElementById("numTF");
  const numFITB = document.getElementById("numFITB");
  const toReviewBtn = document.getElementById("toReviewBtn");
  const backFromFormatBtn = document.getElementById("backFromFormatBtn");
  const formatValidation = document.getElementById("format-validation");
  const selectExamType = document.getElementById("selectExamType");
  const inputDuration = document.getElementById("inputDuration");
  const inputTestDate = document.getElementById("inputTestDate"); // Added this

  // --- Page 3 Refs ---
  const questionBankList = document.getElementById("question-bank-list");
  const bankFilters = document.getElementById("question-bank-filters");
  const addSectionBtn = document.getElementById("addSectionBtn");
  const sectionName = document.getElementById("sectionName");
  const paperQuestionsList = document.getElementById("paper-questions-list");
  // --- FIX: Corrected the ID here ---
  const backToFormatBtn = document.getElementById("backToFormatBtn");
  const toFinalLayoutBtn = document.getElementById("toFinalLayoutBtn");

  // --- Page 3 Header Refs ---
  const printPubName = document.getElementById("print-pub-name");
  const printSubject = document.getElementById("print-subject");
  const printMarks = document.getElementById("print-marks");
  const printDuration = document.getElementById("print-duration");
  const printDate = document.getElementById("print-date");
  const printCounts = document.getElementById("print-counts");
  
  // --- Page 4 Refs ---
  const backToBuildBtn = document.getElementById("backToBuildBtn");
  const finalPrintBtn = document.getElementById("finalPrintBtn");
  const finalPrintHeader = document.getElementById("final-print-header");
  const finalPaperQuestionsList = document.getElementById("final-paper-questions-list");
 
  // --- Theme Refs ---
  const themeToggle = document.getElementById("themeToggle");
  const brandTitle = document.getElementById("brand-title");

  // --- Initialize Modal & Error Function ---
  try {
    const modalEl = document.getElementById('alertModal');
    if (modalEl) {
      alertModal = new bootstrap.Modal(modalEl);
    }
    
    // --- ADDED: Initialize Param Modal ---
    const paramModalEl = document.getElementById('paramModal');
    if (paramModalEl) {
      paramModal = new bootstrap.Modal(paramModalEl, {
        backdrop: 'static', // Don't close on click outside
        keyboard: false     // Don't close on ESC
      });
    }
    // --- END ADDITION ---

  } catch (e) {
    console.error("Failed to initialize modals:", e);
    alertModal = null;
    paramModal = null; // --- ADDED ---
  }

  function showErrorModal(title, message) {
    console.error(title, message); // Log to console always
    if (alertModal) {
      const modalTitle = document.getElementById('alertModalHeader');
      const modalBody = document.getElementById('alertModalBody');
      if (modalTitle) modalTitle.querySelector('.modal-title').textContent = `⚠️ ${title}`;
      if (modalBody) modalBody.textContent = message;
      alertModal.show();
    } else {
      // Fallback if modal fails
      showGlobalToast(`Error: ${title} - ${message}`, 'error');
    }
  }

  // --- REMOVED: Base64 URL-safe encoding/decoding functions ---

  // --- Audio ---
  function initAudioContext() {
    if (!audioContext) {
      audioContext = new (window.AudioContext || window.webkitAudioContext)();
    }
  }

  function playPaperSound() {
    if (!audioContext) return;
    try {
      const bufferSize = audioContext.sampleRate * 0.07;
      const noiseBuffer = audioContext.createBuffer(
        1,
        bufferSize,
        audioContext.sampleRate
      );
      const output = noiseBuffer.getChannelData(0);
      for (let i = 0; i < bufferSize; i++) output[i] = Math.random() * 2 - 1;
      const whiteNoise = audioContext.createBufferSource();
      whiteNoise.buffer = noiseBuffer;
      const bandpass = audioContext.createBiquadFilter();
      bandpass.type = "bandpass";
      bandpass.frequency.setValueAtTime(1500, audioContext.currentTime);
      bandpass.Q.setValueAtTime(1, audioContext.currentTime);
      const gain = audioContext.createGain();
      gain.gain.setValueAtTime(0.6, audioContext.currentTime);
      gain.gain.exponentialRampToValueAtTime(
        0.01,
        audioContext.currentTime + 0.08
      );
      whiteNoise.connect(bandpass);
      bandpass.connect(gain);
      gain.connect(audioContext.destination);
      whiteNoise.start();
      whiteNoise.stop(audioContext.currentTime + 0.1);
    } catch (err) {
      console.error("Audio error", err);
    }
  }

  // --- Loader overlay (updated: animations only when visible) ---
  const _loaderId = "app-global-loader-overlay";
  function ensureLoaderExists() {
    if (document.getElementById(_loaderId)) return;
    const style = document.createElement("style");
    style.innerHTML = `
      #${_loaderId} {
        position: fixed; inset: 0;
        display:flex; align-items:center; justify-content:center;
        z-index: 99999;
        background: rgba(38, 71, 87, 0.9); /* solid grey background */
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        transition: opacity .25s ease;
        opacity: 0;
        pointer-events: none;
      }
      #${_loaderId}.visible { opacity: 1; pointer-events: auto; }



      #${_loaderId} .loader-card {
        display:flex; flex-direction:column; align-items:center; gap:12px;
        padding:22px 26px; border-radius:14px;
        background: linear-gradient(135deg, rgba(255,255,255,0.06), rgba(255,255,255,0.02));
        box-shadow: 0 6px 30px rgba(0,0,0,0.45);
        color: #fff; min-width:240px;
        transform: translateY(6px);
        opacity: 0;
        transition: transform .22s ease, opacity .22s ease;
      }
      #${_loaderId}.visible .loader-card {
        transform: translateY(0);
        opacity: 1;
      }

      #${_loaderId} .ring {
        width:100px; height:100px; border-radius:50%; display:grid;
        place-items:center; position:relative;
      }
      #${_loaderId} .ring svg {
        width:100px; height:100px; transform: rotate(-90deg);
      }
      #${_loaderId} .ring circle.bg {
        stroke: rgba(255,255,255,0.1); stroke-width:8; fill:none;
      }
      #${_loaderId} .ring circle.fg {
        stroke: url(#grad); stroke-width:8; stroke-linecap: round; fill:none;
        stroke-dasharray: 283; stroke-dashoffset: 283;
      }
      #${_loaderId}.visible .ring svg {
        animation: loader-rotate 1.6s linear infinite;
        transform-origin: center center;
      }
      #${_loaderId}.visible .ring circle.fg {
        animation: loader-dash 1.2s cubic-bezier(.3,.7,.2,1) infinite;
      }

      @keyframes loader-rotate { 0% { transform: rotate(0deg);} 100% { transform: rotate(360deg);} }
      @keyframes loader-dash { 0% { stroke-dashoffset: 283; } 50% { stroke-dashoffset: 70; } 100% { stroke-dashoffset: 283; } }

      /* Add AI text in center */
      #${_loaderId} .ring .center-text {
        position: absolute;
        font-weight: 700;
        font-size: 22px;
        color: #ffffff;
        text-shadow: 0 0 6px rgba(0,0,0,0.5);
        pointer-events: none;
      }

      #${_loaderId} .loader-text { font-weight:600; font-size:15px; text-align:center; color:inherit; }
      #${_loaderId} .loader-sub { font-size:13px; opacity:0.85; color:inherit; }
      #${_loaderId} .dots { display:flex; gap:6px; align-items:center; margin-top:6px; }
      #${_loaderId} .dot { width:8px; height:8px; border-radius:50%; background:rgba(255,255,255,0.9); opacity:0.3; animation: dot 1s infinite; }
      #${_loaderId} .dot:nth-child(1){ animation-delay:0s; } 
      #${_loaderId} .dot:nth-child(2){ animation-delay:.12s; } 
      #${_loaderId} .dot:nth-child(3){ animation-delay:.24s; } 
      @keyframes dot { 0%{opacity:.2; transform: translateY(0);} 50%{opacity:1; transform: translateY(-6px);} 100%{opacity:.2; transform: translateY(0);} }

      /* Same dark loader for both light and dark mode */
      body.light-mode #${_loaderId} {
        background: rgba(128,128,128,0.9);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
      }
      body.light-mode #${_loaderId} .loader-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.06), rgba(255,255,255,0.02));
        color:#fff;
        box-shadow: 0 6px 30px rgba(0,0,0,0.45);
      }
    `;
    document.head.appendChild(style);

    const overlay = document.createElement("div");
    overlay.id = _loaderId;
    overlay.innerHTML = `
      <div class="loader-card" role="status" aria-live="polite">
        <div class="ring" aria-hidden="true">
          <svg viewBox="0 0 100 100" preserveAspectRatio="xMidYMid meet">
            <defs>
              <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="#5ee7df"/>
                <stop offset="100%" stop-color="#8a2be2"/>
              </linearGradient>
            </defs>
            <circle class="bg" cx="50" cy="50" r="45"></circle>
            <circle class="fg" cx="50" cy="50" r="45"></circle>
          </svg>
          <div class="center-text">AI</div>
        </div>
        <div class="loader-text" id="${_loaderId}-text">Loading...</div>
        <div class="loader-sub" id="${_loaderId}-sub">Please wait</div>
        <div class="dots" aria-hidden="true"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>
      </div>
    `;
    document.body.appendChild(overlay);
  }

  function showLoader(mainText = "Loading...", subText = "") {
    ensureLoaderExists();
    const el = document.getElementById(_loaderId);
    if (!el) return;
    const t = document.getElementById(`${_loaderId}-text`);
    const s = document.getElementById(`${_loaderId}-sub`);
    if (t) t.textContent = mainText;
    if (s) s.textContent = subText || "";
    // Use rAF to ensure CSS transitions trigger
    requestAnimationFrame(() => el.classList.add("visible"));
  }
  function hideLoader() {
    const el = document.getElementById(_loaderId);
    if (el) el.classList.remove("visible");
  }

  // --- Theme helpers (unchanged from your new script) ---
  function applyThemeColors() {
    const isLight = document.body.classList.contains("light-mode");
    const bodyColor = isLight ? "#0f172a" : "#e2e8f0";

    document.querySelectorAll(".subject-card-header, .chapter-list li, .chapter-list label").forEach(n=>{
      n.style.color = bodyColor;
    });
    document.querySelectorAll(".question-options li").forEach(li => {
      li.style.color = bodyColor;
    });
    // Fix modal text color in light mode
    if (alertModal) {
        const modalContent = document.getElementById('alertModal').querySelector('.modal-content');
        const modalBody = document.getElementById('alertModalBody');
        if (isLight) {
            modalContent.style.backgroundColor = '#fff';
            modalBody.style.color = '#0f172a';
        } else {
            modalContent.style.backgroundColor = 'var(--panel-bg)';
            modalBody.style.color = 'var(--primary-color)';
        }
    }
    
    // --- ADDED: Fix for paramModal colors ---
    if (paramModal) {
        const modalContent = document.getElementById('paramModal').querySelector('.modal-content');
        const modalBody = document.getElementById('paramModal').querySelector('.modal-body');
         if (isLight) {
            modalContent.style.backgroundColor = '#fff';
            modalBody.style.color = '#0f172a';
        } else {
            modalContent.style.backgroundColor = 'var(--panel-bg)';
            modalBody.style.color = 'var(--primary-color)';
        }
    }
    // --- END ADDITION ---
  }

  if (themeToggle) {
    // Set initial theme from localStorage
    if (localStorage.getItem("theme") === "light-mode") {
      document.body.classList.add("light-mode");
      if (themeToggle) themeToggle.checked = true;
    }
    applyThemeColors(); // Apply colors on load

    themeToggle.addEventListener("change", () => {
      document.body.classList.toggle("light-mode");
      localStorage.setItem(
        "theme",
        document.body.classList.contains("light-mode")
          ? "light-mode"
          : "dark-mode"
      );
      applyThemeColors();
    });
  }
  
  // Update currentStep indicator visually
  function updateWizardDots(step) {
    window.currentWizardStep = step; // Expose to global scope for back button check
    wizardDots.forEach((dot) => {
      const step = Number(dot.dataset.step);
      dot.classList.remove("active", "completed");
      if (step === currentStep) dot.classList.add("active");
      if (step < currentStep) dot.classList.add("completed");
      dot.style.pointerEvents = step <= currentStep ? "auto" : "none";
    });
  }
  const wizardContainer = document.getElementById("wizard-steps");
  if (wizardContainer) {
    wizardContainer.addEventListener("click", (ev) => {
      const dot = ev.target.closest(".step-dot");
      if (!dot) return;
      const step = parseInt(dot.dataset.step, 10);
      if (!isNaN(step) && step <= currentStep) goToStep(step);
    });
  }

  function goToStep(step) {
    currentStep = step;
    if (page1) page1.classList.toggle("d-none", step !== 1);
    if (page2) page2.classList.toggle("d-none", step !== 2);
    if (page3) page3.classList.toggle("d-none", step !== 3);
    if (page4) page4.classList.toggle("d-none", step !== 4);
    updateWizardDots(currentStep);
    
    if (step === 3) {
      initializeReviewPage();
    }
    if (step === 4) {
      initializeFinalLayoutPage();
    }
  }

  // --- Page 1: Load Chapters from API ---
  
  // --- REVERTED: This function now just checks for params or shows modal ---
  // --- UPDATED: Smarter check for missing params ---
  async function loadInitialData() {
    // Read parameters from URL (e.g. ?base_url=https://...&pages=1,2,3)
    const urlParams = new URLSearchParams(window.location.search);
    const baseUrl = urlParams.get("base_url");    // full eBook base URL
    const indexPages = urlParams.get("pages");    // comma-separated list like "1,2,3"
    
    // Get modal elements
    const inputBaseUrl = document.getElementById("inputBaseUrl");
    const inputIndexPages = document.getElementById("inputIndexPages");
    const baseUrlInputGroup = document.getElementById("baseUrlInputGroup");
    const indexPagesInputGroup = document.getElementById("indexPagesInputGroup");

    if (baseUrl && indexPages) {
      // 1. Both params found in URL, fetch data immediately
      await fetchChapterData(baseUrl, indexPages);
    } else {
      // 2. One or more params are missing. Show the modal.
      if (!paramModal || !inputBaseUrl || !inputIndexPages || !baseUrlInputGroup || !indexPagesInputGroup) {
        // Fallback if modal or its inputs failed to init
        showErrorModal(
          "Missing Data",
          "Missing base_url or pages in the URL. Please add them to the URL bar and refresh."
        );
        return;
      }
      
      // Configure modal based on what's missing
      if (baseUrl) {
        // Base URL is present, pre-fill and hide it
        inputBaseUrl.value = baseUrl;
        baseUrlInputGroup.classList.add('d-none');
      } else {
        // Base URL is missing, ensure it's empty and shown
        inputBaseUrl.value = '';
        baseUrlInputGroup.classList.remove('d-none');
      }
      
      if (indexPages) {
        // Pages are present, pre-fill and hide it
        inputIndexPages.value = indexPages;
        indexPagesInputGroup.classList.add('d-none');
      } else {
        // Pages are missing, ensure it's empty and shown
        inputIndexPages.value = '';
        indexPagesInputGroup.classList.remove('d-none');
      }

      // Show the configured modal
      paramModal.show();
    }
  }

  // --- NEW FUNCTION: This holds the actual data fetching logic ---
  async function fetchChapterData(baseUrl, indexPages) {
    showLoader("Loading E-book...", "Fetching chapter index");

    try {
      const response = await fetch(`${API_BASE_URL}/get_titles`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          base_url: baseUrl,
          index_pages: indexPages,
        }),
      });

      if (!response.ok) {
        throw new Error(`Server error: ${response.status} ${response.statusText}`);
      }

      const data = await response.json();

      publicationName = data.publication_name || "My Publication";
      subjectName = data.subject_name || "Subject";
      subjectClass = data.class_name || "Class";
      ebookBaseUrl = baseUrl; // Save for later
      fetchedChaptersData = data.chapters || [];

      if (brandTitle) brandTitle.textContent = publicationName;

      populateSubjectCard();
    } catch (err) {
      console.error("Failed to load chapters:", err);
      showErrorModal(
        "Connection Failed",
        `Could not fetch chapter list from the backend. Ensure the server is running at ${API_BASE_URL}. \n\nError: ${err.message}`
      );
      // If the fetch fails (e.g., bad URL from modal), show the modal again
      if (paramModal) {
          paramModal.show();
      }
    } finally {
      hideLoader();
    }
  }
  // --- END NEW FUNCTION ---


  // populate chapter checkboxes
  function populateSubjectCard() {
    if (!subjectCardContainer || !fetchedChaptersData.length) return;
    
    subjectCardContainer.innerHTML = "";
    const header = `<div class="subject-card-header"><h4>${escapeHtml(subjectName)} <span class="subject-class">(${escapeHtml(subjectClass)})</span></h4></div>`;
    
    let listHtml = '<ul class="chapter-list">';
    fetchedChaptersData.forEach((ch, index) => {
      const chapterId = `chap-${index}`;
      // Use chapter.title as the value, just like the old script
      listHtml += `<li>
        <div class="form-check">
          <input class="form-check-input chapter-checkbox" type="checkbox" id="${chapterId}" value="${escapeHtml(ch.title)}">
          <label class="form-check-label" for="${chapterId}">${escapeHtml(ch.title)}</label>
        </div>
      </li>`;
    });
    listHtml +=
      '</ul><div class="text-center mt-2"><input id="select-all-chapters" type="checkbox" class="form-check-input"><label class="form-check-label ms-2" for="select-all-chapters">Select All</label></div>';
    
    subjectCardContainer.innerHTML = `<div class="subject-card">${header}${listHtml}</div>`;

    const selectAll = subjectCardContainer.querySelector(
      "#select-all-chapters"
    );
    const checkboxes = () =>
      Array.from(subjectCardContainer.querySelectorAll(".chapter-checkbox"));
    if (selectAll)
      selectAll.addEventListener("change", () =>
        checkboxes().forEach((cb) => (cb.checked = selectAll.checked))
      );

    // Ensure colors for newly created elements
    applyThemeColors();
  }
  
  // Page1 -> Page2
  if (fetchQuestionsBtn) {
    fetchQuestionsBtn.addEventListener("click", (e) => {
      e.preventDefault();
      const checked = Array.from(
        subjectCardContainer.querySelectorAll(".chapter-checkbox:checked")
      );
      if (!checked.length) {
        showErrorModal("No Chapters Selected", "Please select at least one chapter to continue.");
        return;
      }
      // Store the chapter TITLES, as expected by the backend
      selectedChapters = checked.map((c) => c.value);
      goToStep(2);
    });
  }

  // --- Page 2: Format & Generate ---
  if (backFromFormatBtn) {
    backFromFormatBtn.addEventListener("click", (e) => {
      e.preventDefault();
      goToStep(1);
    });
  }

  // Sync type inputs -> totalQuestions
  const typeInputs = [numMCQ, numShort, numLong, numTF, numFITB].filter(Boolean);
  function updateTotalFromTypes() {
    const sum = typeInputs.reduce((s, inp) => {
      const v = Number(inp.value || 0);
      return s + (Number.isFinite(v) ? v : 0);
    }, 0);
    if (totalQuestionsEl) totalQuestionsEl.value = sum;
    if (formatValidation) formatValidation.classList.add("d-none");
  }
  typeInputs.forEach((inp) => inp.addEventListener("input", updateTotalFromTypes));
  if (totalQuestionsEl) {
    totalQuestionsEl.addEventListener("input", () => {
        const total = Number(totalQuestionsEl.value || 0);
        const sum = typeInputs.reduce((s, inp) => s + (Number(inp.value || 0) || 0), 0);
        if (Number.isFinite(total) && total < sum) {
            totalQuestionsEl.value = sum; // Auto-correct total
        }
    });
  }

  // Page 2 -> Page 3 (Review) — THIS IS THE MAIN API CALL
  // Page 2 -> Page 3 (Review)
if (toReviewBtn) {
  toReviewBtn.addEventListener("click", async (e) => {
    e.preventDefault();

    const total = Number(totalQuestionsEl.value || 0);
    const mcq = Number(numMCQ.value || 0);
    const sh = Number(numShort.value || 0);
    const lg = Number(numLong.value || 0);
    const tf = Number(numTF.value || 0);
    const fitb = Number(numFITB.value || 0);

    // --- Validation ---
    if (!Number.isInteger(total) || total <= 0) {
      formatValidation.textContent = "Total Questions must be a positive integer.";
      formatValidation.classList.remove("d-none");
      return;
    }
    const sum = mcq + sh + lg + tf + fitb;
    if (sum !== total) {
      formatValidation.textContent = `Counts sum mismatch: sum (${sum}) does not match total (${total}).`;
      formatValidation.classList.remove("d-none");
      return;
    }
    formatValidation.classList.add("d-none");

    formatData = { total, mcq, short: sh, long: lg, tf, fitb };

    // ✅ Build payload that matches backend
    const payload = {
      base_url: ebookBaseUrl, // stored from /get_titles
      choices: selectedChapters, // array of selected chapter titles
      q_distribution: {
        mcq: mcq,
        short: sh,
        long: lg,
        tf: tf,
        fitb: fitb
      },
      difficulty: "medium", // you can make this a dropdown later
      chapters: {}
    };

    // Build chapter info dictionary from fetchedChaptersData
    fetchedChaptersData.forEach(ch => {
      if (selectedChapters.includes(ch.title)) {
        // --- FIX: Use 'page' from Gemini, not start_page/end_page ---
        // The backend /generate_questions needs the *actual* page numbers
        payload.chapters[ch.title] = {
          title: ch.title,
          start_page: ch.start_page || ch.page || 1, // Use 'page'
          end_page: ch.end_page || ch.page || 1   // Use 'page' (assuming 1 page per chapter for now)
          // If your /get_titles can return start AND end, use them here.
          // e.g., start_page: ch.start_page || ch.page || 1,
          // e.g., end_page: ch.end_page || ch.start_page || ch.page || 1
        };
      }
    });

    showLoader("Generating Questions...", "AI is at work, please wait.");
    window.isGenerating = true;

    try {
      const response = await fetch(`${API_BASE_URL}/generate_questions`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });

      if (!response.ok) {
        const text = await response.text();
        throw new Error(`Server error (${response.status}): ${text}`);
      }

      const data = await response.json();
      window.isGenerating = false;
      
      // --- ID SANITIZATION FIX ---
      // We can't trust backend IDs (e.g., multiple 'id: 1').
      // Assign our own guaranteed-unique ID ('app_id') once on fetch
      // using the array index.
      aiQuestionPool = (data.questions || []).map((q, index) => {
        q.question_type = (q.question_type || q.type || "").toLowerCase();
        q.app_id = `q-idx-${index}`; // Assign a stable, unique ID
        return q;
      });
      // --- END FIX ---
      
      paperQuestions = [];

      goToStep(3);
    } catch (err) {
      console.error("Failed to generate questions:", err);
      showErrorModal("Generation Failed", err.message);
    } finally {
      hideLoader();
      window.isGenerating = false;
    }
  });
}


  // --- REVIEW PAGE (page3) ---
  

  // populate bank with clickable items (AI pool on left)
function populateQuestionBank(filter = "All") {
  if (!questionBankList) return;
  questionBankList.innerHTML = "";

  // Normalize backend data just in case
  let items = aiQuestionPool.map(q => {
    q.question_type = (q.question_type || q.type || "").toLowerCase();
    // ID assignment is no longer needed here, it's done on fetch
    return q;
  });

  // Apply filter if not "All"
  if (filter !== "All") items = items.filter(q => q.question_type === filter);

  if (!items || !items.length) {
    questionBankList.innerHTML =
      '<div class="small text-muted p-3" style="color: var(--light-text);">No questions found for this filter.</div>';
    return;
  }

  // Pretty labels for badges
  const labelMap = {
    mcq: "MCQ",
    short: "Short",
    long: "Long",
    tf: "True/False",
    fitb: "Fill in the Blanks"
  };

  items.forEach(q => {
    const item = document.createElement("div");
    item.className = "bank-question-item";
    
    // --- USE app_id ---
    item.dataset.questionId = q.app_id; // Use our unique ID

    // Select badge color by type
    const badgeClass =
      q.question_type === "mcq"
        ? "bg-primary"
        : q.question_type === "short"
        ? "bg-success"
        : q.question_type === "long"
        ? "bg-danger"
        : q.question_type === "tf"
        ? "bg-info"
        : "bg-warning";

    const labelType = labelMap[q.question_type] || "Other";

    // --- MODIFICATION: Changed <label> to <div> ---
    // Using a <div> instead of <label> to avoid any potential
    // event conflicts from Bootstrap's .form-check-label class.
    item.innerHTML = `
      <div class="bank-question-text">
        ${escapeHtml(q.question_text)}
        <span class="badge ${badgeClass} ms-2">${labelType}</span>
      </div>
    `;
    // --- END MODIFICATION ---

    // Dim added questions
    // --- USE app_id ---
    if (paperQuestions.some(p => p.app_id === q.app_id)) {
      item.classList.add("added");
    }

    // --- NEW CLICK LISTENER ---
    item.addEventListener('click', () => {
      if (item.classList.contains('added')) {
        console.log("Already added:", q.app_id);
        return;
      }
      
      // Find the question from the pool using the ID from the closure
      // --- USE app_id ---
      const question = aiQuestionPool.find((p) => p.app_id === q.app_id);
      if (!question) {
         console.warn("Question not found in pool:", q.app_id);
         return;
      }

      initAudioContext();
      flyQuestionToPaper(item, question);
    });
    // --- END NEW LISTENER ---

    questionBankList.appendChild(item);
  });

  applyThemeColors();
}


function initializeReviewPage() {
  updateMetadata();
  populateQuestionBank("All");
  renderPaperList();

  // ✅ Reattach the click listener here so it binds to new DOM
  // --- THIS BLOCK IS NOW ACTIVE ---
  /* // --- REMOVING THIS BLOCK ---
  if (questionBankList) {
    questionBankList.onclick = (e) => {
      const item = e.target.closest(".bank-question-item");
      if (!item) return;

      const qId = item.dataset.questionId;
      const question = aiQuestionPool.find((q) => q.id === qId);

      if (!question) {
        console.warn("Question not found in pool:", qId);
        return;
      }

      if (paperQuestions.some((p) => p.id === qId)) {
        console.log("Already added:", qId);
        return;
      }

      initAudioContext();
      flyQuestionToPaper(item, question);
    };
  }
  */ // --- END REMOVED BLOCK ---
  // --- END ACTIVE BLOCK ---

  if (paperQuestionsList && !paperQuestionsList.dataset.sortableInitiated) {
    new Sortable(paperQuestionsList, {
      animation: 150,
      ghostClass: "sortable-ghost",
      onEnd: reorderPaperQuestionsFromDOM,
    });
    paperQuestionsList.dataset.sortableInitiated = "1";
  }

  applyThemeColors();
}

  
  // Bank filter buttons
  if (bankFilters) {
    bankFilters.addEventListener('click', (e) => {
        const btn = e.target.closest('button');
        if (!btn) return;
        bankFilters.querySelectorAll('button').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        populateQuestionBank(btn.dataset.filter);
    });
  }
 
  // --- NEW LISTENER for 'Back to Step 2' button ---
  if (backToFormatBtn) {
    backToFormatBtn.addEventListener('click', (e) => {
      e.preventDefault();
      goToStep(2); // Go back to the format page
    });
  }

  // --- NEW LISTENER for 'Next: Final Layout' button ---
  if (toFinalLayoutBtn) {
    toFinalLayoutBtn.addEventListener('click', (e) => {
      e.preventDefault();
      // Optional: Check if paper has questions
      if (paperQuestions.length === 0) {
        showErrorModal("Empty Paper", "Please add at least one question to the paper before proceeding.");
        return;
      }
      goToStep(4); // Go to the final layout page
    });
  }
  // --- END NEW LISTENER ---
  
  // click bank item -> fly to paper + sound + add
  // --- Click event for AI question bank ---

  // --- THIS BLOCK IS NOW COMMENTED OUT TO PREVENT DUPLICATES ---
  /*
  if (questionBankList) {
    questionBankList.addEventListener("click", (e) => {
      const item = e.target.closest(".bank-question-item");
      if (!item) return;

      const qId = item.dataset.questionId;
      const question = aiQuestionPool.find((q) => q.id === qId);

      if (!question) {
        console.warn("Question not found in pool:", qId);
        return;
      }

      // Prevent re-adding
      if (paperQuestions.some((p) => p.id === qId)) {
        console.log("Already added:", qId);
        return;
      }

      // ✅ Play animation & add to paper
      initAudioContext();
      flyQuestionToPaper(item, question);
    });
  }
  */
  // --- END COMMENTED BLOCK ---


  // fly animation + add to paper
  function flyQuestionToPaper(itemElement, question) {
    // --- USE app_id ---
    if (paperQuestions.some((p) => p.app_id === question.app_id)) {
      return; // already added
    }

    if (itemElement) itemElement.classList.add("added");

    const start = itemElement.getBoundingClientRect();
    const end = paperQuestionsList.getBoundingClientRect();

    const clone = itemElement.cloneNode(true);
    clone.classList.add("question-fly-animation");
    clone.style.left = `${start.left}px`;
    clone.style.top = `${start.top}px`;
    clone.style.width = `${start.width}px`;
    document.body.appendChild(clone);

    playPaperSound();

    requestAnimationFrame(() => {
      clone.style.left = `${end.left + end.width / 2 - 120}px`;
      clone.style.top = `${end.top + end.height / 2 - 20}px`;
      clone.style.opacity = "0";
      clone.style.transform = "scale(0.6)";
    });

    setTimeout(() => {
      paperQuestions.push(Object.assign({}, question));
      renderPaperList(); // Render the paper on the right
      clone.remove();
      // No need to refresh bank, 'added' class was already applied
    }, 650);
  }

  // add section
  if (addSectionBtn && sectionName) {
    addSectionBtn.addEventListener("click", () => {
      const name = sectionName.value.trim();
      if (!name) return;
      paperQuestions.push({
        type: "section",
        // --- USE app_id ---
        app_id: `section-${Date.now()}`,
        title: name,
      });
      sectionName.value = "";
      renderPaperList();
    });
  }

  // render paper list (right side)
  function renderPaperList() {
    if (!paperQuestionsList) return;
    paperQuestionsList.innerHTML = "";
    paperQuestions.forEach((q, idx) => {
      const wrapper = document.createElement("div");
      wrapper.className = "preview-item";
      wrapper.dataset.index = idx; // Store original index
      // --- USE app_id ---
      wrapper.dataset.id = q.app_id;   // Store unique ID

      if (q.type === "section") {
        wrapper.classList.add("section-header-item");
        wrapper.innerHTML = `<div class="section-center">${escapeHtml(q.title)}</div><button class="btn-remove-item" title="Remove section"><i class="fas fa-times"></i></button>`;
        wrapper
          .querySelector(".btn-remove-item")
          ?.addEventListener("click", () => {
            // --- USE app_id ---
            paperQuestions = paperQuestions.filter((p) => p.app_id !== q.app_id);
            renderPaperList();
            // Refresh bank to un-mark 'added'
            populateQuestionBank(
              bankFilters ? (bankFilters.querySelector("button.active")?.dataset.filter || "All") : "All"
            );
          });
      } else {
        const optionsHtml = buildOptionsHtml(q);
        wrapper.innerHTML = `
          <div class="question-item">
            <div class="question-header">
              <span class="question-text"><span class="question-number"></span> ${escapeHtml(
                q.question_text
              )}</span>
              <span class="question-marks">[${q.marks || 0} Marks]</span>
            </div>
            <div class="question-body">${optionsHtml}</div>
          </div>
          <button class="btn-remove-item" title="Remove question"><i class="fas fa-times"></i></button>
        `;
        wrapper
          .querySelector(".btn-remove-item")
          ?.addEventListener("click", () => {
            // --- USE app_id ---
            paperQuestions = paperQuestions.filter((p) => p.app_id !== q.app_id);
            renderPaperList();
            // Refresh bank to un-mark 'added'
            populateQuestionBank(
              bankFilters ? (bankFilters.querySelector("button.active")?.dataset.filter || "All") : "All"
            );
          });
      }
      paperQuestionsList.appendChild(wrapper);
    });
    updateQuestionNumbers();
    updateMetadata();
    applyThemeColors();
  }

  function buildOptionsHtml(q) {
    if (!q) return "";
    // Handle 'mcq' (lowercase) and 'options' as object OR array
    if (q.question_type === "mcq" && q.options) {
      let out = '<ol class="question-options" type="a">';
      
      if (Array.isArray(q.options)) {
        // Handle array: ["A) opt1", "B) opt2"] or ["opt1", "opt2"]
         q.options.forEach(
            (opt) =>
              (out += `<li>${escapeHtml(String(opt).replace(/^[A-D][.)]\s*/, ""))}</li>`)
          );
      } else if (typeof q.options === 'object') {
        // Handle object: {"A": "opt1", "B": "opt2"}
        for (const key in q.options) {
            out += `<li>${escapeHtml(q.options[key])}</li>`
        }
      }
      
      out += "</ol>";
      return out;
    }
    return ""; // No answer space for other types
  }

  function reorderPaperQuestionsFromDOM() {
    if (!paperQuestionsList) return;
    const newOrder = [];
    paperQuestionsList.querySelectorAll(".preview-item").forEach((node) => {
       const id = node.dataset.id; // This is now our app_id
       // --- USE app_id ---
       const found = paperQuestions.find(p => p.app_id === id);
       if (found) {
           newOrder.push(found);
       }
    });

    if (newOrder.length === paperQuestions.length) {
       paperQuestions = newOrder;
    } else {
       console.warn("Reorder failed, lengths mismatch");
    }
    
    // Re-render to fix dataset.index and question numbers
    renderPaperList(); 
  }

  function updateQuestionNumbers() {
    let n = 1;
    if (!paperQuestionsList) return;
    paperQuestionsList.querySelectorAll(".preview-item").forEach((el) => {
      const numSpan = el.querySelector(".question-number");
      if (numSpan) {
        numSpan.textContent = `Q${n}.`;
        n++;
      }
    });
  }

  function computeCounts() {
    const counts = {
      total: 0, mcq: 0, short: 0, long: 0,
      tf: 0, fitb: 0,
    };
    paperQuestions.forEach(q => {
      if (q.type === "section") return;
      counts.total += 1;
      const key = (q.question_type || "").toLowerCase();
      if (counts.hasOwnProperty(key)) counts[key] += 1;
    });
    return counts;
  }

  function updateMetadata() {
    let marks = 0;
    paperQuestions.forEach((p) => {
      if (p.type !== "section") marks += Number(p.marks || 0);
    });

    const duration = inputDuration?.value || "30";
    const dateStr = inputTestDate?.value
      ? new Date(inputTestDate.value).toLocaleDateString("en-IN")
      : new Date().toLocaleDateString("en-IN");
    const subject = `${subjectName} (${subjectClass})`;
    const examType = formatData?.examType || (selectExamType?.value || "Class Test");

    // --- Update Step 3 Header ---
    if (printMarks) printMarks.textContent = `Total Marks: ${marks}`;
    if (printDuration) printDuration.textContent = `Duration: ${duration} minutes`;
    if (printDate) printDate.textContent = `Date: ${dateStr}`;
    if (printSubject) printSubject.textContent = subject;
    if (printPubName) printPubName.textContent = publicationName;

    // --- Hide or clear the counts area ---
    if (printCounts) printCounts.textContent = ""; // or use: printCounts.style.display = "none";

    // --- Update Final Print Header ---
    if (finalPrintHeader) {
      finalPrintHeader.innerHTML = `
        <div class="final-print-header">
          <h1>${escapeHtml(publicationName)}</h1>
          <div class="meta-row">${escapeHtml(subject)} — ${escapeHtml(examType)}</div>
          <div class="meta-row">Date: ${dateStr} | Duration: ${duration} mins | Marks: ${marks}</div>
        </div>
      `;
    }

    applyThemeColors();
  }

  
  
  // --- FINAL LAYOUT PAGE (page4) ---
  function initializeFinalLayoutPage() {
      updateMetadata();
      
      if (finalPaperQuestionsList) {
        finalPaperQuestionsList.innerHTML = "";
        
        let qNum = 1;
        paperQuestions.forEach((q, idx) => {
          const wrap = document.createElement("div");
          wrap.className = "preview-item";
          // --- USE app_id ---
          wrap.dataset.id = q.app_id; // Use persistent ID
          
          if (q.type === "section") {
            wrap.classList.add("section-header-item");
            wrap.innerHTML = `<div class="section-center">${escapeHtml(q.title)}</div>`;
          } else {
            wrap.innerHTML = `<div class="question-item">
              <div class="question-header">
                <span class="question-text">Q${qNum}. ${escapeHtml(q.question_text)}</span>
                <span class="question-marks">[${q.marks||0} Marks]</span>
              </div>
              <div class="question-body">${buildOptionsHtml(q)}</div>
            </div>`;
            qNum++; // Only increment for actual questions
          }
          finalPaperQuestionsList.appendChild(wrap);
        });

        // Make final list sortable
        if (!finalPaperQuestionsList.dataset.sortableInitiated) {
          new Sortable(finalPaperQuestionsList, {
            animation: 150,
            ghostClass: "sortable-ghost",
            onEnd: (evt) => {
              const newOrder = [];
              finalPaperQuestionsList.querySelectorAll('.preview-item').forEach(node => {
                const id = node.dataset.id; // This is app_id
                // --- USE app_id ---
                const found = paperQuestions.find(p => p.app_id === id);
                if (found) newOrder.push(found);
              });
              
              if (newOrder.length === paperQuestions.length) {
                paperQuestions = newOrder;
                renderPaperList(); // Sync page-3 order
                initializeFinalLayoutPage(); // Re-render final page to fix Q numbers
              }
            }
          });
          finalPaperQuestionsList.dataset.sortableInitiated = '1';
        }
      }
      applyThemeColors();
  }
  
  if (backToBuildBtn) {
    backToBuildBtn.addEventListener('click', (e) => {
        e.preventDefault();
        goToStep(3);
    });
  }
  
  // Page 4 -> Print (This uses your new, more robust print logic)
  if (finalPrintBtn) {
    finalPrintBtn.addEventListener('click', () => {
      const printable = buildPrintableHTMLForFinalPreview();
      if (!printable) {
        showErrorModal("Nothing to Print", "The final paper preview is empty.");
        return;
      }

      const printWindow = window.open("", "_blank");
      if (!printWindow) {
        showErrorModal("Popup Blocked", "Please allow popups for this site to print the paper.");
        return;
      }

      printWindow.document.open();
      printWindow.document.write(printable);
      printWindow.document.close();

      printWindow.onload = function () {
        try {
          printWindow.focus();
          printWindow.print();
        } catch (err) {
          console.error("Print error:", err);
        } 
        // We don't close automatically, gives user time
      };
    });
  }

  // --- SAVE PAPER LOGIC ---
  const savePaperBtn = document.getElementById("savePaperBtn");
  if (savePaperBtn) {
    savePaperBtn.addEventListener('click', async () => {
      if (paperQuestions.length === 0) {
        showErrorModal("Empty Paper", "Cannot save an empty paper.");
        return;
      }
      
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const ebookId = document.querySelector('meta[name="ebook-id"]')?.getAttribute('content');
      const assignedEbookId = document.querySelector('meta[name="assigned-ebook-id"]')?.getAttribute('content');
      
      if (!csrfToken || !ebookId || !assignedEbookId) {
        showErrorModal("Missing Data", "CSRF token, Ebook ID, or Assigned Ebook ID missing. Cannot save.");
        return;
      }

      showLoader("Saving Paper...", "Please wait.");
      
      try {
        const payload = {
          ebook_id: ebookId,
          paperQuestions: paperQuestions.map(q => {
            return {
              type_name: q.question_type,
              chapter: q.chapter,
              chapter_num: q.chapter_num,
              section: q.section,
              question: q.question_text,
              diagrams: q.diagrams,
              options: q.options,
              answer: q.answer,
              answer_text: q.answer_text,
              marks: q.marks
            };
          })
        };

        const response = await fetch('/student/aitest/save', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          },
          body: JSON.stringify(payload)
        });

        if (!response.ok) {
            throw new Error(`Server returned ${response.status}`);
        }
        
        const data = await response.json();
        hideLoader();
        
        // Show modern toast success
        showGlobalToast(data.message || 'Paper saved successfully!', 'success');
        
        // Redirect back to index
        setTimeout(() => {
            window.location.href = `/student/aitest/${assignedEbookId}`;
        }, 1500);

      } catch (err) {
        hideLoader();
        console.error("Save error:", err);
        showErrorModal("Save Failed", "Could not save the paper: " + err.message);
      }
    });
  }


  // --- Helper Functions (Unchanged) ---
  function escapeHtml(str) {
    if (!str) return "";
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;");
  }

  function buildPrintStyles() {
  // UPDATED: remove question numbers & marks from the print output,
  // and hide the counts meta-row in the printed header.
  return `
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Test Paper</title>
    <style>
      /* Force high-contrast printing */
      html,body {
        margin:12mm;
        font-family: Arial, Helvetica, sans-serif;
        color:#000 !important;
        background:#fff !important;
        -webkit-print-color-adjust: exact !important;
        color-adjust: exact !important;
        print-color-adjust: exact !important;
      }

      .final-print-header{ text-align:center; margin-bottom:12px; color:#000 !important;}
      .final-print-header h1{ margin:0 0 6px 0; font-size:20px; font-weight:800 !important; color:#000 !important;}
      .final-print-header .meta-row{ font-size:12px; margin:6px 0; font-weight:700 !important; color:#000 !important; }

      /* Hide the counts line (e.g. "Total: 2 — MCQ: 1...") in the printed header */
      .final-print-header .meta-row:last-of-type { display: none !important; }

      #print-content{ counter-reset: question; color:#000 !important; }

      .preview-item, .section-header-item{
        background:transparent !important;
        color:#000 !important;
        border:none !important;
        box-shadow:none !important;
        page-break-inside:avoid;
        padding:4px 0;
      }

      .section-header-item{
        font-weight:800 !important;
        font-size:1.05em !important;
        margin:12px 0 8px 0;
        text-align:center !important;
        border-bottom: 1px solid #444 !important;
        padding-bottom:6px;
        color:#000 !important;
      }

      .question-item { margin-bottom: 12px; color:#000 !important; }
      .question-header { display:flex; justify-content: space-between; align-items:flex-start; }
      .question-text{ /* keep question text, but hide the number via separate selector */
        font-weight:700 !important;
        margin:6px 0;
        white-space:pre-wrap;
        padding-right:15px;
        flex:1;
        color:#000 !important;
        font-size:14px !important;
      }

      /* HIDE question numbers and marks in print */
      .question-number { display: none !important; }
      .question-marks { display: none !important; }

      .question-options{ margin-left:18px; margin-top:6px; color:#000 !important; }
      .question-options li{ margin-bottom:6px; color:#000 !important; font-weight:500 !important; }

      ol,ul{ margin:6px 0 12px 22px; color:#000 !important; }
      li{ margin-bottom:6px; color:#000 !important; }

      .badge, .meta-row, .small { color:#000 !important; background:transparent !important; }

      @media print {
        body{ -webkit-print-color-adjust:exact !important; print-color-adjust: exact !important; }
        @page { margin: 10mm; }
        .section-header-item { text-align: center !important; border-bottom: none !important; }
      }
    </style>
  `;
}
  function buildPrintableHTMLForFinalPreview() {
    // (This function is unchanged from your new script)
    if (!finalPrintHeader || !finalPaperQuestionsList) return null;
    const headerClone = finalPrintHeader.cloneNode(true);
    const questionsClone = finalPaperQuestionsList.cloneNode(true);
    headerClone.querySelectorAll("button, input, select, a").forEach(n => n.remove());
    questionsClone.querySelectorAll("button, input, select, a").forEach(n => n.remove());
    questionsClone.querySelectorAll(".btn, .btn-remove-item, .added").forEach(n => n.remove());
    questionsClone.querySelectorAll("[data-index]").forEach(n => n.removeAttribute("data-index"));
    // --- FIX: Use data-id (which stores our app_id) not data-question-id ---
    questionsClone.querySelectorAll("[data-id]").forEach(n => n.removeAttribute("data-id"));
    const styles = buildPrintStyles();
    const body = `
      <div class="final-print-header">${headerClone.innerHTML}</div>
      <div id="print-content">${questionsClone.innerHTML}</div>
    `;
    return `<!doctype html><html><head>${styles}</head><body>${body}</body></html>`;
  }

  // --- Initial Load ---
  updateWizardDots(currentStep);
  document.body.addEventListener("click", initAudioContext, { once: true });
  
  // Set default date for input
  if (inputTestDate && !inputTestDate.value) {
    inputTestDate.valueAsDate = new Date();
  }
  
  // --- REVERTED: Listener for param modal submit ---
  const submitParamBtn = document.getElementById("submitParamModalBtn");
  const inputBaseUrl = document.getElementById("inputBaseUrl");
  const inputIndexPages = document.getElementById("inputIndexPages");

  if (submitParamBtn && inputBaseUrl && inputIndexPages) {
    submitParamBtn.addEventListener("click", async () => {
      const baseUrl = inputBaseUrl.value.trim();
      const indexPages = inputIndexPages.value.trim();

      if (!baseUrl || !indexPages) {
        // You could show a small error message inside the modal
        console.warn("Modal inputs are empty");
        // Simple validation feedback
        inputBaseUrl.classList.toggle('is-invalid', !baseUrl);
        inputIndexPages.classList.toggle('is-invalid', !indexPages);
        return;
      }
      
      // Clear validation
      inputBaseUrl.classList.remove('is-invalid');
      inputIndexPages.classList.remove('is-invalid');

      // --- REMOVED: Generate and display the URL ---

      // Hide the modal
      if (paramModal) {
        paramModal.hide();
      }
      
      // Now, call the fetch function with the modal's data
      await fetchChapterData(baseUrl, indexPages);
    });
  }
  // --- END REVERT ---

  // --- REMOVED: Copy URL functionality ---


  // Start by fetching data from the backend
  loadInitialData();

});

function showGlobalToast(message, type = 'success') {
    const toast = document.createElement('div');
    
    let iconSvg = '';
    if (type === 'success') {
        iconSvg = `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>`;
    } else if (type === 'error') {
        iconSvg = `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>`;
    } else {
        iconSvg = `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`;
    }
    
    toast.innerHTML = `<span style="display: flex; align-items: center; justify-content: center; margin-right: 12px; filter: drop-shadow(0 2px 2px rgba(0,0,0,0.2));">${iconSvg}</span> <span>${message}</span>`;
    toast.style.position = 'fixed';
    toast.style.top = '30px';
    toast.style.left = '50%';
    toast.style.transform = 'translateX(-50%) scale(0.8)';
    toast.style.display = 'flex';
    toast.style.alignItems = 'center';
    toast.style.justifyContent = 'center';
    
    if (type === 'error') {
        toast.style.background = 'linear-gradient(180deg, #FF9999, #FF6B6B)';
        toast.style.border = '3px solid #FFF';
        toast.style.boxShadow = '0 0 0 3px #FF4B4B, 0 6px 0 3px #D32F2F';
        toast.style.color = '#FFF';
    } else if (type === 'info') {
        toast.style.background = 'linear-gradient(180deg, #8BDDFF, #4FC3F7)';
        toast.style.border = '3px solid #FFF';
        toast.style.boxShadow = '0 0 0 3px #29B6F6, 0 6px 0 3px #0288D1';
        toast.style.color = '#FFF';
    } else { // success
        toast.style.background = 'linear-gradient(180deg, #9DE182, #68CC45)';
        toast.style.border = '3px solid #FFF';
        toast.style.boxShadow = '0 0 0 3px #3AAA5B, 0 6px 0 3px #2E7D32';
        toast.style.color = '#FFF';
    }
    
    toast.querySelector('span:last-child').style.textShadow = '0 2px 4px rgba(0,0,0,0.3)';
    
    toast.style.padding = '10px 28px';
    toast.style.borderRadius = '8px';
    toast.style.fontFamily = "'Quicksand', sans-serif";
    toast.style.fontWeight = '900';
    toast.style.fontSize = '18px';
    toast.style.zIndex = '99999';
    toast.style.opacity = '0';
    toast.style.transition = 'all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
    
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(-50%) scale(1)';
    }, 10);

    setTimeout(() => {
        if (toast) {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(-50%) scale(0.8)';
            setTimeout(() => toast.remove(), 500);
        }
    }, 3500);
}
