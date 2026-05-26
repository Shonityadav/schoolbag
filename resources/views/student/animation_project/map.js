/* =========================
   ELEMENTS
========================= */

const svg =
    document.querySelector(".road-svg");

const path =
    document.querySelector("#road-base");

const progressPath =
    document.querySelector("#road-progress");

const totalLength =
    path.getTotalLength();

progressPath.style.strokeDasharray =
    totalLength;

progressPath.style.strokeDashoffset =
    totalLength;

const levelsContainer =
    document.querySelector(".levels");
const decorations =          // ← ADD THIS
    document.querySelector(".decorations");

/* =========================
   SETTINGS
========================= */

const totalLevels = 15;

const ROAD_HEIGHT =
    totalLevels * 260;

let completedLevels = 1;
svg.style.height =
    `${ROAD_HEIGHT}px`;

document.querySelector(".map-inner").style.minHeight =
    `${ROAD_HEIGHT}px`;

/* =========================
   RESPONSIVE LEVELS
========================= */

function createLevels() {

    levelsContainer.innerHTML = "";

    const pathLength =
        path.getTotalLength();

    for (let i = 0; i < totalLevels; i++) {

        const level =
            document.createElement("div");

        level.classList.add("level");

        if (i < completedLevels) {

            level.classList.add("active");
        }

        const pinSrc =
            i < completedLevels
                ? "assets2/completed.png"
                : "assets2/pin.png";

        level.innerHTML = `
    <img src="${pinSrc}" class="pin-img">
    <span class="level-number">${i + 1}</span>
`;

        /* =========================
           GET SVG POINT
        ========================= */
        const topPadding = 0;

        const bottomPadding = 260;

        /* usable road */
        const usableLength =
            pathLength - topPadding - bottomPadding;

        /* exact level position */
        const levelLength =
            topPadding +
            (usableLength / (totalLevels - 1)) * i;

        const point =
            path.getPointAtLength(levelLength);
        /* =========================
           SVG -> SCREEN SCALE
        ========================= */

        const svgRect =
            svg.getBoundingClientRect();

        const viewBox =
            svg.viewBox.baseVal;

        const scaleX =
            svgRect.width / viewBox.width;

        const scaleY =
            svgRect.height / viewBox.height;

        const x =
            point.x * scaleX;

        level.style.transform =
            "translate(-50%, -50%)";

        const y =
            point.y * scaleY;

        level.style.left = `${x}px`;

        level.style.top = `${y}px`;

        /* =========================
           CLICK LEVEL
        ========================= */

        level.addEventListener("click", () => {

            completedLevels = i + 1;

            updateProgress();

            level.scrollIntoView({

                behavior: "smooth",

                block: "center"
            });
        });

        levelsContainer.appendChild(level);
    }
}

/* =========================
   UPDATE PROGRESS
========================= */

function updateProgress() {

    const pathLength =
        path.getTotalLength();

    /* =========================
       EXACT POSITION OF LEVEL
    ========================= */

    let targetLength = 0;
    const topPadding = 0;

    const bottomPadding = 260;

    const usableLength =
        pathLength - topPadding - bottomPadding;

    if (completedLevels > 0) {

        targetLength =
            topPadding +
            (usableLength / (totalLevels - 1)) *
            (completedLevels - 1);
    }

    const nodeFix = 48;

    const dashOffset =
        pathLength - targetLength + nodeFix;
    /* =========================
       ANIMATE ROAD
    ========================= */

    gsap.to(progressPath, {

        strokeDashoffset: dashOffset,

        duration: 1.2,

        ease: "power3.inOut"
    });

    /* =========================
       UPDATE LEVEL STATES
    ========================= */

    document
        .querySelectorAll(".level")
        .forEach((level, index) => {

            level.classList.remove(
                "active",
                "current"
            );

            const pin =
                level.querySelector(".pin-img");
            const number =
                level.querySelector(".level-number");

            /* COMPLETED */
            if (index < completedLevels - 1) {

                level.classList.add("active");

                pin.src =
                    "assets2/completed.png";
                number.style.display = "none";
            }

            /* CURRENT */
            else if (index === completedLevels - 1) {

                level.classList.add("current");

                pin.src =
                    "assets2/pin.png";
            }

            /* FUTURE */
            else {

                pin.src =
                    "assets2/pin.png";
            }
        });
    gsap.killTweensOf(".level.current");

    gsap.to(".level.current", {

        y: "-=10",

        duration: 1.2,

        repeat: -1,

        yoyo: true,

        ease: "sine.inOut"
    });
}

