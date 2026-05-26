/* =========================
   ELEMENTS
========================= */

const bag = document.querySelector(".bag-container");
const button = document.querySelector(".open-btn");

/* =========================
   FLOATING IDLE
========================= */

gsap.to(".bag-container", {

    y: -12,

    duration: 2,

    repeat: -1,

    yoyo: true,

    ease: "sine.inOut"

});

/* =========================
   OPEN BAG
========================= */

function openBag() {

    if (bag.classList.contains("opened")) {
        return;
    }

    bag.classList.add("opened");

    const tl = gsap.timeline();

    /* =========================
       BAG POP
    ========================= */

    tl.to(".bag-container", {

        scale: 1.03,

        duration: 0.18

    });

    /* =========================
       SHAKE
    ========================= */

    tl.to(".bag-container", {

        x: -6,

        duration: 0.05,

        repeat: 5,

        yoyo: true,

        ease: "power1.inOut"

    });

    tl.to(".flap-wrapper", {

        rotateX: -160,

        y: -1,

        duration: 0.75,

        ease: "power3.out",

        onUpdate: function () {

            const currentRotation =
                gsap.getProperty(".flap-wrapper", "rotateX");

            // swap around halfway

            if (currentRotation < -80) {

                gsap.to(".flap-front", {

                    opacity: 0,

                    duration: 0.12

                });

                gsap.to(".flap-back", {

                    opacity: 1,

                    duration: 0.12

                });

            }

        }

    }, "-=0.15");
    /* =========================
       FLAP OPEN
    ========================= */

    tl.to(".lock-wrap", {

        rotation: 0,

        y: -1,

        duration: 0.35,

        ease: "back.out(2)"

    }, "-=0.5");

    tl.to(".flap-wrapper", {

        rotateX: -140,

        duration: 0.22,

        ease: "power2.out"

    });
    /* =========================
       INSIDE SHADOW
    ========================= */

    tl.to(".inside-shadow", {

        opacity: 1,

        duration: 0.3

    }, "-=0.35");

    /* =========================
       GLOW
    ========================= */

    tl.to(".glow", {

        opacity: 1,

        scale: 1.5,

        duration: 0.5,

        ease: "power2.out"

    }, "-=0.45");

    const bagRect =
        bag.getBoundingClientRect();

    gsap.set(".book", {

        x: bagRect.left + bagRect.width / 2,

        y: bagRect.top + bagRect.height * 0.42,

        xPercent: -50,
        yPercent: -50,

        scale: 0.05

    });
    
    /* =========================
   BOOK START POSITION
========================= */

    const startX =
        bagRect.left + bagRect.width * 0.52;

    const startY =
        bagRect.top + bagRect.height * 0.48;

    /* =========================
       PLACE BOOK INSIDE BAG
    ========================= */

    gsap.set(".book", {

        x: startX,
        y: startY,

        xPercent: -50,
        yPercent: -50,

        scale: 0.05,

        rotation: -8,

        opacity: 0,

        zIndex: 10

    });
    tl.set(".flap-wrapper", {

        zIndex: 1

    });

    /* =========================
       BOOK EMERGES FROM BAG
    ========================= */

    tl.to(".book", {

        opacity: 1,

        y: startY - 140,

        scale: 1,

        duration: 0.9,

        ease: "power3.out"

    }, "-=0.25");

    /* =========================
       BIG MAGIC GLOW
    ========================= */

    tl.to(".glow", {

        scale: 7,

        opacity: 1,

        duration: 0.8,

        ease: "power2.out"

    }, "-=0.7");

    /* =========================
   BOOK TAKES OVER SCREEN
========================= */

tl.to(".book", {

    x: window.innerWidth / 2,
    y: window.innerHeight / 2,

    scale: 22,

    rotation: 0,

    borderRadius: 0,

    zIndex: 99999,

    duration: 1.6,

    ease: "power4.inOut"

}, "-=0.25");

/* =========================
   BOOK BLURS
========================= */

tl.to(".book", {

    filter:
        "blur(14px) brightness(1.2)",

    duration: 0.45,

    ease: "power2.out"

});

/* =========================
   GLASS BREAK FLASH
========================= */

tl.to(".white-flash", {

    opacity: 1,

    duration: 0.15,

    ease: "power2.out"

});

tl.to(".white-flash", {

    opacity: 1,

    duration: 0.4

});

    /* =========================
       CREATE MAGIC STARS
    ========================= */

    const particlesContainer =
        document.querySelector(".particles");

    /* CLEAR OLD PARTICLES */

    particlesContainer.innerHTML = "";

    /* MORE PARTICLES */

    for (let i = 0; i < 220; i++) {

        const dot =
            document.createElement("div");

        dot.classList.add("particle");

        particlesContainer.appendChild(dot);

        gsap.set(dot, {

            x:
                Math.random() *
                window.innerWidth,

            y:
                Math.random() *
                window.innerHeight,

            scale:
                Math.random() * 1.4 + 0.3
        });
    }

    const particles =
        document.querySelectorAll(".particle");

    /* =========================
       PARTICLES APPEAR
    ========================= */

    particles.forEach((p, i) => {

        tl.to(p, {

            opacity: 1,

            duration: 0.15

        }, `-=${0.25 - i * 0.001}`);

    });

    /* =========================
   FLOATING MAGIC
========================= */

    particles.forEach((p) => {

        const anim = gsap.to(p, {

            x:
                `+=${Math.random() * 300 - 150}`,

            y:
                `+=${Math.random() * 300 - 150}`,

            duration:
                Math.random() * 4 + 2,

            repeat: -1,

            yoyo: true,

            ease: "sine.inOut"

        });

        /* SAVE FLOAT ANIMATION */

        p.floatAnim = anim;

    });
    /* =========================
       MAGIC HOLD
    ========================= */

    tl.to({}, { duration: 1.8 });
    /* =========================
   STOP FLOATING
========================= */

    particles.forEach((p) => {

        if (p.floatAnim) {

            p.floatAnim.kill();

        }

    });

    /* =========================
       PARTICLES FORM LOADER
    ========================= */

    particles.forEach((p, i) => {

        const angle =
            (Math.PI * 2 * i) / particles.length;

        const radius = 38;

        const targetX =
            window.innerWidth / 2 +
            Math.cos(angle) * radius;

        const targetY =
            window.innerHeight / 2 +
            Math.sin(angle) * radius;

        tl.to(p, {

            x: targetX,

            y: targetY,

            scale: 1.2,

            opacity: 1,

            duration: 1.1,

            ease: "power3.inOut"

        }, "-=1");

    });

    /* =========================
       HIDE SOME PARTICLES
    ========================= */

    particles.forEach((p, i) => {

        if (i > 60) {

            tl.to(p, {

                opacity: 0,

                duration: 0.5

            }, "-=0.9");

        }

    });

    /* =========================
       LOADER APPEARS
    ========================= */

    tl.to(".loader-wrap", {

        opacity: 1,

        scale: 1,

        duration: 0.35,

        ease: "back.out(2)"

    }, "-=0.7");

    /* =========================
       SPIN LOADER
    ========================= */

    gsap.to(".loader", {

        rotation: 360,

        repeat: -1,

        ease: "none",

        duration: 1

    });
    /* =========================
       SCREEN BRIGHTENS
    ========================= */

    tl.to("body", {

        filter: "brightness(1.4)",

        duration: 0.5

    }, "-=0.8");

    /* =========================
       FINAL SCALE
    ========================= */

    tl.to(".bag-container", {

        scale: 1.05,

        duration: 0.4

    }, "-=0.3");

}

/* =========================
   AUTO START SPLASH
========================= */

window.addEventListener("load", () => {

    // small cinematic delay

    setTimeout(() => {

        openBag();

    }, 800);

});