{{-- ====================================================
     CHAPTER JOURNEY MAP
     Combined from map.html + map.css + map.js
     Images: uploads/images/maps/
====================================================--}}

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/gsap@3/dist/gsap.min.js"></script>
<style>
/* ========================= map.css ========================= */
* { margin: 0; padding: 0; box-sizing: border-box; }

#chapter-journey-map {
    position: fixed;
    inset: 0;
    z-index: 8000;
    width: 100%;
    height: 100vh;
    overflow: hidden;
    font-family: sans-serif;
    background: #d2e3f9;
    transition: opacity 0.5s ease;
}
#chapter-journey-map.hiding {
    opacity: 0;
    pointer-events: none;
}

#cjm-map-container {
    position: relative;
    width: 100%;
    height: 100vh;
    overflow-y: auto;
    overflow-x: hidden;
    background: linear-gradient(to bottom, #d2e3f9, #87b1e9);
}
#cjm-map-container .top-fade,
#cjm-map-container .bottom-fade {
    position: fixed;
    left: 0;
    width: 100%;
    height: 70px;
    z-index: 50;
    pointer-events: none;
}
#cjm-map-container .top-fade {
    top: 0;
    background: linear-gradient(to bottom, #d2e3f9, transparent);
}
#cjm-map-container .bottom-fade {
    bottom: 0;
    background: linear-gradient(to top, #d2e3f9, transparent);
}

/* MAP INNER */
.cjm-map-inner {
    position: relative;
    width: 100%;
    min-height: 3000px;
    overflow-x: hidden;
}
.cjm-map-bg {
    position: fixed;
    inset: 0;
    z-index: -10;
    background: radial-gradient(circle at top, #f8fcff 0%, #d7e8ff 100%);
}

/* SVG ROAD */
.cjm-road-svg {
    position: absolute;
    width: 100%;
    left: 0; top: 0;
    z-index: 1;
    overflow: visible;
    min-width: 100%;
}
#cjm-road-base {
    fill: none;
    stroke: #b4cef5;
    stroke-width: 70;
    stroke-linecap: round;
    filter: drop-shadow(0 10px 18px rgba(0,0,0,0.08));
}
#cjm-road-progress {
    fill: none;
    stroke: #4b9cff;
    stroke-width: 60;
    stroke-linecap: round;
    filter: drop-shadow(0 0 10px #4b9cff) drop-shadow(0 0 25px rgba(75,156,255,0.6));
    stroke-dasharray: 5000;
    stroke-dashoffset: 5000;
}

/* LEVELS */
.cjm-levels {
    position: absolute;
    inset: 0;
    z-index: 5;
}
.cjm-level {
    position: absolute;
    width: 85px; height: 85px;
    transform: translate(-50%, -50%);
    display: flex; justify-content: center; align-items: center;
    cursor: pointer;
    z-index: 10;
    transition: transform 0.25s, filter 0.25s, opacity 0.25s;
    filter: drop-shadow(0 6px 10px rgba(0,0,0,0.12));
}
.cjm-level:hover {
    transform: translate(-50%, -50%) scale(1.08);
}
.cjm-level.cjm-active {
    opacity: 0.45;
    transform: translate(-50%, -50%) scale(0.92);
    filter: grayscale(0.1) brightness(0.95);
}
.cjm-level.cjm-current {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1.18);
    z-index: 30;
    filter: drop-shadow(0 0 10px rgba(255,255,255,0.8))
            drop-shadow(0 0 20px rgba(255,220,90,0.9))
            drop-shadow(0 0 35px rgba(255,220,90,0.7));
}
.cjm-pin-img {
    width: 100%; height: 100%;
    object-fit: contain;
    user-select: none; pointer-events: none;
}
.cjm-level-number {
    position: absolute;
    width: 44px; height: 44px;
    left: 52%; top: 36%;
    transform: translate(-50%, -50%);
    display: flex; justify-content: center; align-items: center;
    font-size: 22px; font-weight: 700;
    color: #357de8;
    text-shadow: 0 2px 4px rgba(0,0,0,0.15);
    pointer-events: none;
}