/* =========================
   INITIALIZE
========================= */

createLevels();

updateProgress();

/* =========================
   ON RESIZE
========================= */

window.addEventListener("resize", () => {

    createLevels();

    updateProgress();
});

/* =========================
   FLOATING CLOUDS
========================= */

gsap.to(".cloud-1", {

    y: -20,

    duration: 4,

    repeat: -1,

    yoyo: true,

    ease: "sine.inOut"
});

gsap.to(".cloud-2", {

    y: -15,

    duration: 5,

    repeat: -1,

    yoyo: true,

    ease: "sine.inOut"
});

/* =========================
   LEVEL PULSE
========================= */

gsap.to(".level.active", {

    scale: 1.08,

    duration: 1,

    repeat: -1,

    yoyo: true,

    stagger: 0.08,

    ease: "sine.inOut"
});

/* =========================
   MAGIC PARTICLES
========================= */

const particles =
    document.querySelector(".particles");

for (let i = 0; i < 40; i++) {

    const p =
        document.createElement("div");

    p.classList.add("particle");

    p.style.left =
        Math.random() * window.innerWidth + "px";

    p.style.top =
        Math.random() * window.innerHeight + "px";

    particles.appendChild(p);

    gsap.to(p, {

        y: `-=${100 + Math.random() * 150}`,

        x: `+=${Math.random() * 80 - 40}`,

        opacity: 0,

        duration: 4 + Math.random() * 4,

        repeat: -1,

        ease: "none"
    });
}
/* =========================
   DECORATION SYSTEM — PREMIUM
========================= */

const ROAD_HALF_WIDTH = 38;
const SIDE_MARGIN = 6;

// Sizes tuned for a ~400px wide container
// Make them LARGE — they should fill the side space
const decorSizes = {
    "indiagate.png": { w: 150, h: 170 },
    "watchtower.png": { w: 155, h: 190 },
    "tree.png": { w: 95, h: 110 },
    "lamp.png": { w: 55, h: 80 },
    "patch.png": { w: 140, h: 140 },
    "cup.png": { w: 65, h: 65 },
    "tower2.png": { w: 130, h: 155 },
    "car.png": { w: 145, h: 95 },
    "cloud.png": { w: 140, h: 80 },
    "booth.png": { w: 90, h: 115 },
    "tree1.png": { w: 110, h: 125 },
    "lightpatch.png": { w: 140, h: 140 },
};

// Layered layout — each "scene" places 2-3 items at different depths
// depth: "hero"(front/big), "mid", "far"(small/faded, pushed to edge)
const scenes = [
    // Scene A: big hero left + cloud far right
    (roadScX, baseY, cW) => {
        place("watchtower.png", "left", roadScX, baseY, cW, "hero");
        place("cloud.png", "right", roadScX, baseY - 60, cW, "far");
    },
    // Scene B: hero right + small accent left
    (roadScX, baseY, cW) => {
        place("car.png", "right", roadScX, baseY, cW, "hero");
        place("lamp.png", "left", roadScX, baseY + 50, cW, "mid");
    },
    // Scene C: tall building left + tree right
    (roadScX, baseY, cW) => {
        place("indiagate.png", "left", roadScX, baseY, cW, "hero");
        place("tree1.png", "right", roadScX, baseY + 40, cW, "mid");
    },
    // Scene D: booth right + patch left (ground patches add richness)
    (roadScX, baseY, cW) => {
        place("booth.png", "right", roadScX, baseY, cW, "hero");
        place("patch.png", "left", roadScX, baseY + 60, cW, "mid");
    },
    // Scene E: two trees, one each side (forest moment)
    (roadScX, baseY, cW) => {
        place("tree.png", "left", roadScX, baseY, cW, "hero");
        place("tree1.png", "right", roadScX, baseY + 30, cW, "hero");
        place("cup.png", "right", roadScX, baseY - 80, cW, "far");
    },
    // Scene F: patch + cloud filler (breathing space)
    (roadScX, baseY, cW) => {
        place("lightpatch.png", "left", roadScX, baseY, cW, "mid");
        place("cloud.png", "right", roadScX, baseY - 40, cW, "far");
    },
];

