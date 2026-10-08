(function() {
    // ----------------------------------------------------
    // MASCOT ENGINE - Core System
    // ----------------------------------------------------
    document.addEventListener('DOMContentLoaded', function() {
        if(document.getElementById('schoolbag-mascot-widget')) return;

        let currentAvatar = localStorage.getItem('student_avatar_index') || 1;
        const path = window.location.pathname.toLowerCase();

        // Do not load the mascot on stage activities, lessons, or inside the flipbook reader
        if (path.includes('/stage') || path.includes('/lesson') || path.match(/\/ebooks\/\d+$/)) {
            return;
        }

        // 1. Inject HTML Structure (Avatar Only)
        const widgetHTML = `
            <div id="schoolbag-mascot-widget" style="position: fixed; width: 130px; height: 180px; z-index: 9999; cursor: pointer; -webkit-tap-highlight-color: transparent; overflow: visible; touch-action: none;">
                <button id="mascot-voice-toggle" style="position: absolute; top: -10px; right: -10px; width: 32px; height: 32px; background: white; border: 2px solid #E8E2D9; border-radius: 50%; z-index: 15; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); padding: 0;">
                    <svg id="icon-voice-on" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#5E4D3B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M15.54 8.46a5 5 0 0 1 0 7.07"></path><path d="M19.07 4.93a10 10 0 0 1 0 14.14"></path></svg>
                    <svg id="icon-voice-off" style="display: none;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#5E4D3B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><line x1="23" y1="9" x2="17" y2="15"></line><line x1="17" y1="9" x2="23" y2="15"></line></svg>
                </button>
                <div class="thought-bubble" style="position: absolute; background: white; padding: 8px 12px; border-radius: 12px; font-size: 14px; font-weight: bold; color: #5E4D3B; transition: all 0.2s; opacity: 0; pointer-events: none; white-space: normal; width: max-content; max-width: 220px; text-align: center; line-height: 1.2; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 10; top: var(--bubble-top, -40px);">Welcome to Schoolbag!</div>
                <div class="schoolbag-mascot-inner" style="width: 100%; height: 100%; position: relative;">
                    
                    <img id="mascot-static" src="/uploads/avatars/avatar2.png" style="width: 120px; height: 150px; object-fit: contain; display: none;" fetchpriority="high">
                    
                    <div id="mascot-skeleton" style="position: relative; width: 120px; height: 150px; display: none;">
                        <img class="part-base" src="/uploads/avatars/avatar1_parts_separated/1.png" style="position:absolute; width:100%; height:100%; top:0; left:0; object-fit: contain; z-index: 1;">
                        <img class="part-arms" src="/uploads/avatars/avatar1_parts_separated/2.png" style="position:absolute; width:45%; height:45%; top:35%; left:28%; object-fit: contain; transition: all 0.2s; z-index: 4;">
                        <img class="part-whites" src="/uploads/avatars/avatar1_parts_separated/9.png" style="position:absolute; width:48%; height:18%; top:20%; left:26%; object-fit: contain; z-index: 2;">
                        <img class="part-pupils" src="/uploads/avatars/avatar1_parts_separated/10.png" style="position:absolute; width:48%; height:18%; top:20%; left:26%; object-fit: contain; transition: margin 0.1s; z-index: 3;">
                        <img class="part-mouth" src="/uploads/avatars/avatar1_parts_separated/14.png" style="position:absolute; width:26%; height:8%; top:31%; left:37%; object-fit: contain; z-index: 2;">
                    </div>
                </div>
            </div>
            <style>
                @keyframes mascotBreathe {
                    0%, 100% { transform: scale(1) translateY(0); }
                    50% { transform: scale(1.02) translateY(-3px); }
                }
                .mascot-breathing { animation: mascotBreathe 3s ease-in-out infinite; }
                .particle {
                    position: fixed; width: 8px; height: 8px; border-radius: 50%; pointer-events: none; z-index: 99999; opacity: 1;
                    animation: particleFade 0.6s ease-out forwards;
                }
                @keyframes particleFade {
                    100% { transform: translate(var(--tx), var(--ty)) scale(0); opacity: 0; }
                }
                .thought-bubble::after {
                    content: ''; position: absolute; bottom: var(--tail-bottom, -8px); top: var(--tail-top, auto); left: var(--tail-x, 20px); width: 10px; height: 10px; background: #fff; border-radius: 50%;
                }
                .thought-bubble::before {
                    content: ''; position: absolute; bottom: var(--tail-bottom2, -14px); top: var(--tail-top2, auto); left: var(--tail-x2, 14px); width: 6px; height: 6px; background: #fff; border-radius: 50%;
                }
            </style>
        `;
        document.body.insertAdjacentHTML('beforeend', widgetHTML);

        // References
        const widget = document.getElementById('schoolbag-mascot-widget');
        const innerRing = widget.querySelector('.schoolbag-mascot-inner');
        const skeleton = document.getElementById('mascot-skeleton');
        const staticAvatar = document.getElementById('mascot-static');
        const bubble = widget.querySelector('.thought-bubble');

        const parts = {
            base: skeleton.querySelector('.part-base'),
            pupils: skeleton.querySelector('.part-pupils'),
            whites: skeleton.querySelector('.part-whites'),
            mouth: skeleton.querySelector('.part-mouth'),
            arms: skeleton.querySelector('.part-arms')
        };

        // Apply breathing
        Object.values(parts).forEach(p => p.classList.add('mascot-breathing'));
        staticAvatar.classList.add('mascot-breathing');

        const updateAvatarDisplay = () => {
            if (currentAvatar == 1) {
                skeleton.style.display = 'block';
                staticAvatar.style.display = 'none';
            } else {
                skeleton.style.display = 'none';
                staticAvatar.style.display = 'block';
                staticAvatar.src = '/uploads/avatars/avatar' + currentAvatar + '.png';
            }
        };
        updateAvatarDisplay();

        const updateBubblePosition = () => {
            // We use the CSS left/top values instead of getBoundingClientRect() 
            // because the bubble itself expands the bounding box, causing a feedback loop!
            const x = parseFloat(widget.style.left) || 0;
            const y = parseFloat(widget.style.top) || 0;
            const isLeftHalf = (x + 65) < (window.innerWidth / 2);
            const isTop = y < 60;

            if (isLeftHalf) {
                bubble.style.left = '50px';
                bubble.style.right = 'auto';
                bubble.style.setProperty('--tail-x', '20px');
                bubble.style.setProperty('--tail-x2', '14px');
            } else {
                bubble.style.left = 'auto';
                bubble.style.right = '50px';
                bubble.style.setProperty('--tail-x', 'calc(100% - 30px)');
                bubble.style.setProperty('--tail-x2', 'calc(100% - 24px)');
            }

            if (isTop) {
                bubble.style.setProperty('--bubble-top', '150px');
                bubble.style.setProperty('--tail-bottom', 'auto');
                bubble.style.setProperty('--tail-bottom2', 'auto');
                bubble.style.setProperty('--tail-top', '-8px');
                bubble.style.setProperty('--tail-top2', '-14px');
            } else {
                bubble.style.setProperty('--bubble-top', '-40px');
                bubble.style.setProperty('--tail-bottom', '-8px');
                bubble.style.setProperty('--tail-bottom2', '-14px');
                bubble.style.setProperty('--tail-top', 'auto');
                bubble.style.setProperty('--tail-top2', 'auto');
            }
        };

        // Global single Audio element for mobile compatibility
        window.mascotAudio = new Audio();
        
        const audioMap = {
            "Welcome to Schoolbag!": "/uploads/audio/def_welcome.m4a",
            "Ready to learn?": "/uploads/audio/def_learn.m4a",
            "You're doing great!": "/uploads/audio/def_great.m4a",
            "Learning is a lifelong journey!": "/uploads/audio/def_journey.m4a",
            "Ready to learn something new?": "/uploads/audio/def_new.m4a",
            "Here you can see your daily tasks.": "/uploads/audio/def_tasks.m4a",
            "Check your attendance and assignments here.": "/uploads/audio/def_check.m4a",
            "Welcome to your Workspace!": "/uploads/audio/def_workspace.m4a",
            
            "Your attendance looks great this month!": "/uploads/audio/att_great.m4a",
            "Always good to be present!": "/uploads/audio/att_present.m4a",
            "Keep your streak going!": "/uploads/audio/att_streak.m4a",
            "Marking your attendance is super important!": "/uploads/audio/att_important.m4a",
            "A new day, a new chance to learn!": "/uploads/audio/att_newday.m4a",
            
            "This shows your fee status for the year.": "/uploads/audio/fee_status.m4a",
            "Your fee records are kept safe here.": "/uploads/audio/fee_records.m4a",
            "Keep track of payments here.": "/uploads/audio/fee_track.m4a",
            "This page shows your payment history.": "/uploads/audio/fee_history.m4a",
            "Keep track of invoices here.": "/uploads/audio/fee_invoices.m4a",
            "All your fee details in one place!": "/uploads/audio/fee_details.m4a",
            
            "Check here to see if teachers assigned homework today!": "/uploads/audio/hw_assigned.m4a",
            "Make sure to finish your assignments on time!": "/uploads/audio/hw_finish.m4a",
            "Homework helps you practice what you learned!": "/uploads/audio/hw_practice.m4a",
            
            "Flip the cards to find matching pairs!": "/uploads/audio/game_flip.m4a",
            "Can you remember where that card was?": "/uploads/audio/game_remember.m4a",
            "Match them all to win!": "/uploads/audio/game_match.m4a",
            "Unscramble the letters to make a word!": "/uploads/audio/game_unscramble.m4a",
            "Word games make you smarter!": "/uploads/audio/game_smart.m4a",
            "Need a hint for this word?": "/uploads/audio/game_hint.m4a",
            "Math puzzles are a great brain workout!": "/uploads/audio/game_math_puzzle.m4a",
            "Can you solve this math problem?": "/uploads/audio/game_math_solve.m4a",
            "Numbers are fun!": "/uploads/audio/game_numbers.m4a",
            "Wanna play a minigame? It's super fun!": "/uploads/audio/game_wanna_play.mp.m4a",
            "Let's test your skills!": "/uploads/audio/game_skills.m4a",
            "Games make learning awesome!": "/uploads/audio/game_awesome.m4a",
            
            "Complete your daily quests to find the treasure!": "/uploads/audio/quest_complete.m4a",
            "There is a map waiting to be explored!": "/uploads/audio/quest_map.m4a",
            "Every quest gives you rewards!": "/uploads/audio/quest_rewards.m4a",
            
            "Good luck with your exams!": "/uploads/audio/exam_luck.m4a",
            "Study hard and you'll do great!": "/uploads/audio/exam_study.m4a",
            "Check your awesome results!": "/uploads/audio/exam_results.m4a",
            
            "Here is your class schedule.": "/uploads/audio/time_schedule.m4a",
            "Be on time for your classes!": "/uploads/audio/time_ontime.m4a",
            "Check what subject is next!": "/uploads/audio/time_next.m4a",
            
            "Look at all these amazing books!": "/uploads/audio/book_amazing.m4a",
            "What are we reading today?": "/uploads/audio/book_reading.m4a",
            "Stories take you on adventures!": "/uploads/audio/book_stories.m4a",
            "Let's read a story together!": "/uploads/audio/book_together.m4a",
            "Books are magical portals!": "/uploads/audio/book_magic.m4a",
            "Pick a subject to start learning!": "/uploads/audio/book_subject.m4a",
            "Reading makes your brain super strong!": "/uploads/audio/book_brain.m4a",
            
            "Wow, look at all your points!": "/uploads/audio/reward_points.m4a",
            "You've earned some amazing badges!": "/uploads/audio/reward_badges.m4a",
            "Keep learning to unlock more rewards!": "/uploads/audio/reward_unlock.m4a",
            
            "This is your profile! You can customize things here.": "/uploads/audio/prof_customize.m4a",
            "Show off your badges and points!": "/uploads/audio/prof_showoff.m4a",
            "Make sure your information is up to date!": "/uploads/audio/prof_update.m4a",
            
            "Worksheets are great for extra practice!": "/uploads/audio/ws_practice.m4a",
            "Let's review what we've done.": "/uploads/audio/ws_review.m4a",
            "Practice makes perfect!": "/uploads/audio/ws_perfect.m4a",
            
            "This page has important rules.": "/uploads/audio/terms_rules.m4a",
            "Privacy is super important to us!": "/uploads/audio/privacy_policy.m4a",
            "Always read the rules to stay safe online!": "/uploads/audio/terms_safe.m4a",
            
            "Welcome to Schoolbag! Please sign in.": "/uploads/audio/auth_signin.m4a",
            "Ready for another great day of learning?": "/uploads/audio/auth_ready.m4a",
            "Log in to see your dashboard!": "/uploads/audio/auth_dash.m4a",

            "Are you leaving? Please don't go!": "/uploads/audio/logout_2.m4a",
            "We were having so much fun...": "/uploads/audio/logout_3.m4a",
            "Aww, leaving so soon?": "/uploads/audio/logout_4.m4a",
            "I'll miss you! Come back soon!": "/uploads/audio/logout_5.m4a",
            "Wait! Are you sure?": "/uploads/audio/logout_2.m4a",
            "Are you leaving?": "/uploads/audio/logout_2.m4a",

            "Let's check out your profile!": "/uploads/audio/personal_info.m4a",
            "Keep your account secure! 🗝️": "/uploads/audio/change_password.m4a",
            "Reading the rules is smart! 📜": "/uploads/audio/terms_and_conditions.m4a",
            "We keep your data super safe! 🛡️": "/uploads/audio/privacy_policy.m4a",
            "We'd love to hear your thoughts! 💭": "/uploads/audio/feedback.m4a",
            "Oh no! Deleting data is forever! ⚠️": "/uploads/audio/data_deletion.m4a",
            
            // --- Feedback & Password ---
            "Make sure your new password is secure!": "/uploads/audio/pwd_secure.m4a",
            "Don't share your password with anyone!": "/uploads/audio/pwd_share.m4a",
            "Keep your account safe!": "/uploads/audio/pwd_safe.m4a",
            "We love hearing your thoughts!": "/uploads/audio/fb_thoughts.m4a",
            "Your feedback helps us improve!": "/uploads/audio/fb_improve.m4a",
            "Tell us what you think!": "/uploads/audio/fb_think.m4a",

            // --- NEW PAGE PHRASES (For Clipchamp) ---
            "Your teacher assigned this special book!": "/uploads/audio/assigned_special.m4a",
            "Let's complete all the stages!": "/uploads/audio/assigned_stages.m4a",
            "Follow the map to finish the book!": "/uploads/audio/assigned_map.m4a",
            
            "Let's generate a practice test!": "/uploads/audio/aitest_gen.m4a",
            "AI will create a custom paper for you!": "/uploads/audio/aitest_custom.m4a",
            "Testing your knowledge makes you sharper!": "/uploads/audio/aitest_sharp.m4a",
            
            "Got a question? Just ask!": "/uploads/audio/chat_ask.m4a",
            "Chatting with your study buddy!": "/uploads/audio/chat_buddy.m4a",
            "Let's discuss this topic together.": "/uploads/audio/chat_discuss.m4a",
            
            "Let's review your past homework.": "/uploads/audio/hw_past.m4a",
            "Reviewing old homework is great for exams!": "/uploads/audio/hw_review.m4a",
            "Look at all the assignments you've completed!": "/uploads/audio/hw_completed.m4a",
            
            "Scan a QR code to unlock a new book!": "/uploads/audio/qr_scan1.m4a",
            "Got a book QR? Scan it here!": "/uploads/audio/qr_scan2.m4a",
            "Upload your QR code to add a book!": "/uploads/audio/qr_scan3.m4a",
            
            // --- ID Card, Attendance, Fees & Subjects ---
            "This is your official Student I-Card.": "/uploads/audio/icard_official.m4a",
            "Keep your ID card clean and safe!": "/uploads/audio/icard_clean.m4a",
            "Wear your ID card every day at school.": "/uploads/audio/icard_wear.m4a",
            
            "Your attendance looks great this month!": "/uploads/audio/att_great.m4a",
            "Always good to be present!": "/uploads/audio/att_present.m4a",
            "Keep your streak going!": "/uploads/audio/att_streak.m4a",
            "Marking your attendance is super important!": "/uploads/audio/att_important.m4a",
            "A new day, a new chance to learn!": "/uploads/audio/att_newday.m4a",
            
            "This shows your fee status for the year.": "/uploads/audio/fee_status.m4a",
            "Your fee records are kept safe here.": "/uploads/audio/fee_records.m4a",
            "Keep track of payments here.": "/uploads/audio/fee_track.m4a",
            "This page shows your payment history.": "/uploads/audio/fee_history.m4a",
            "Keep track of invoices here.": "/uploads/audio/fee_invoices.m4a",
            "All your fee details in one place!": "/uploads/audio/fee_details.m4a",
            
            "Let's explore this subject!": "/uploads/audio/sub_explore.m4a",
            "Click to see your assigned books!": "/uploads/audio/sub_assigned.m4a",
            "This subject is so much fun!": "/uploads/audio/sub_fun.m4a"
        };

        const speak = (text) => {
            // Cancel any scheduled auto-thought to prevent overlapping
            if (window.autoThoughtTimer) clearTimeout(window.autoThoughtTimer);

            const onAudioFinished = () => {
                // Hide bubble exactly when audio finishes
                if (window.bubbleTimeout) clearTimeout(window.bubbleTimeout);
                bubble.style.opacity = '0';
                bubble.style.transform = 'translateY(0)';
                
                // Wait 2 seconds, then trigger the next auto thought
                window.autoThoughtTimer = setTimeout(() => {
                    if (!window.isMascotLocked) interactAvatar(true);
                }, 2000);
            };

            if (localStorage.getItem('mascot_voice_enabled') === 'false') {
                // If muted, wait 4 seconds before next thought
                window.autoThoughtTimer = setTimeout(() => {
                    if (!window.isMascotLocked) interactAvatar(true);
                }, 4000);
                return;
            }
            
            // Strip HTML tags and trim whitespace
            const plainText = text.replace(/<[^>]*>?/gm, '').trim();

            if (audioMap[plainText]) {
                window.mascotAudio.pause();
                window.mascotAudio.src = audioMap[plainText];
                window.mascotAudio.currentTime = 0;
                window.mascotAudio.onended = onAudioFinished;
                
                const playPromise = window.mascotAudio.play();
                if (playPromise !== undefined) {
                    playPromise.then(() => {
                        // Success! Clear the fallback bubbleTimeout so bubble stays until audio ends
                        if (window.bubbleTimeout) clearTimeout(window.bubbleTimeout);
                    }).catch(e => {
                        console.log("Audio blocked by browser (waiting for tap):", e);
                        // Fallback if blocked (e.g. page change without user tap)
                        window.autoThoughtTimer = setTimeout(() => {
                            if (!window.isMascotLocked) interactAvatar(true);
                        }, 4000);
                    });
                }
            } else {
                console.log("No audio file mapped for:", plainText);
                // Fallback if no audio mapped
                window.autoThoughtTimer = setTimeout(() => {
                    if (!window.isMascotLocked) interactAvatar(true);
                }, 4000);
            }
        };

        const showBubble = (text, duration = 3000) => {
            bubble.innerHTML = text; // Use innerHTML so SVGs render instead of being printed as text
            updateBubblePosition();
            
            const rect = widget.getBoundingClientRect();
            if (rect.top < 60) {
                bubble.style.transform = 'translateY(5px)'; // slide down
            } else {
                bubble.style.transform = 'translateY(-5px)'; // slide up
            }

            bubble.style.opacity = '1';
            
            // Default fallback hiding (cleared by speak() if audio succeeds)
            if(window.bubbleTimeout) clearTimeout(window.bubbleTimeout);
            window.bubbleTimeout = setTimeout(() => {
                bubble.style.opacity = '0';
                bubble.style.transform = 'translateY(0)';
            }, duration);
            
            speak(text); // Trigger voice
        };

        // Voice Toggle Logic
        const voiceToggleBtn = document.getElementById('mascot-voice-toggle');
        const iconVoiceOn = document.getElementById('icon-voice-on');
        const iconVoiceOff = document.getElementById('icon-voice-off');

        const updateVoiceIcon = () => {
            if (localStorage.getItem('mascot_voice_enabled') === 'false') {
                iconVoiceOn.style.display = 'none';
                iconVoiceOff.style.display = 'block';
            } else {
                iconVoiceOn.style.display = 'block';
                iconVoiceOff.style.display = 'none';
            }
        };
        updateVoiceIcon();

        // Voice Toggle Logic
        const toggleVoice = (e) => {
            if(e) { e.stopPropagation(); e.preventDefault(); }
            const isEnabled = localStorage.getItem('mascot_voice_enabled') !== 'false';
            if (isEnabled) {
                localStorage.setItem('mascot_voice_enabled', 'false');
                window.mascotAudio.pause(); // Stop talking instantly
            } else {
                localStorage.setItem('mascot_voice_enabled', 'true');
            }
            updateVoiceIcon();
        };

        voiceToggleBtn.addEventListener('click', toggleVoice);
        voiceToggleBtn.addEventListener('touchend', (e) => {
            e.preventDefault(); // Prevent double-firing with click
            toggleVoice(e);
        });
        
        // Handle drag interference on the button
        voiceToggleBtn.addEventListener('touchstart', (e) => e.stopPropagation(), {passive: true});
        voiceToggleBtn.addEventListener('mousedown', (e) => e.stopPropagation());

        // Unlock Audio on Mobile (Requires direct user interaction)
        let audioUnlocked = false;
        const unlockAudio = () => {
            if (!audioUnlocked) {
                try {
                    window.mascotAudio.src = 'data:audio/wav;base64,UklGRigAAABXQVZFZm10IBIAAAABAAEARKwAAIhYAQACABAAAABkYXRhAgAAAAEA'; // Tiny silent wave
                    window.mascotAudio.play().catch(e => {});
                    audioUnlocked = true;
                } catch(e) {}
                
                // Remove listeners once unlocked
                document.removeEventListener('touchstart', unlockAudio);
                document.removeEventListener('click', unlockAudio);
            }
        };
        document.addEventListener('touchstart', unlockAudio, { passive: true });
        document.addEventListener('click', unlockAudio, { passive: true });
        
        // Pause audio when app is pushed to background
        document.addEventListener("visibilitychange", function() {
            if (document.hidden) {
                window.mascotAudio.pause();
            }
        });

        // ----------------------------------------------------
        // MODULE: Privacy Guard (Login Page)
        // ----------------------------------------------------
        window.baseArmTransform = "rotate(0deg)"; // Track arm transforms

        window.togglePrivacyGuard = (state) => {
            if (state === 'hide') {
                if (currentAvatar == 1) {
                    parts.arms.src = "/uploads/avatars/avatar1_parts_separated/4.png"; // cover eyes
                    parts.arms.style.top = "18%"; 
                    parts.arms.style.left = "24%";
                    parts.arms.style.width = "52%"; 
                    window.baseArmTransform = "rotate(0deg)";
                    parts.arms.style.transform = window.baseArmTransform;
                    parts.whites.src = "/uploads/avatars/avatar1_parts_separated/12.png"; // squint
                    parts.pupils.style.opacity = '0';
                } else {
                    staticAvatar.style.transform = 'scale(1.1) rotate(-10deg)';
                    staticAvatar.style.filter = 'brightness(0.8)';
                }
            } else if (state === 'peek') {
                if (currentAvatar == 1) {
                    parts.arms.src = "/uploads/avatars/avatar1_parts_separated/4.png"; // cover eyes
                    parts.arms.style.top = "20%"; // drop slightly
                    parts.arms.style.left = "24%";
                    parts.arms.style.width = "52%"; 
                    window.baseArmTransform = "rotate(-12deg) translateY(4px)";
                    parts.arms.style.transform = window.baseArmTransform;
                    parts.whites.src = "/uploads/avatars/avatar1_parts_separated/9.png"; // peeking eyes (normal)
                    parts.pupils.style.opacity = '1';
                    parts.pupils.style.marginLeft = "2px";
                    parts.pupils.style.marginTop = "2px";
                } else {
                    staticAvatar.style.transform = 'scale(1.05) rotate(-5deg)';
                    staticAvatar.style.filter = 'brightness(0.9)';
                }
            } else {
                if (currentAvatar == 1) {
                    parts.arms.src = "/uploads/avatars/avatar1_parts_separated/2.png"; // idle arms
                    parts.arms.style.top = "35%";
                    parts.arms.style.left = "28%";
                    parts.arms.style.width = "45%";
                    window.baseArmTransform = "rotate(0deg)";
                    parts.arms.style.transform = window.baseArmTransform;
                    parts.whites.src = "/uploads/avatars/avatar1_parts_separated/9.png"; // normal eyes
                    parts.pupils.style.opacity = '1';
                    parts.pupils.style.marginLeft = "0px";
                    parts.pupils.style.marginTop = "0px";
                } else {
                    staticAvatar.style.transform = 'scale(1) rotate(0deg)';
                    staticAvatar.style.filter = 'none';
                }
            }
        };

        if (path.includes('/login') || path.includes('/register') || path.includes('/auth')) {
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('focus', () => {
                    if (input.type === 'password') {
                        window.togglePrivacyGuard('hide');
                        showBubble("I'm not looking!");
                    } else if (input.dataset.isPassword === 'true') {
                        window.togglePrivacyGuard('peek');
                    }
                });
                input.addEventListener('blur', () => {
                    if (input.dataset.isPassword === 'true' || input.type === 'password') {
                        window.togglePrivacyGuard('idle');
                    }
                });
            });
        }

        // ----------------------------------------------------
        // MODULE: Evaluator & Celebration Actions
        // ----------------------------------------------------
        window.mascotSad = (text = "Are you leaving?") => {
            window.isMascotLocked = true;
            if (currentAvatar == 1) {
                parts.arms.src = "/uploads/avatars/avatar1_parts_separated/4.png"; // cover eyes (looks like crying)
                parts.arms.style.top = "18%"; 
                parts.arms.style.left = "24%";
                parts.arms.style.width = "52%"; 
                
                parts.mouth.src = "/uploads/avatars/avatar1_parts_separated/14.png"; 
                parts.whites.src = "/uploads/avatars/avatar1_parts_separated/12.png"; // squint/sad
                parts.pupils.style.opacity = '0';
            } else {
                staticAvatar.style.transform = 'translateY(10px) scale(0.95)';
                staticAvatar.style.filter = 'grayscale(0.5)';
            }
            showBubble(text, 5000);
        };

        window.mascotRestore = () => {
            window.isMascotLocked = false;
            
            // Stop talking and immediately hide the speech bubble
            window.speechSynthesis.cancel();
            if(window.bubbleTimeout) clearTimeout(window.bubbleTimeout);
            bubble.style.opacity = '0';
            bubble.style.transform = 'translateY(0)';

            if (currentAvatar == 1) {
                parts.arms.src = "/uploads/avatars/avatar1_parts_separated/2.png";
                parts.arms.style.top = "35%";
                parts.arms.style.left = "28%";
                parts.arms.style.width = "45%";
                parts.mouth.src = "/uploads/avatars/avatar1_parts_separated/14.png";
                
                parts.whites.src = "/uploads/avatars/avatar1_parts_separated/9.png";
                parts.pupils.style.opacity = '1';
            } else {
                staticAvatar.style.transform = 'rotate(0deg) scale(1)';
                staticAvatar.style.filter = 'none';
            }
        };

        window.mascotSay = (text) => {
            if(window.isMascotLocked) return;
            window.mascotRestore();
            if (currentAvatar == 1) {
                parts.mouth.src = "/uploads/avatars/avatar1_parts_separated/15.png";
                setTimeout(() => {
                    if(!window.isMascotLocked) parts.mouth.src = "/uploads/avatars/avatar1_parts_separated/14.png";
                }, 2000);
            }
            showBubble(text, 3500);
        };

        window.mascotCelebrate = (text = "Yay! Great job!") => {
            if (currentAvatar == 1) {
                parts.arms.src = "/uploads/avatars/avatar1_parts_separated/6.png"; // clasped hands celebration
                parts.arms.style.top = "34%"; // Fine-tuned to match shoulders perfectly
                parts.arms.style.left = "27%";
                parts.arms.style.width = "46%";
                parts.mouth.src = "/uploads/avatars/avatar1_parts_separated/15.png"; // open mouth
            } else {
                staticAvatar.style.transform = 'translateY(-20px) scale(1.1)';
            }
            showBubble(text, 5000);
            
            // Confetti
            for(let i=0; i<15; i++) {
                let p = document.createElement('div');
                p.className = 'particle';
                p.style.background = ['#FFD561', '#FFB37C', '#8BDDFF', '#FF8B8B'][Math.floor(Math.random()*4)];
                
                const rect = widget.getBoundingClientRect();
                p.style.left = (rect.left + 60) + 'px';
                p.style.top = (rect.top + 70) + 'px';
                
                const angle = Math.random() * Math.PI * 2;
                const distance = 40 + Math.random() * 80;
                p.style.setProperty('--tx', Math.cos(angle) * distance + 'px');
                p.style.setProperty('--ty', Math.sin(angle) * distance - 40 + 'px');
                
                document.body.appendChild(p);
                setTimeout(() => p.remove(), 600);
            }

            setTimeout(() => {
                if (currentAvatar == 1) {
                    parts.arms.src = "/uploads/avatars/avatar1_parts_separated/2.png";
                    parts.arms.style.top = "35%";
                    parts.arms.style.left = "28%";
                    parts.arms.style.width = "45%";
                    // Apply bounce/breathe to arms & head
                    if (currentAvatar == 1 && window.baseArmTransform) { 
                        parts.arms.style.transform = `${window.baseArmTransform} translateY(${breatheOffset * 0.5}px)`;
                    } else if (currentAvatar == 1) {
                        parts.arms.style.transform = `translateY(${breatheOffset * 0.5}px)`;
                    }
                    parts.mouth.src = "/uploads/avatars/avatar1_parts_separated/14.png";
                } else {
                    staticAvatar.style.transform = 'translateY(0) scale(1)';
                }
            }, 1500);
        };

        // If on workspace/dashboard, attach to Minigame clicks to trigger celebration
        if (path.includes('workspace') || path.includes('dashboard')) {
            document.addEventListener('click', (e) => {
                if (e.target.closest('.minigame-card') || (e.target.innerText && e.target.innerText.toLowerCase().includes('play'))) {
                    setTimeout(window.mascotCelebrate, 500);
                }
            });
        }

        // ----------------------------------------------------
        // MODULE: The Wanderer, Space Finder & Dashboard Attacher
        // ----------------------------------------------------
        const findEmptySpot = () => {
            const w = window.innerWidth;
            const h = window.innerHeight;
            const mascotW = 130;
            const mascotH = 180;
            
            let spots = [];
            // Preferred corners
            spots.push({ x: 20, y: h - mascotH - 20 }); // Bottom Left
            spots.push({ x: w - mascotW - 20, y: h - mascotH - 20 }); // Bottom Right
            if (w > 768) {
                spots.push({ x: 20, y: 80 }); // Top Left
                spots.push({ x: w - mascotW - 20, y: 80 }); // Top Right
            }
            
            // Grid points for fallback
            for (let y = 50; y < h - mascotH; y += 150) {
                for (let x = 20; x < w - mascotW; x += 100) {
                    spots.push({ x, y });
                }
            }
            
            let bestSpot = spots[0];
            let minOverlaps = 999;
            
            // Parse current position to prefer dodging AWAY from current spot if everything is covered
            const currentX = parseFloat(widget.style.left) || 20;
            const currentY = parseFloat(widget.style.top) || 20;
            
            for (let spot of spots) {
                const checkPoints = [
                    { cx: spot.x + 65, cy: spot.y + 90 }, // Center
                    { cx: spot.x + 40, cy: spot.y + 60 }, // Top Left
                    { cx: spot.x + 90, cy: spot.y + 60 }  // Top Right
                ];
                
                let overlaps = 0;
                for (let pt of checkPoints) {
                    const els = document.elementsFromPoint(pt.cx, pt.cy);
                    if (!els) continue;
                    
                    els.forEach(el => {
                        if (el.closest('#schoolbag-mascot-widget')) return;
                        const tag = el.tagName ? el.tagName.toUpperCase() : '';
                        if (
                            el.closest('.s-card') || 
                            el.closest('.card') || 
                            el.closest('form') ||
                            el.closest('button') || 
                            ['INPUT', 'SELECT', 'TEXTAREA', 'BUTTON', 'A'].includes(tag)
                        ) {
                            overlaps += 1;
                        }
                    });
                }
                
                if (overlaps === 0) return spot; // Found perfect spot!
                
                if (overlaps < minOverlaps) {
                    minOverlaps = overlaps;
                    bestSpot = spot;
                }
            }
            
            return bestSpot;
        };

        let isAttached = false;
        let isJumping = false;
        let isUserPlaced = false; // Tracks if user manually dragged it
        
        const setAttachedState = (attached) => {
            if (attached) {
                widget.style.transition = 'none';
                isAttached = true;
            } else {
                widget.style.transition = 'none'; // We handle movement manually via rAF
                isAttached = false;
            }
        };

        const toggleOverlays = (show) => {
            const opacity = show ? '1' : '0';
            parts.arms.style.opacity = opacity;
            parts.whites.style.opacity = opacity;
            parts.pupils.style.opacity = opacity;
            parts.mouth.style.opacity = opacity;
        };

        let isMoving = false;
        let moveTimeout;
        let moveFrameIndex = 0;
        const moveFrames = [22, 23];
        let lastMoveTime = 0;

        const triggerMovementAnim = (dx = 0) => {
            if (isJumping) return; // Don't interfere with jumpToSpot

            if (currentAvatar != 1) {
                const now = Date.now();
                const wobble = Math.sin(now / 80) * 10;
                staticAvatar.style.transform = `rotate(${wobble}deg) scaleY(${1 + Math.abs(Math.sin(now/150)*0.1)})`;
                
                clearTimeout(moveTimeout);
                moveTimeout = setTimeout(() => {
                    staticAvatar.style.transform = 'rotate(0deg) scaleY(1)';
                }, 150);
                return;
            }

            if (!isMoving) toggleOverlays(false);
            isMoving = true;

            if (dx !== 0) {
                widget.style.transform = `scaleX(${dx < 0 ? -1 : 1})`;
            }

            const now = Date.now();
            if (now - lastMoveTime > 120) {
                moveFrameIndex = (moveFrameIndex + 1) % moveFrames.length;
                parts.base.src = `/uploads/avatars/avatar1_parts_separated/${moveFrames[moveFrameIndex]}.png`;
                lastMoveTime = now;
            }

            clearTimeout(moveTimeout);
            moveTimeout = setTimeout(() => {
                isMoving = false;
                if (!isJumping) {
                    parts.base.src = '/uploads/avatars/avatar1_parts_separated/1.png';
                    toggleOverlays(true);
                    widget.style.transform = 'scaleX(1)';
                }
            }, 150);
        };

        const jumpToSpot = (spot) => {
            if (isAttached || isJumping) return;
            isJumping = true;

            // Parse current position
            const startX = parseFloat(widget.style.left) || 20;
            const startY = parseFloat(widget.style.top) || 20;
            const endX = spot.x;
            const endY = spot.y;
            const dx = endX - startX;
            const dy = endY - startY;
            const distance = Math.sqrt(dx * dx + dy * dy);
            
            if (distance < 20) return; // Too close, already there!
            
            const isJump = distance < 250; // Short distance = jump arc, long distance = walk frames

            // Duration scales with distance
            const duration = isJump ? 800 : Math.min(2800, Math.max(1200, distance * 3));
            const startTime = performance.now();

            let walkFrameIndex = 0;
            const walkFrames = [22, 23];
            let lastFrameTime = 0;
            const frameDuration = 280;

            if (currentAvatar == 1) {
                toggleOverlays(false);
                parts.base.src = isJump ? '/uploads/avatars/avatar1_parts_separated/24.png' : '/uploads/avatars/avatar1_parts_separated/22.png';
            }

            // Flip mascot to face direction of travel
            const scaleX = dx < 0 ? -1 : 1;
            widget.style.transform = `scaleX(${scaleX})`;

            const animate = (now) => {
                const elapsed = now - startTime;
                const t = Math.min(elapsed / duration, 1);
                
                // Ease function
                const easedT = t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t;

                const currentX = startX + dx * easedT;
                let currentY = startY + dy * easedT;
                
                // Jump physics: apply parabolic arc if it's a short distance
                if (isJump) {
                    const jumpHeight = 60; // pixels
                    currentY -= Math.sin(t * Math.PI) * jumpHeight;
                }

                widget.style.left = currentX + 'px';
                widget.style.top = currentY + 'px';
                
                // Avatar 2 Wobble for walking/jumping
                if (currentAvatar != 1) {
                    const wobble = Math.sin(now / 80) * 10;
                    staticAvatar.style.transform = `rotate(${wobble}deg) scaleY(${1 + Math.abs(Math.sin(now/150)*0.1)})`;
                }

                if (bubble.style.opacity === '1') updateBubblePosition();

                // Walk frames if not jumping
                if (!isJump && currentAvatar == 1) {
                    if (now - lastFrameTime > frameDuration) {
                        walkFrameIndex = (walkFrameIndex + 1) % walkFrames.length;
                        parts.base.src = `/uploads/avatars/avatar1_parts_separated/${walkFrames[walkFrameIndex]}.png`;
                        lastFrameTime = now;
                    }
                }

                if (t < 1) {
                    requestAnimationFrame(animate);
                } else {
                    // Landed
                    widget.style.left = endX + 'px';
                    widget.style.top = endY + 'px';
                    widget.style.transform = 'scaleX(1)';
                    isJumping = false;
                    
                    if (currentAvatar == 1) {
                        parts.base.src = '/uploads/avatars/avatar1_parts_separated/1.png';
                        toggleOverlays(true);
                    } else {
                        staticAvatar.style.transform = 'rotate(0deg) scaleY(1)';
                    }
                }
            };

            requestAnimationFrame(animate);
        };

        const ring = document.getElementById('dashboard-mascot-ring');
        
        if (ring && !path.includes('/login') && !path.includes('/register')) {
            // Dashboard Logic
            setAttachedState(true);
            
            const syncWithRingOrWander = () => {
                if (isUserPlaced) return; // Don't auto-snap if user dragged it
                const ringRect = ring.getBoundingClientRect();
                
                // If ring is near the top of the viewport (hasn't scrolled away)
                if (ringRect.top > 20) {
                    if (!isAttached) setAttachedState(true);
                    
                    // Attach perfectly inside the ring (ring is 140x140, widget is 130x180)
                    widget.style.left = (ringRect.left + 5) + 'px'; 
                    widget.style.top = (ringRect.top - 15) + 'px';
                    if (bubble.style.opacity === '1') updateBubblePosition();
                    
                } else {
                    // Ring scrolled out of view - detach and walk down (stay in frame)
                    if (isAttached) {
                        setAttachedState(false);
                        jumpToSpot(findEmptySpot());
                    }
                }
            };

            window.addEventListener('scroll', syncWithRingOrWander, {passive: true});
            window.addEventListener('resize', syncWithRingOrWander);
            syncWithRingOrWander(); // Initial placement

        } else if (!path.includes('/login') && !path.includes('/register')) {
            // Other pages: wait a bit and jump to empty spot immediately
            setAttachedState(false);
            setTimeout(() => { if (!isUserPlaced) jumpToSpot(findEmptySpot()); }, 100);

        } else {
            // Login page static position
            setAttachedState(false);
            widget.style.left = (window.innerWidth - 140) + 'px';
            widget.style.top = '20px';
        }

        // GLOBAL AUTO-DODGE (Runs every 2 seconds)
        setInterval(() => {
            if (isAttached || isJumping || isUserPlaced) return;
            
            const rect = widget.getBoundingClientRect();
            // Check multiple points around the mascot to be safe
            const checkPoints = [
                {x: rect.left + 65, y: rect.top + 90}, // Center
                {x: rect.left + 40, y: rect.top + 60}, // Top Left
                {x: rect.left + 90, y: rect.top + 60}  // Top Right
            ];
            
            let isCovering = false;
            for (let pt of checkPoints) {
                const els = document.elementsFromPoint(pt.x, pt.y);
                if (!els) continue;
                
                els.forEach(el => {
                    if(el.closest('#schoolbag-mascot-widget')) return;
                    const tag = el.tagName ? el.tagName.toUpperCase() : '';
                    if (
                        el.closest('.s-card') || 
                        el.closest('.card') || 
                        el.closest('form') ||
                        el.closest('button') || 
                        ['INPUT', 'SELECT', 'TEXTAREA', 'BUTTON', 'A'].includes(tag)
                    ) {
                        isCovering = true;
                    }
                });
                if (isCovering) break;
            }
            
            if(isCovering) jumpToSpot(findEmptySpot());
        }, 2000);


        // ----------------------------------------------------
        // PROCEDURAL ANIMATIONS
        // ----------------------------------------------------
        let eyeTargetX = window.innerWidth / 2;
        let eyeTargetY = window.innerHeight / 2;
        let scrollEyeTimeout;

        const updateEyes = () => {
            if(currentAvatar != 1) return;
            const rect = parts.whites.getBoundingClientRect();
            const eyeCenterX = rect.left + rect.width / 2;
            const eyeCenterY = rect.top + rect.height / 2;
            
            const dx = eyeTargetX - eyeCenterX;
            const dy = eyeTargetY - eyeCenterY;
            
            const maxMove = 3;
            const angle = Math.atan2(dy, dx);
            const moveX = Math.cos(angle) * Math.min(maxMove, Math.abs(dx/25));
            const moveY = Math.sin(angle) * Math.min(maxMove, Math.abs(dy/25));
            
            parts.pupils.style.marginLeft = `${moveX}px`;
            parts.pupils.style.marginTop = `${moveY}px`;
        };

        window.addEventListener('mousemove', (e) => {
            eyeTargetX = e.clientX;
            eyeTargetY = e.clientY;
            updateEyes();
        });

        window.addEventListener('touchstart', (e) => {
            if(e.touches.length > 0) {
                eyeTargetX = e.touches[0].clientX;
                eyeTargetY = e.touches[0].clientY;
                updateEyes();
            }
        }, {passive: true});
        
        window.addEventListener('click', (e) => {
            eyeTargetX = e.clientX;
            eyeTargetY = e.clientY;
            updateEyes();
        });

        let lastEyeScrollY = window.scrollY;
        window.addEventListener('scroll', () => {
            const currentScrollY = window.scrollY;
            if (currentScrollY > lastEyeScrollY) {
                // Scrolling down -> look down
                eyeTargetY = window.innerHeight + 500; 
            } else if (currentScrollY < lastEyeScrollY) {
                // Scrolling up -> look up
                eyeTargetY = -500;
            }
            eyeTargetX = window.innerWidth / 2;
            lastEyeScrollY = currentScrollY;
            updateEyes();
            
            // Return eyes to center a moment after scrolling stops
            clearTimeout(scrollEyeTimeout);
            scrollEyeTimeout = setTimeout(() => {
                eyeTargetX = window.innerWidth / 2;
                eyeTargetY = window.innerHeight / 2;
                updateEyes();
            }, 300);
        }, {passive: true});
        
        setInterval(() => {
            if(currentAvatar != 1) return;
            if(isJumping || isMoving) return; // Don't blink while walking or jumping (overlays are hidden)
            if(parts.arms.src.includes('4.png')) return; // Don't blink if covering eyes

            parts.whites.src = "/uploads/avatars/avatar1_parts_separated/12.png"; // squint/closed
            parts.pupils.style.opacity = '0';
            setTimeout(() => {
                // Ensure we haven't started walking or jumping during the blink
                if (!isJumping && !isMoving) {
                    parts.whites.src = "/uploads/avatars/avatar1_parts_separated/9.png"; // open
                    parts.pupils.style.opacity = '1';
                }
            }, 150);
        }, 3000 + Math.random() * 4000);

        // ----------------------------------------------------
        // SCROLL TILT
        // ----------------------------------------------------
        let lastScrollY = window.scrollY;
        let lookTimeout;
        innerRing.style.transition = 'transform 0.15s ease-out';
        
        window.addEventListener('scroll', () => {
            isUserPlaced = false; // Reset manual placement flag so he can dodge if needed
            
            const scrolled = window.scrollY;
            const delta = scrolled - lastScrollY;
            
            // Trigger walk cycle when scrolling
            if (Math.abs(delta) > 2) {
                triggerMovementAnim(0);
            }
            
            const tilt = Math.min(15, scrolled * 0.15); 
            const rotate = Math.sin(scrolled * 0.02) * 10;
            widget.style.transform = `perspective(500px) rotateX(${tilt}deg) rotateY(${rotate}deg)`;
            
            const lookY = Math.min(Math.max(delta * 0.6, -12), 12);
            const lookRotX = Math.min(Math.max(delta * 1.2, -20), 20);
            innerRing.style.transform = `perspective(400px) rotateX(${lookRotX}deg) translateY(${lookY}px)`;
            
            clearTimeout(lookTimeout);
            lookTimeout = setTimeout(() => {
                innerRing.style.transform = 'perspective(400px) rotateX(0deg) translateY(0px)';
            }, 150);
            
            lastScrollY = scrolled;
        }, { passive: true });

        // ----------------------------------------------------
        // INTERACTION & DRAG
        // ----------------------------------------------------
        const spawnParticles = () => {
            const rect = widget.getBoundingClientRect();
            const cx = rect.left + 65;
            const cy = rect.top + 90;
            
            const colors = ['#FFD561', '#FF7C7C', '#8BDDFF', '#9DE182'];
            for(let i=0; i<8; i++) {
                const p = document.createElement('div');
                p.className = 'particle';
                p.style.background = colors[Math.floor(Math.random() * colors.length)];
                p.style.left = cx + 'px';
                p.style.top = cy + 'px';
                
                const angle = (Math.random() * Math.PI * 2);
                const distance = 50 + Math.random() * 50;
                p.style.setProperty('--tx', Math.cos(angle) * distance + 'px');
                p.style.setProperty('--ty', Math.sin(angle) * distance + 'px');
                
                document.body.appendChild(p);
                setTimeout(() => p.remove(), 600);
            }
        };

        window.lastMascotPhrase = "";
        const getRandomPhrase = (phrases) => {
            if (!phrases || phrases.length === 0) return "";
            if (phrases.length === 1) return phrases[0];
            
            let phrase;
            let attempts = 0;
            do {
                phrase = phrases[Math.floor(Math.random() * phrases.length)];
                attempts++;
            } while (phrase === window.lastMascotPhrase && attempts < 10);
            
            window.lastMascotPhrase = phrase;
            return phrase;
        };

        const getContextPhrase = () => {
            const path = window.location.pathname.toLowerCase();
            const rect = widget.getBoundingClientRect();
            // Calculate strict geometric 2D center of the mascot
            const mascotCenterX = rect.left + (rect.width / 2);
            const mascotCenterY = rect.top + (rect.height / 2);
            
            // 1. Identify overarching page route
            const url = window.location.href.toLowerCase();
            let pageContext = 'dashboard';
            if (path.includes('workspace')) pageContext = 'workspace';
            if (path.includes('assigned-ebook')) pageContext = 'assigned_ebooks';
            else if (path.includes('ebook') || path.includes('subject') || path.includes('lesson')) pageContext = 'library';
            if (path.includes('aitest') || path.includes('ai-test')) pageContext = 'aitest';
            if (path.includes('chat')) pageContext = 'chat';
            if (path.includes('profile')) pageContext = 'profile';
            if (path.includes('password') || path.includes('reset')) pageContext = 'password';
            if (path.includes('homework-history') || path.includes('worksheet')) pageContext = 'homework_history';
            if (url.includes('transaction') || url.includes('fee') || url.includes('payment')) pageContext = 'fees';
            if (url.includes('attendance')) pageContext = 'attendance';
            if (url.includes('icard')) pageContext = 'icard';
            if (path.includes('feedback')) pageContext = 'feedback';
            if (path.includes('terms') || path.includes('privacy')) pageContext = 'legal';
            if (path.includes('login') || path.includes('register') || path.includes('auth')) pageContext = 'auth';

            // 2. Perform DOM Proximity Search ONLY for multi-feature pages
            let elementContext = null;
            if (pageContext === 'workspace' || pageContext === 'dashboard' || pageContext === 'assigned_ebooks' || pageContext === 'library' || pageContext === 'profile') {
                const candidateNodes = document.querySelectorAll('[class*="card"], section, [class*="game"], .grid-btn, .notice-board-container, .daily-quest-flat');
                
                // Filter out layout wrappers that accidentally contain "card" (like card-body-main)
                const candidateElements = Array.from(candidateNodes).filter(el => {
                    const cls = el.className || '';
                    return !cls.includes('card-body-main') && !cls.includes('dashboard-layout-wrapper');
                });
                
                let closestEl = null;
                let minDistance = Infinity;
                
                for (let el of candidateElements) {
                    const r = el.getBoundingClientRect();
                    // Ignore invisible elements
                    if (r.bottom < 0 || r.top > window.innerHeight) continue;
                    
                    // Geometric 2D math: Euclidean distance from Mascot Center to Element Center
                    const elCenterX = r.left + (r.width / 2);
                    const elCenterY = r.top + (r.height / 2);
                    const distance = Math.sqrt(
                        Math.pow(mascotCenterX - elCenterX, 2) + 
                        Math.pow(mascotCenterY - elCenterY, 2)
                    );
                    
                    // Weight boost: If the mascot strictly overlaps the 2D bounds, subtract heavily
                    const isOverlapping = (
                        mascotCenterX >= r.left && mascotCenterX <= r.right && 
                        mascotCenterY >= r.top && mascotCenterY <= r.bottom
                    );
                    const finalScore = isOverlapping ? distance - 1000 : distance;
                    
                    if (finalScore < minDistance) {
                        minDistance = finalScore;
                        closestEl = el;
                    }
                }
                
                if (closestEl) {
                    let textContext = '';
                    
                    // 1. Check for explicit data attribute (100% Industry Standard Accuracy)
                    if (closestEl.dataset.mascotContext) {
                        elementContext = closestEl.dataset.mascotContext;
                    } else {
                        // 2. Extract precise headers if they exist
                        const headers = closestEl.querySelectorAll('h1, h2, h3, h4, h5, h6, .card-title, .title, .fw-bold');
                        if (headers.length > 0) {
                            headers.forEach(h => textContext += ' ' + h.innerText);
                        } else {
                            // 3. Fallback to entire text if no headers found (fixes custom spans)
                            textContext = closestEl.innerText;
                        }
                        
                        textContext = (textContext || '').toLowerCase();
                        const cls = (typeof closestEl.className === 'string' ? closestEl.className : (closestEl.getAttribute('class') || '')).toLowerCase();
                        
                        // Prioritize context mapping based on text & classes
                        if (cls.includes('attendance') || cls.includes('att-') || textContext.includes('attendance')) {
                            elementContext = 'attendance';
                        } else if (cls.includes('id-card') || cls.includes('id_card') || textContext.includes('id card') || textContext.includes('i-card')) {
                            elementContext = 'id_card';
                        } else if (cls.includes('minigame') || cls.includes('game') || textContext.includes('minigame')) {
                            if (textContext.includes('memory') || textContext.includes('match') || textContext.includes('flip')) elementContext = 'memory_game';
                            else if (textContext.match(/\b(scramble|word)\b/)) elementContext = 'word_game';
                            else if (textContext.match(/\b(math|puzzle)\b/)) elementContext = 'math_game';
                            else elementContext = 'minigame';
                        } else if (textContext.includes('fee') || textContext.includes('payment') || textContext.includes('invoice') || cls.includes('fee')) {
                            elementContext = 'fees';
                        } else if (textContext.includes('homework') || textContext.includes('assignment') || cls.includes('notice')) {
                            elementContext = 'homework';
                        } else if (textContext.includes('quest') || textContext.includes('map') || textContext.includes('treasure')) {
                            elementContext = 'quest';
                        } else if (textContext.includes('exam') || textContext.includes('result')) {
                            elementContext = 'exam';
                        } else if (textContext.includes('timetable') || textContext.includes('schedule') || textContext.includes('routine')) {
                            elementContext = 'timetable';
                        } else if (textContext.includes('ai test') || textContext.includes('test paper')) {
                            elementContext = 'aitest';
                        } else if (textContext.includes('chat with') || textContext.includes('chat')) {
                            elementContext = 'chat';
                        } else if (textContext.includes('map view') || textContext.includes('create map')) {
                            elementContext = 'map_view';
                        } else if (textContext.includes('flipbook')) {
                            elementContext = 'flipbook';
                        } else if (textContext.includes('scan') || textContext.includes('qr') || textContext.includes('upload')) {
                            elementContext = 'qr_scanner';
                        } else if (textContext.includes('math adventure') || textContext.includes('science explorer') || textContext.includes('english storytime')) {
                            elementContext = 'subject_card';
                        }
                    }
                }
            }

            // --- 3. RESOLVE FINAL PHRASES ---
            
            // Dynamic Proximity Matches (Workspace & Details)
            if (pageContext === 'workspace' || pageContext === 'dashboard' || pageContext === 'assigned_ebooks' || pageContext === 'library' || pageContext === 'profile') {
                if (elementContext === 'attendance') return getRandomPhrase(["Your attendance looks great this month!", "Always good to be present!", "Keep your streak going!", "Marking your attendance is super important!", "A new day, a new chance to learn!"]);
                if (elementContext === 'fees') return getRandomPhrase(["This shows your fee status for the year.", "Your fee records are kept safe here.", "Keep track of payments here."]);
                if (elementContext === 'homework') return getRandomPhrase(["Check here to see if teachers assigned homework today!", "Make sure to finish your assignments on time!", "Homework helps you practice what you learned!"]);
                
                if (elementContext === 'memory_game') return getRandomPhrase(["Flip the cards to find matching pairs!", "Can you remember where that card was?", "Match them all to win!"]);
                if (elementContext === 'word_game') return getRandomPhrase(["Unscramble the letters to make a word!", "Word games make you smarter!", "Need a hint for this word?"]);
                if (elementContext === 'math_game') return getRandomPhrase(["Math puzzles are a great brain workout!", "Can you solve this math problem?", "Numbers are fun!"]);
                if (elementContext === 'minigame') return getRandomPhrase(["Wanna play a minigame? It's super fun!", "Let's test your skills!", "Games make learning awesome!"]);
                
                if (elementContext === 'quest') return getRandomPhrase(["Complete your daily quests to find the treasure!", "There is a map waiting to be explored!", "Every quest gives you rewards!"]);
                if (elementContext === 'exam') return getRandomPhrase(["Good luck with your exams!", "Study hard and you'll do great!", "Check your awesome results!"]);
                if (elementContext === 'timetable') return getRandomPhrase(["Here is your class schedule.", "Be on time for your classes!", "Check what subject is next!"]);
                
                if (elementContext === 'aitest') return getRandomPhrase(["Let's generate a practice test!", "AI will create a custom paper for you!", "Testing your knowledge makes you sharper!"]);
                if (elementContext === 'chat') return getRandomPhrase(["Got a question? Just ask!", "Chatting with your study buddy!", "Let's discuss this topic together."]);
                if (elementContext === 'map_view') return getRandomPhrase(["Follow the map to finish the book!", "Let's complete all the stages!"]);
                if (elementContext === 'flipbook') return getRandomPhrase(["Look at all these amazing books!", "What are we reading today?", "Let's read a story together!"]);
                if (elementContext === 'qr_scanner') return getRandomPhrase(["Scan a QR code to unlock a new book!", "Got a book QR? Scan it here!", "Upload your QR code to add a book!"]);
                if (elementContext === 'subject_card') return getRandomPhrase(["Let's explore this subject!", "Click to see your assigned books!", "This subject is so much fun!"]);
                if (elementContext === 'id_card') return getRandomPhrase(["This is your official Student I-Card.", "Keep your ID card clean and safe!", "Wear your ID card every day at school."]);
                
                if (pageContext === 'assigned_ebooks') {
                    return getRandomPhrase(["Your teacher assigned this special book!", "Let's complete all the stages!", "Follow the map to finish the book!"]);
                }
                
                if (pageContext === 'library') {
                    return getRandomPhrase(["Let's read a story together!", "Books are magical portals!", "Pick a subject to start learning!", "Reading makes your brain super strong!"]);
                }
                
                if (pageContext === 'dashboard') {
                    return getRandomPhrase(["Welcome to Schoolbag!", "Ready to learn?", "You're doing great!", "Learning is a lifelong journey!"]);
                }
                
                if (pageContext === 'profile') {
                    return getRandomPhrase(["This is your profile! You can customize things here.", "Show off your badges and points!", "Make sure your information is up to date!"]);
                }
                
                return getRandomPhrase(["Welcome to your Workspace!", "Here you can see your daily tasks.", "Check your attendance and assignments here.", "Ready to learn something new?"]);
            }
            
            // Static Page Routes Matches
            switch(pageContext) {
                case 'aitest':
                    return getRandomPhrase(["Let's generate a practice test!", "AI will create a custom paper for you!", "Testing your knowledge makes you sharper!"]);
                case 'chat':
                    return getRandomPhrase(["Got a question? Just ask!", "Chatting with your study buddy!", "Let's discuss this topic together."]);
                case 'homework_history':
                    return getRandomPhrase(["Let's review your past homework.", "Reviewing old homework is great for exams!", "Look at all the assignments you've completed!"]);
                case 'profile':
                    return getRandomPhrase(["This is your profile! You can customize things here.", "Show off your badges and points!", "Make sure your information is up to date!"]);
                case 'password':
                    return getRandomPhrase(["Make sure your new password is secure!", "Don't share your password with anyone!", "Keep your account safe!"]);
                case 'fees':
                    return getRandomPhrase(["This page shows your payment history.", "Keep track of invoices here.", "All your fee details in one place!"]);
                case 'attendance':
                    return getRandomPhrase(["Your attendance looks great this month!", "Always good to be present!", "Keep your streak going!", "Marking your attendance is super important!", "A new day, a new chance to learn!"]);
                case 'icard':
                    return getRandomPhrase(["This is your official Student I-Card.", "Keep your ID card clean and safe!", "Wear your ID card every day at school."]);
                case 'feedback':
                    return getRandomPhrase(["We love hearing your thoughts!", "Your feedback helps us improve!", "Tell us what you think!"]);
                case 'legal':
                    return getRandomPhrase(["This page has important rules.", "Privacy is super important to us!", "Always read the rules to stay safe online!"]);
                case 'auth':
                    return getRandomPhrase(["Welcome to Schoolbag! Please sign in.", "Ready for another great day of learning?", "Log in to see your dashboard!"]);
                default:
                    return getRandomPhrase(["Welcome to Schoolbag!", "Ready to learn?", "You're doing great!", "Learning is a lifelong journey!"]);
            }
        };
        
        const interactAvatar = (isAuto = false) => {
            if (window.isMascotLocked) return;
            if (!isAuto) {
                if(currentAvatar == 1) {
                    parts.mouth.src = "/uploads/avatars/avatar1_parts_separated/15.png"; // open mouth
                    parts.arms.src = "/uploads/avatars/avatar1_parts_separated/6.png"; // clasped hands celebration
                    parts.arms.style.top = "32%"; // Fine-tuned to match shoulders perfectly
                    parts.arms.style.left = "31%";
                    parts.arms.style.width = "40%";
                }
                
                spawnParticles();
                if (navigator.vibrate) navigator.vibrate(50);
                
                setTimeout(() => {
                    if(currentAvatar == 1) {
                        parts.mouth.src = "/uploads/avatars/avatar1_parts_separated/14.png"; // closed mouth
                        
                        // Check if they are still focused on a password field that is currently hidden
                        if (document.activeElement && document.activeElement.type === 'password') {
                            parts.arms.src = "/uploads/avatars/avatar1_parts_separated/4.png"; // cover eyes
                            parts.arms.style.top = "18%"; 
                            parts.arms.style.left = "24%";
                            parts.arms.style.width = "52%"; 
                            window.baseArmTransform = "rotate(0deg)";
                        } else if (document.activeElement && document.activeElement.dataset.isPassword === 'true') {
                            parts.arms.src = "/uploads/avatars/avatar1_parts_separated/4.png"; // peek
                            parts.arms.style.top = "20%"; 
                            parts.arms.style.left = "24%";
                            parts.arms.style.width = "52%"; 
                            window.baseArmTransform = "rotate(-12deg) translateY(4px)";
                        } else {
                            parts.arms.src = "/uploads/avatars/avatar1_parts_separated/2.png"; // idle arms
                            parts.arms.style.top = "35%";
                            parts.arms.style.left = "28%";
                            parts.arms.style.width = "45%";
                        }
                    }
                }, 1000);
            }
            
            // Show thought bubble for 3.8 seconds so it barely disappears before the 4s interval
            showBubble(getContextPhrase(), 3800);
        };
        
        // Start the intelligent auto-loop
        window.autoThoughtTimer = setTimeout(() => {
            if (!window.isMascotLocked) interactAvatar(true);
        }, 4000);

        let lastClick = 0;
        let isDragging = false;
        let dragStartX, dragStartY, initialLeft, initialTop;
        let wasDragged = false;

        const onDragStart = (x, y) => {
            isDragging = true;
            wasDragged = false;
            dragStartX = x;
            dragStartY = y;
            initialLeft = parseFloat(widget.style.left) || 0;
            initialTop = parseFloat(widget.style.top) || 0;
            isAttached = false;
        };

        let lastDragX = 0;
        const onDragMove = (x, y) => {
            if (!isDragging) return;
            const dx = x - dragStartX;
            const dy = y - dragStartY;
            if (Math.abs(dx) > 5 || Math.abs(dy) > 5) wasDragged = true;
            
            // Constrain to screen bounds
            const newLeft = Math.max(0, Math.min(window.innerWidth - 130, initialLeft + dx));
            const newTop = Math.max(0, Math.min(window.innerHeight - 180, initialTop + dy));
            
            const directionX = newLeft - lastDragX;
            lastDragX = newLeft;
            
            widget.style.left = newLeft + 'px';
            widget.style.top = newTop + 'px';
            if (bubble.style.opacity === '1') updateBubblePosition();
            
            // Trigger walk cycle on drag
            triggerMovementAnim(directionX);
        };

        const onDragEnd = () => {
            if (isDragging) {
                isDragging = false;
                if (wasDragged) isUserPlaced = true;
            }
        };

        // Mouse Events
        widget.addEventListener('mousedown', (e) => {
            e.preventDefault(); // Prevent image dragging ghost
            onDragStart(e.clientX, e.clientY);
        });
        window.addEventListener('mousemove', (e) => onDragMove(e.clientX, e.clientY));
        window.addEventListener('mouseup', onDragEnd);

        // Touch Events
        widget.addEventListener('touchstart', (e) => {
            if (e.touches.length > 0) onDragStart(e.touches[0].clientX, e.touches[0].clientY);
        }, {passive: true});
        window.addEventListener('touchmove', (e) => {
            if (e.touches.length > 0) onDragMove(e.touches[0].clientX, e.touches[0].clientY);
        }, {passive: true});
        window.addEventListener('touchend', onDragEnd);

        widget.addEventListener('click', (e) => {
            if (wasDragged) return; // Prevent click trigger if the user was just dragging
            
            let now = Date.now();
            interactAvatar(false); // Manual click
            
            if (now - lastClick < 400) { 
                currentAvatar = currentAvatar == 1 ? 2 : 1;
                localStorage.setItem('student_avatar_index', currentAvatar);
                updateAvatarDisplay();
                lastClick = 0;
            } else {
                lastClick = now;
            }
        });
        
    });
})();
