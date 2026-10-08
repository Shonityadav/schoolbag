<style>
    #pwa-install-banner {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        width: calc(100% - 32px);
        max-width: 400px;
        background: #FFF9E5;
        border: 2px solid #E8D8A0;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        padding: 16px;
        display: none;
        align-items: center;
        justify-content: space-between;
        z-index: 100000;
        font-family: 'Quicksand', sans-serif;
        color: #5E4D3B;
    }
    
    #pwa-install-banner.show-banner {
        display: flex;
        animation: popUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }

    @keyframes popUp {
        0% { transform: translate(-50%, 100px); opacity: 0; }
        100% { transform: translate(-50%, 0); opacity: 1; }
    }

    .pwa-banner-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .pwa-banner-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        object-fit: cover;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .pwa-banner-text-title {
        font-family: 'Bubblegum Sans', cursive;
        font-size: 18px;
        color: #D89839;
        margin-bottom: 2px;
        line-height: 1.1;
    }

    .pwa-banner-text-desc {
        font-size: 12px;
        font-weight: 700;
        color: #8D7E6A;
        line-height: 1.2;
    }

    .pwa-banner-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pwa-btn-install {
        background: linear-gradient(135deg, #FFD561, #FFB37C);
        color: #5E4D3B;
        border: none;
        padding: 8px 16px;
        border-radius: 999px;
        font-family: 'Bubblegum Sans', cursive;
        font-size: 16px;
        box-shadow: 0 4px 0 #CC7A00;
        cursor: pointer;
        transition: transform 0.1s, box-shadow 0.1s;
    }

    .pwa-btn-install:active {
        transform: translateY(4px);
        box-shadow: 0 0 0 #CC7A00;
    }

    .pwa-btn-close {
        background: rgba(0,0,0,0.05);
        color: #8D7E6A;
        border: none;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 16px;
        transition: background 0.2s;
    }

    .pwa-btn-close:hover {
        background: rgba(0,0,0,0.1);
        color: #5E4D3B;
    }
</style>

<div id="pwa-install-banner">
    <div class="pwa-banner-left">
        <img src="{{ asset('app-icons/icon-192x192-v2.png') }}" class="pwa-banner-icon" alt="App Icon">
        <div>
            <div class="pwa-banner-text-title">SchoolBag App</div>
            <div class="pwa-banner-text-desc">Install for a faster,<br>better experience!</div>
        </div>
    </div>
    <div class="pwa-banner-actions">
        <button id="pwa-btn-install" class="pwa-btn-install">Install</button>
        <button id="pwa-btn-close" class="pwa-btn-close">&times;</button>
    </div>
</div>

<script>
    let deferredPrompt;
    const pwaBanner = document.getElementById('pwa-install-banner');
    const installBtn = document.getElementById('pwa-btn-install');
    const closeBtn = document.getElementById('pwa-btn-close');

    // Check if already installed
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    
    // Check if user dismissed recently
    const hasDismissed = localStorage.getItem('pwa-banner-dismissed') === 'true';

    // iOS Detection
    const isIos = () => {
        const userAgent = window.navigator.userAgent.toLowerCase();
        return /iphone|ipad|ipod/.test(userAgent);
    };

    // iOS doesn't support beforeinstallprompt, so we manually show it
    if (isIos() && !isStandalone && !hasDismissed) {
        setTimeout(() => {
            pwaBanner.classList.add('show-banner');
        }, 1500);
    }

    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent Chrome from automatically showing the prompt
        e.preventDefault();
        deferredPrompt = e;
        
        // Show our custom banner if not installed and not dismissed
        if (!isStandalone && !hasDismissed && !isIos()) {
            setTimeout(() => {
                pwaBanner.classList.add('show-banner');
            }, 1500);
        }
    });

    installBtn.addEventListener('click', async () => {
        if (isIos()) {
            alert("To install on iPhone/iPad:\n\n1. Tap the Share button (square with arrow) at the bottom of Safari.\n2. Scroll down and tap 'Add to Home Screen'.");
            return;
        }

        if (deferredPrompt) {
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            if (outcome === 'accepted') {
                pwaBanner.classList.remove('show-banner');
            }
            deferredPrompt = null;
        }
    });

    closeBtn.addEventListener('click', () => {
        pwaBanner.classList.remove('show-banner');
        localStorage.setItem('pwa-banner-dismissed', 'true');
    });

    window.addEventListener('appinstalled', () => {
        pwaBanner.classList.remove('show-banner');
    });
</script>