function getRoadXatY(targetVbY) {
    const steps = 300;
    const total = path.getTotalLength();
    let best = { x: 200, dist: Infinity };
    for (let i = 0; i <= steps; i++) {
        const pt = path.getPointAtLength((i / steps) * total);
        const d = Math.abs(pt.y - targetVbY);
        if (d < best.dist) best = { x: pt.x, dist: d };
    }
    return best.x;
}

function place(img, side, roadScX, centerY, cW, depth) {
    const size = decorSizes[img] || { w: 100, h: 120 };

    // depth controls size multiplier and opacity
    const scale = depth === "hero" ? 1.0 : depth === "mid" ? 0.82 : 0.62;
    const opacity = depth === "hero" ? 1.0 : depth === "mid" ? 0.88 : 0.60;
    const blur = depth === "far" ? "blur(1.5px)" : "none";

    const w = Math.round(size.w * scale);
    const h = Math.round(size.h * scale);

    let left;
    if (side === "left") {
        left = roadScX - ROAD_HALF_WIDTH - SIDE_MARGIN - w;
        // push "far" items even further to the edge
        if (depth === "far") left -= 8;
        if (depth === "hero") left += 4;   // slightly closer for drama
        if (left < -20) return;            // allow slight bleed off left edge
    } else {
        left = roadScX + ROAD_HALF_WIDTH + SIDE_MARGIN;
        if (depth === "far") left += 8;
        if (depth === "hero") left -= 4;
        if (left + w > cW + 20) return;    // allow slight bleed off right edge
    }

    const el = document.createElement("img");
    el.src = `assets2/${img}`;
    el.classList.add("decor");
    el.style.cssText = `
    position: absolute;
    width: ${w}px;
    top: ${centerY - h / 2}px;
    left: ${left}px;
    z-index: ${depth === "hero" ? 4 : depth === "mid" ? 3 : 2};
    opacity: ${opacity};
    filter: ${blur} drop-shadow(0 ${depth === "hero" ? 14 : 8}px ${depth === "hero" ? 20 : 10}px rgba(0,0,0,${depth === "hero" ? 0.18 : 0.10}));
    transform-origin: bottom center;
  `;
    decorations.appendChild(el);

    // Gentle float — hero items move more, far items barely move
    const floatAmt = depth === "hero" ? 10 : depth === "mid" ? 6 : 3;
    gsap.to(el, {
        y: `-=${floatAmt}`,
        duration: 3 + Math.random() * 2.5,
        repeat: -1,
        yoyo: true,
        ease: "sine.inOut",
        delay: Math.random() * 3,
    });
}

function buildDecorations() {
    decorations.innerHTML = "";

    const svgRect = svg.getBoundingClientRect();
    const containerWidth = svgRect.width || 400;
    const scaleX = containerWidth / svg.viewBox.baseVal.width;
    const scaleY = svgRect.height / svg.viewBox.baseVal.height;

    // Tighter spacing = denser, richer map
    const SLOT_HEIGHT = 230;
    const SLOTS = Math.floor(ROAD_HEIGHT / SLOT_HEIGHT);

    for (let s = 0; s < SLOTS; s++) {
        // slight vertical jitter so rows don't look grid-like
        const centerY = 140 + s * SLOT_HEIGHT + (Math.random() * 50 - 25);
        if (centerY > ROAD_HEIGHT - 150) break;

        // Road x in viewBox coords → screen px
        const vbY = centerY / scaleY;
        const roadVbX = getRoadXatY(vbY);
        const roadScX = roadVbX * scaleX;

        // Pick a scene, cycling so we get good variety
        const scene = scenes[s % scenes.length];
        scene(roadScX, centerY, containerWidth);
    }
}

buildDecorations();
window.addEventListener("resize", buildDecorations);