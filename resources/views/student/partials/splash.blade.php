<div id="magic-splash-screen">
    <div class="scene">
        <div class="books-wrapper">
            <img src="{{ asset('uploads/images/splash/book.png') }}" class="book">
        </div>
        <div class="bag-container">
            <div class="glow"></div>
            <div class="inside-shadow"></div>
            <img src="{{ asset('uploads/images/splash/bag-body.png') }}" class="bag-body">
            <div class="flap-wrapper">
                <img src="{{ asset('uploads/images/splash/png2.png') }}" class="flap flap-front">
                <img src="{{ asset('uploads/images/splash/png1.png') }}" class="flap flap-back">
                <div class="lock-wrap">
                    <img src="{{ asset('uploads/images/splash/lock.png') }}" class="lock">
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<!-- Preload critical splash images so they load instantly -->
<link rel="preload" href="{{ asset('uploads/images/splash/1.png') }}" as="image">
<link rel="preload" href="{{ asset('uploads/images/splash/book.png') }}" as="image">
<link rel="preload" href="{{ asset('uploads/images/splash/bag-body.png') }}" as="image">
<link rel="preload" href="{{ asset('uploads/images/splash/png1.png') }}" as="image">
<link rel="preload" href="{{ asset('uploads/images/splash/png2.png') }}" as="image">
<link rel="preload" href="{{ asset('uploads/images/splash/lock.png') }}" as="image">

<style>
#magic-splash-screen {
    position: fixed;
    inset: 0;
    z-index: 99999999;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #A8E8FF;
    background-image: url("{{ asset('uploads/images/splash/1.png') }}");
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    overflow: hidden;
    transition: opacity 0.5s ease;
}

