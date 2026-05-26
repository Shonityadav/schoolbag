document.addEventListener('DOMContentLoaded', () => {
    const state = {
        currentGame: null,
        player: { name: '', score: 0 },
        currentQuestionIndex: 0
    };

    const startGame = (gameName) => {
        switch(gameName) {
            case 'numberStory':
                numberStory();
                break;
            case 'puzzleBoxes':
                puzzleBoxes();
                break;
            case 'patternQuest':
                patternQuest();
                break;
            case 'mathMatch':
                mathMatch();
                break;
            default:
                console.log('Game not found!');
        }
    };

    const showScore = () => {
        const scoreContainer = document.getElementById('score-container');
        if(scoreContainer) scoreContainer.textContent = `Player Score: ${state.player.score}`;
    };

    /* =========================
       NUMBER STORY
    ========================= */
    const numberStoryQuestions = [
        { question: "I am a two-digit number. Sum of digits = 9. Guess me.", answer: 45, hint: "Both digits add to 9." },
        { question: "I am less than 50, greater than 40. Guess me.", answer: 42, hint: "I am even." },
        { question: "I am a multiple of 7 between 50 and 70. Guess me.", answer: 56, hint: "Divisible by 7." }
    ];

    const numberStory = () => {
        state.currentQuestionIndex = 0;
        const container = document.getElementById('game-container');

        const renderQuestion = () => {
            const q = numberStoryQuestions[state.currentQuestionIndex];
            container.innerHTML = `
                <h2 class="section-title">Number Story</h2>
                <p>${q.question}</p>
                <input type="number" id="guess-input" placeholder="Enter your guess"/>
                <div class="game-buttons">
                  <button class="btn-primary" id="check-guess">Check</button>
                  <button class="btn-warning" id="hint-btn">Hint</button>
                  <button class="btn-secondary" id="next-btn">Next</button>
                  <button class="btn-danger" id="reset-btn">Reset</button>
                </div>
                <p id="feedback"></p>
            `;

            document.getElementById('check-guess').onclick = () => {
                const guess = parseInt(document.getElementById('guess-input').value);
                const feedback = document.getElementById('feedback');
                if (guess === q.answer) {
                    feedback.textContent = '🎉 Correct! +10 points';
                    state.player.score += 10;
                    showScore();
                } else {
                    feedback.textContent = '❌ Wrong! Try again.';
                }
            };

            document.getElementById('hint-btn').onclick = () => {
                document.getElementById('feedback').textContent = `💡 Hint: ${q.hint}`;
            };

            document.getElementById('next-btn').onclick = () => {
                state.currentQuestionIndex = (state.currentQuestionIndex + 1) % numberStoryQuestions.length;
                renderQuestion();
            };

            document.getElementById('reset-btn').onclick = () => {
                state.player.score = 0;
                showScore();
                renderQuestion();
            };
        };

        renderQuestion();
    };

    /* =========================
       PUZZLE BOXES
    ========================= */
    const puzzleBoxesQuestions = [
        { order: [1, 2, 3], hint: "Start from smallest to largest." },
        { order: [3, 1, 2], hint: "Biggest box first." },
        { order: [2, 3, 1], hint: "Middle box first." }
    ];

    const puzzleBoxes = () => {
        state.currentQuestionIndex = 0;
        const container = document.getElementById('game-container');

        const renderPuzzle = () => {
            const q = puzzleBoxesQuestions[state.currentQuestionIndex];
            let userOrder = [];
            container.innerHTML = `
                <h2 class="section-title">Puzzle Boxes</h2>
                <p>Click the boxes in the correct order!</p>
                <div id="boxes" style="display:flex;gap:10px;margin:10px 0;"></div>
                <div class="game-buttons">
                  <button class="btn-warning" id="hint-btn">Hint</button>
                  <button class="btn-secondary" id="next-btn">Next</button>
                  <button class="btn-danger" id="reset-btn">Reset</button>
                </div>
                <p id="puzzle-feedback"></p>
            `;

            const boxes = document.getElementById('boxes');
            for(let i=1;i<=3;i++){
                const box = document.createElement('div');
                box.textContent = `Box ${i}`;
                box.classList.add('draggable');
                box.style.width = '80px';
                box.style.height = '80px';
                box.style.background = '#f5a623';
                box.style.display = 'flex';
                box.style.alignItems = 'center';
                box.style.justifyContent = 'center';
                box.style.borderRadius = '10px';
                box.style.cursor = 'pointer';
                box.onclick = () => {
                    userOrder.push(i);
                    if(userOrder.length === q.order.length){
                        const feedback = document.getElementById('puzzle-feedback');
                        if(userOrder.join() === q.order.join()){
                            feedback.textContent = '🎉 Correct! +10 points';
                            state.player.score +=10;
                            showScore();
                        } else {
                            feedback.textContent = '❌ Wrong! Try again.';
                        }
                        userOrder = [];
                    }
                };
                boxes.appendChild(box);
            }

            document.getElementById('hint-btn').onclick = () => {
                document.getElementById('puzzle-feedback').textContent = `💡 Hint: ${q.hint}`;
            };

            document.getElementById('next-btn').onclick = () => {
                state.currentQuestionIndex = (state.currentQuestionIndex +1)%puzzleBoxesQuestions.length;
                renderPuzzle();
            };

            document.getElementById('reset-btn').onclick = () => {
                state.player.score =0;
                showScore();
                renderPuzzle();
            };
        };

        renderPuzzle();
    };

    /* =========================
       PATTERN QUEST
    ========================= */
    const patternQuestQuestions = [
        { pattern: ['▲','■','●','▲','■','●','?'], answer: '▲', hint: "The pattern repeats." },
        { pattern: ['★','☆','★','?','★','☆'], answer: '☆', hint: "Follow the star sequence." }
    ];

    const patternQuest = () => {
        state.currentQuestionIndex =0;
        const container = document.getElementById('game-container');

        const renderPattern = () => {
            const q = patternQuestQuestions[state.currentQuestionIndex];
            container.innerHTML = `
                <h2 class="section-title">Pattern Quest</h2>
                <p>Find the missing symbol in the sequence:</p>
                <p>${q.pattern.join(' ')}</p>
                <input type="text" id="pattern-input" placeholder="Enter missing symbol"/>
                <div class="game-buttons">
                  <button class="btn-primary" id="check-pattern">Check</button>
                  <button class="btn-warning" id="hint-btn">Hint</button>
                  <button class="btn-secondary" id="next-btn">Next</button>
                  <button class="btn-danger" id="reset-btn">Reset</button>
                </div>
                <p id="pattern-feedback"></p>
            `;

            document.getElementById('check-pattern').onclick = () => {
                const answer = document.getElementById('pattern-input').value.trim();
                const feedback = document.getElementById('pattern-feedback');
                if(answer === q.answer){
                    feedback.textContent = '🎉 Correct! +10 points';
                    state.player.score +=10;
                    showScore();
                } else {
                    feedback.textContent = '❌ Wrong! Try again.';
                }
            };

            document.getElementById('hint-btn').onclick = () => {
                document.getElementById('pattern-feedback').textContent = `💡 Hint: ${q.hint}`;
            };

            document.getElementById('next-btn').onclick = () => {
                state.currentQuestionIndex = (state.currentQuestionIndex+1)%patternQuestQuestions.length;
                renderPattern();
            };

            document.getElementById('reset-btn').onclick = () => {
                state.player.score =0;
                showScore();
                renderPattern();
            };
        };

        renderPattern();
    };

    /* =========================
       MATH MATCH
    ========================= */
    const mathMatchQuestions = [
        { q: '5 + 3 = ?', answer: 8, hint: 'Sum of 5 and 3.' },
        { q: '7 - 4 = ?', answer: 3, hint: 'Subtract 4 from 7.' },
        { q: '6 + 2 = ?', answer: 8, hint: 'Sum of 6 and 2.' }
    ];

    const mathMatch = () => {
        state.currentQuestionIndex =0;
        const container = document.getElementById('game-container');

        const renderMath = () => {
            const q = mathMatchQuestions[state.currentQuestionIndex];
            container.innerHTML = `
                <h2 class="section-title">Math Match</h2>
                <p>${q.q}</p>
                <input type="number" id="math-input" placeholder="Enter answer"/>
                <div class="game-buttons">
                  <button class="btn-primary" id="check-math">Check</button>
                  <button class="btn-warning" id="hint-btn">Hint</button>
                  <button class="btn-secondary" id="next-btn">Next</button>
                  <button class="btn-danger" id="reset-btn">Reset</button>
                </div>
                <p id="math-feedback"></p>
            `;

            document.getElementById('check-math').onclick = () => {
                const answer = parseInt(document.getElementById('math-input').value);
                const feedback = document.getElementById('math-feedback');
                if(answer === q.answer){
                    feedback.textContent = '🎉 Correct! +10 points';
                    state.player.score +=10;
                    showScore();
                } else {
                    feedback.textContent = '❌ Wrong! Try again.';
                }
            };

            document.getElementById('hint-btn').onclick = () => {
                document.getElementById('math-feedback').textContent = `💡 Hint: ${q.hint}`;
            };

            document.getElementById('next-btn').onclick = () => {
                state.currentQuestionIndex = (state.currentQuestionIndex+1)%mathMatchQuestions.length;
                renderMath();
            };

            document.getElementById('reset-btn').onclick = () => {
                state.player.score =0;
                showScore();
                renderMath();
            };
        };

        renderMath();
    };

    /* =========================
       BUTTON EVENT LISTENERS
    ========================= */
    document.getElementById('start-numberStory')?.addEventListener('click', () => startGame('numberStory'));
    document.getElementById('start-puzzleBoxes')?.addEventListener('click', () => startGame('puzzleBoxes'));
    document.getElementById('start-patternQuest')?.addEventListener('click', () => startGame('patternQuest'));
    document.getElementById('start-mathMatch')?.addEventListener('click', () => startGame('mathMatch'));

    showScore();
});