/* DECORATIONS */
.decorations {
    position: absolute;
    inset: 0; width: 100%; height: 100%;
    z-index: 2; pointer-events: none;
    overflow: visible;
}
.decor {
    position: absolute;
    z-index: 0; pointer-events: none; opacity: 0.9;
    filter: drop-shadow(0 8px 14px rgba(0,0,0,0.08));
    transition: 0.3s;
    animation: decorFloat 5s ease-in-out infinite;
}
@keyframes decorFloat {
    0%   { transform: translateY(0px); }
    50%  { transform: translateY(-8px); }
    100% { transform: translateY(0px); }
}

/* PARTICLES */
.cjm-particles {
    position: fixed; inset: 0; z-index: 0;
    pointer-events: none; overflow: hidden;
}
.cjm-particle {
    position: absolute;
    width: 5px; height: 5px;
    border-radius: 50%;
    background: rgba(255,255,255,0.9);
    filter: blur(1px);
</style>
@endpush

{{-- ── HTML (from map.html) ── --}}
<div id="chapter-journey-map" style="{{ isset($requestedChapterId) && $requestedChapterId ? 'display: none; opacity: 0;' : '' }}">
    <a href="{{ route('student.courses.index') }}" style="position: fixed; top: 20px; left: 10px; z-index: 9000; transition: transform 0.15s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'" title="Back to Subjects">
        <img src="{{ asset('uploads/images/buttons/Previous button.png') }}" alt="Back" style="height: 52px; object-fit: contain;" fetchpriority="high" loading="eager" decoding="async">
    </a>
    <div id="cjm-map-container">
        <div class="top-fade"></div>
        <div class="bottom-fade"></div>
        <div class="cjm-map-inner">
            <div class="cjm-map-bg"></div>
            <div class="cjm-particles"></div>
            <div class="decorations" id="cjm-decorations"></div>

            <svg class="cjm-road-svg" viewBox="0 0 400 2800" preserveAspectRatio="none">
                <path id="cjm-road-base" d="
                    M 200 160
                    C 90 320, 320 500, 210 760
                    C 100 980, 330 1180, 190 1450
                    C 70 1700, 310 1950, 220 2200
                    C 180 2400, 260 2700, 220 2950
                "/>
                <path id="cjm-road-progress" d="
                    M 200 160
                    C 90 320, 320 500, 210 760
                    C 100 980, 330 1180, 190 1450
                    C 70 1700, 310 1950, 220 2200
                    C 180 2400, 260 2700, 220 2950
                "/>
            </svg>

            <div class="cjm-levels" id="cjm-levels"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
/* ========================= map.js (adapted) ========================= */

const ASSET_BASE = "{{ asset('uploads/images/map') }}/";

// ── Laravel data ──────────────────────────────────────
const chaptersData    = @json($chaptersData);
const activeChapterId = {{ $activeChapter ? $activeChapter->id : 'null' }};
const courseId        = {{ $course->id }};

// Compute completed count
let completedLevels = 0;
chaptersData.forEach(d => { if (d.completed) completedLevels++; });
// current = first unlocked, not completed
const totalLevels = chaptersData.length;

// ── Elements ─────────────────────────────────────────
const svg            = document.querySelector(".cjm-road-svg");
const path           = document.querySelector("#cjm-road-base");
const progressPath   = document.querySelector("#cjm-road-progress");
const levelsContainer= document.querySelector("#cjm-levels");
const decorations    = document.querySelector("#cjm-decorations");

const totalLength    = path.getTotalLength();
progressPath.style.strokeDasharray  = totalLength;
progressPath.style.strokeDashoffset = totalLength;

// ── Settings ─────────────────────────────────────────
const ROAD_HEIGHT = totalLevels * 260;
svg.style.height  = `${ROAD_HEIGHT}px`;
document.querySelector(".cjm-map-inner").style.minHeight = `${ROAD_HEIGHT}px`;

// ── Create Levels ─────────────────────────────────────
function createLevels() {
    levelsContainer.innerHTML = "";
    const pathLength = path.getTotalLength();
    const topPadding    = 0;
    const bottomPadding = 250;
    const usableLength  = pathLength - topPadding - bottomPadding;

    chaptersData.forEach((data, i) => {
        const chapter   = data.chapter;
        const unlocked  = data.unlocked;
        const completed = data.completed;
        const isCurrent = chapter.id === activeChapterId;

        const level = document.createElement("div");
        level.classList.add("cjm-level");

        if (i < completedLevels)  level.classList.add("cjm-active");
        if (isCurrent)            level.classList.add("cjm-current");

        const pinSrc = i < completedLevels
            ? ASSET_BASE + "completed.png"
            : ASSET_BASE + "pin.png";

        level.innerHTML = `
            <img src="${pinSrc}" class="cjm-pin-img" alt="Chapter ${i+1}" fetchpriority="high" loading="eager" decoding="async">
            <span class="cjm-level-number">${i < completedLevels ? '' : (i + 1)}</span>
        `;

        // Position on road
        const levelLength = topPadding + (usableLength / Math.max(totalLevels - 1, 1)) * i;
        const point = path.getPointAtLength(levelLength);
        const svgRect = svg.getBoundingClientRect();
        const viewBox = svg.viewBox.baseVal;
        const scaleX  = svgRect.width  / viewBox.width;
        const scaleY  = svgRect.height / viewBox.height;

        level.style.transform = "translate(-50%, -50%)";
        level.style.left = `${point.x * scaleX}px`;
        level.style.top  = `${point.y * scaleY}px`;

        // Click
        level.addEventListener("click", () => {
            if (!unlocked && !completed) return;
            if (chapter.id === activeChapterId) {
                closeCjm();
            } else {
                const url = `/student/subjects/${courseId}?chapter_id=${chapter.id}`;
                closeCjm(() => { window.location.href = url; });
            }
        });

        levelsContainer.appendChild(level);
    });
}

// ── Update Progress Road ──────────────────────────────
function updateProgress() {
    const pathLength    = path.getTotalLength();
    const topPadding    = 0;
    const bottomPadding = 250;
    const usableLength  = pathLength - topPadding - bottomPadding;

    let targetLength = 0;
    if (completedLevels > 0) {
        targetLength = topPadding + (usableLength / Math.max(totalLevels - 1, 1)) * completedLevels;
    }

    const nodeFix    = 48;
    const dashOffset = pathLength - targetLength + nodeFix;

    gsap.to(progressPath, { strokeDashoffset: dashOffset, duration: 1.2, ease: "power3.inOut" });

    document.querySelectorAll(".cjm-level").forEach((level, index) => {
        level.classList.remove("cjm-active", "cjm-current");
        const pin    = level.querySelector(".cjm-pin-img");
        const number = level.querySelector(".cjm-level-number");

        if (index < completedLevels) {
            level.classList.add("cjm-active");
            pin.src = ASSET_BASE + "completed.png";
            if (number) number.style.display = "none";
        } else if (index === completedLevels) {
            level.classList.add("cjm-current");
            pin.src = ASSET_BASE + "pin.png";
            if (number) number.style.display = "flex";
            if (number) number.textContent = (index + 1);
        } else {
            pin.src = ASSET_BASE + "pin.png";
            if (number) number.style.display = "flex";
            if (number) number.textContent = (index + 1);
        }
    });

    gsap.killTweensOf(".cjm-level.cjm-current");
    gsap.to(".cjm-level.cjm-current", { y: "-=10", duration: 1.2, repeat: -1, yoyo: true, ease: "sine.inOut" });
}

// ── Particles ─────────────────────────────────────────
const particlesEl = document.querySelector(".cjm-particles");
for (let i = 0; i < 40; i++) {
    const p = document.createElement("div");
    p.classList.add("cjm-particle");
    p.style.left = Math.random() * window.innerWidth + "px";
    p.style.top  = Math.random() * window.innerHeight + "px";
    particlesEl.appendChild(p);
    gsap.to(p, {
        y: `-=${100 + Math.random() * 150}`,
        x: `+=${Math.random() * 80 - 40}`,
        opacity: 0, duration: 4 + Math.random() * 4, repeat: -1, ease: "none"
    });
}

// ── Decorations ───────────────────────────────────────
const ROAD_HALF_WIDTH = 38;
const SIDE_MARGIN     = 6;
const decorSizes = {
    "indiagate.png":  { w: 150, h: 170 },
    "watchtower.png": { w: 155, h: 190 },
    "tree.png":       { w: 95,  h: 110 },
    "lamp.png":       { w: 55,  h: 80  },
    "patch.png":      { w: 140, h: 140 },
    "cup.png":        { w: 65,  h: 65  },
    "tower2.png":     { w: 130, h: 155 },
    "car.png":        { w: 145, h: 95  },
    "cloud.png":      { w: 140, h: 80  },
    "booth.png":      { w: 90,  h: 115 },
    "tree1.png":      { w: 110, h: 125 },
    "lightpatch.png": { w: 140, h: 140 },
};

const scenes = [
    (roadScX, baseY, cW) => { place("watchtower.png","left",roadScX,baseY,cW,"hero"); place("cloud.png","right",roadScX,baseY-60,cW,"far"); },
    (roadScX, baseY, cW) => { place("car.png","right",roadScX,baseY,cW,"hero"); place("lamp.png","left",roadScX,baseY+50,cW,"mid"); },
    (roadScX, baseY, cW) => { place("indiagate.png","left",roadScX,baseY,cW,"hero"); place("tree1.png","right",roadScX,baseY+40,cW,"mid"); },
    (roadScX, baseY, cW) => { place("booth.png","right",roadScX,baseY,cW,"hero"); place("patch.png","left",roadScX,baseY+60,cW,"mid"); },
    (roadScX, baseY, cW) => { place("tree.png","left",roadScX,baseY,cW,"hero"); place("tree1.png","right",roadScX,baseY+30,cW,"hero"); place("cup.png","right",roadScX,baseY-80,cW,"far"); },
    (roadScX, baseY, cW) => { place("lightpatch.png","left",roadScX,baseY,cW,"mid"); place("cloud.png","right",roadScX,baseY-40,cW,"far"); },
];

function getRoadXatY(targetVbY) {
    const steps = 300, total = path.getTotalLength();
    let best = { x: 200, dist: Infinity };
    for (let i = 0; i <= steps; i++) {
        const pt = path.getPointAtLength((i / steps) * total);
        const d  = Math.abs(pt.y - targetVbY);
        if (d < best.dist) best = { x: pt.x, dist: d };
    }
    return best.x;
}

function place(img, side, roadScX, centerY, cW, depth) {
    const size  = decorSizes[img] || { w: 100, h: 120 };
    const scale = depth === "hero" ? 1.0 : depth === "mid" ? 0.82 : 0.62;
    const opacity = depth === "hero" ? 1.0 : depth === "mid" ? 0.88 : 0.60;
    const blur    = depth === "far"  ? "blur(1.5px)" : "none";
    const w = Math.round(size.w * scale);
    const h = Math.round(size.h * scale);

    let left;
    if (side === "left") {
        left = roadScX - ROAD_HALF_WIDTH - SIDE_MARGIN - w;
        if (depth === "far")  left -= 8;
        if (depth === "hero") left += 4;
        if (left < -20) return;
    } else {
        left = roadScX + ROAD_HALF_WIDTH + SIDE_MARGIN;
        if (depth === "far")  left += 8;
        if (depth === "hero") left -= 4;
        if (left + w > cW + 20) return;
    }

    const el = document.createElement("img");
    el.src = ASSET_BASE + img;
    el.classList.add("decor");
    el.style.cssText = `
        position:absolute; width:${w}px; top:${centerY - h/2}px; left:${left}px;
        z-index:${depth === "hero" ? 4 : depth === "mid" ? 3 : 2};
        opacity:${opacity}; filter:${blur} drop-shadow(0 ${depth==="hero"?14:8}px ${depth==="hero"?20:10}px rgba(0,0,0,${depth==="hero"?0.18:0.10}));
        transform-origin:bottom center;
    `;
    decorations.appendChild(el);
    gsap.to(el, { y: `-=${depth==="hero"?10:depth==="mid"?6:3}`, duration: 3 + Math.random() * 2.5, repeat: -1, yoyo: true, ease: "sine.inOut", delay: Math.random() * 3 });
}

function buildDecorations() {
    decorations.innerHTML = "";
    const svgRect = svg.getBoundingClientRect();
    const cW      = svgRect.width || 400;
    const scaleX  = cW / svg.viewBox.baseVal.width;
    const scaleY  = svgRect.height / svg.viewBox.baseVal.height;
    const SLOT_H  = 230;
    const SLOTS   = Math.floor(ROAD_HEIGHT / SLOT_H);
    for (let s = 0; s < SLOTS; s++) {
        const centerY = 140 + s * SLOT_H + (Math.random() * 50 - 25);
        if (centerY > ROAD_HEIGHT - 150) break;
        const vbY    = centerY / scaleY;
        const roadVbX= getRoadXatY(vbY);
        const roadScX= roadVbX * scaleX;
        scenes[s % scenes.length](roadScX, centerY, cW);
    }
}

// ── GSAP animations ────────────────────────────────────
gsap.to(".cloud-1", { y: -20, duration: 4, repeat: -1, yoyo: true, ease: "sine.inOut" });
gsap.to(".cloud-2", { y: -15, duration: 5, repeat: -1, yoyo: true, ease: "sine.inOut" });
let activeLevelTween = null;
function initMapLayout() {
    createLevels();
    updateProgress();
    buildDecorations();
    
    if (activeLevelTween) activeLevelTween.kill();
    activeLevelTween = gsap.to(".cjm-level.cjm-active", { scale: 1.08, duration: 1, repeat: -1, yoyo: true, stagger: 0.08, ease: "sine.inOut" });
}

// ── Init ──────────────────────────────────────────────
if (document.getElementById("chapter-journey-map").style.display !== "none") {
    initMapLayout();
}

window.addEventListener("resize", () => { 
    if (document.getElementById("chapter-journey-map").style.display !== "none") {
        initMapLayout();
    }
});

// ── Auto scroll to current ────────────────────────────
setTimeout(() => {
    const cur = document.querySelector(".cjm-level.cjm-current");
    if (cur) {
        const scrollEl = document.getElementById("cjm-map-container");
        const y = parseFloat(cur.style.top) - window.innerHeight * 0.4;
        scrollEl.scrollTo({ top: Math.max(0, y), behavior: "smooth" });
    }
}, 800);

// ── Close / Open ──────────────────────────────────────
window.closeCjm = function(callback) {
    gsap.to("#chapter-journey-map", {
        opacity: 0, duration: 0.5, ease: "power2.inOut",
        onComplete: () => {
            document.getElementById("chapter-journey-map").style.display = "none";
            if (callback) callback();
        }
    });
};

window.openCjm = function() {
    const map = document.getElementById("chapter-journey-map");
    map.style.display = "block";
    
    // Recalculate dimensions now that it is visible
    initMapLayout();

    gsap.to(map, {
        opacity: 1, duration: 0.5, ease: "power2.inOut"
    });
};

})();
</script>
@endpush