#magic-splash-screen .scene {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 30px;
}
#magic-splash-screen .bag-container {
    position: relative;
    perspective: 1200px;
    width: 420px;
    aspect-ratio: 1/1;
    cursor: pointer;
}
#magic-splash-screen .bag-container img {
    position: absolute;
    width: 100%;
    left: 0;
    top: 0;
    user-select: none;
    pointer-events: none;
}
#magic-splash-screen .bag-body {
    z-index: 1;
}
#magic-splash-screen .inside-shadow {
    position: absolute;
    width: 50%;
    height: 24%;
    left: 25%;
    top: 28%;
    border-radius: 50%;
    background: radial-gradient(ellipse at center, rgba(0, 0, 0, 0.28) 0%, rgba(0, 0, 0, 0.05) 70%, transparent 100%);
    filter: blur(20px);
    opacity: 0;
    z-index: 2;
}
#magic-splash-screen .books-wrapper {
    position: absolute;
    width: 100%;
    height: 100%;
    left: 0;
    top: 0;
    z-index: 2;
    pointer-events: none;
}
#magic-splash-screen .book {
    position: fixed;
    width: 65px;
    left: 0;
    top: 0;
    opacity: 0;
    z-index: 2;
    transform-origin: center center;
    object-fit: cover;
    border-radius: 14px;
    pointer-events: none;
    will-change: transform;
    filter: drop-shadow(0 20px 30px rgba(0, 0, 0, 0.25));
}
#magic-splash-screen .flap-wrapper {
    position: absolute;
    width: 45%;
    height: 24%;
    left: 28%;
    top: 25%;
    z-index: 3;
    transform-origin: 50% 8%;
    transform-style: preserve-3d;
}
#magic-splash-screen .flap {
    width: 100% !important;
    left: 0 !important;
    top: 0 !important;
    z-index: -1;
    filter: drop-shadow(0 6px 10px rgba(0, 0, 0, 0.08));
}
#magic-splash-screen .flap-front {
    backface-visibility: hidden;
}
#magic-splash-screen .flap-back {
    opacity: 0;
}
#magic-splash-screen .lock-wrap {
    position: absolute;
    width: 58px;
    height: 58px;
    left: 41%;
    top: 90%;
    z-index: 4;
}
#magic-splash-screen .lock {
    width: 100% !important;
    height: 100%;
    object-fit: contain;
    transform-origin: center top;
}
#magic-splash-screen .glow {
    position: absolute;
    width: 220px;
    height: 220px;
    border-radius: 50%;
    left: 50%;
    top: 38%;
    transform: translate(-50%, -50%) scale(0.2);
    opacity: 0;
    z-index: 0;
    background: radial-gradient(circle, rgba(255, 255, 180, 0.95) 0%, rgba(255, 255, 180, 0.4) 40%, rgba(255, 255, 180, 0) 75%);
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/gsap@3/dist/gsap.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    if (sessionStorage.getItem("splash_shown")) {
        document.getElementById("magic-splash-screen").style.display = "none";
        return;
    }
    
    // Set flag
    sessionStorage.setItem("splash_shown", "true");

    const splash = document.getElementById("magic-splash-screen");
    const bag = document.querySelector("#magic-splash-screen .bag-container");

    gsap.to("#magic-splash-screen .bag-container", {
        y: -12,
        duration: 2,
        repeat: -1,
        yoyo: true,
        ease: "sine.inOut"
    });

    function openBag() {
        if (bag.classList.contains("opened")) return;
        bag.classList.add("opened");

        const tl = gsap.timeline();

        tl.to("#magic-splash-screen .bag-container", { scale: 1.03, duration: 0.18 });
        tl.to("#magic-splash-screen .bag-container", { x: -6, duration: 0.05, repeat: 5, yoyo: true, ease: "power1.inOut" });

        tl.to("#magic-splash-screen .flap-wrapper", {
            rotateX: -160,
            y: -1,
            duration: 0.75,
            ease: "power3.out",
            onUpdate: function () {
                const currentRotation = gsap.getProperty("#magic-splash-screen .flap-wrapper", "rotateX");
                if (currentRotation < -80) {
                    gsap.to("#magic-splash-screen .flap-front", { opacity: 0, duration: 0.12 });
                    gsap.to("#magic-splash-screen .flap-back", { opacity: 1, duration: 0.12 });
                }
            }
        }, "-=0.15");

        tl.to("#magic-splash-screen .lock-wrap", { rotation: 0, y: -1, duration: 0.35, ease: "back.out(2)" }, "-=0.5");
        tl.to("#magic-splash-screen .flap-wrapper", { rotateX: -140, duration: 0.22, ease: "power2.out" });
        tl.to("#magic-splash-screen .inside-shadow", { opacity: 1, duration: 0.3 }, "-=0.35");
        tl.to("#magic-splash-screen .glow", { opacity: 1, scale: 1.5, duration: 0.5, ease: "power2.out" }, "-=0.45");

        const bagRect = bag.getBoundingClientRect();
        gsap.set("#magic-splash-screen .book", {
            x: bagRect.left + bagRect.width / 2,
            y: bagRect.top + bagRect.height * 0.42,
            xPercent: -50, yPercent: -50,
            scale: 0.05
        });

        const startX = bagRect.left + bagRect.width * 0.52;
        const startY = bagRect.top + bagRect.height * 0.48;

        gsap.set("#magic-splash-screen .book", {
            x: startX, y: startY,
            xPercent: -50, yPercent: -50,
            scale: 0.05, rotation: -8, opacity: 0, zIndex: 10
        });

        tl.set("#magic-splash-screen .flap-wrapper", { zIndex: 1 });
        tl.to("#magic-splash-screen .book", { opacity: 1, y: startY - 140, scale: 1, duration: 0.9, ease: "power3.out" }, "-=0.25");
        tl.to("#magic-splash-screen .glow", { scale: 7, opacity: 1, duration: 0.8, ease: "power2.out" }, "-=0.7");

        tl.to("#magic-splash-screen .book", {
            x: window.innerWidth / 2,
            y: window.innerHeight / 2,
            scale: 22,
            rotation: 0,
            borderRadius: 0,
            zIndex: 99999,
            duration: 1.6,
            ease: "power4.inOut"
        }, "-=0.25");

        tl.to("#magic-splash-screen .book", { filter: "blur(14px) brightness(1.2)", duration: 0.45, ease: "power2.out" });

        // After animation completes, fade out splash
        tl.then(() => {
            setTimeout(() => {
                splash.style.opacity = '0';
                setTimeout(() => {
                    splash.style.display = 'none';
                }, 500);
            }, 100); // quick fade out after the book takes over
        });
    }

    setTimeout(() => { openBag(); }, 800);
});

</script>
@endpush
