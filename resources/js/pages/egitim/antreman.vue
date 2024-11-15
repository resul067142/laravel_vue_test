<template>
    <div id="app">
        <div v-if="!gameOver" class="game-container">
            <div class="score-timer">
                <div>Puan: {{ score }}</div>
                <div>Süre: {{ timer }}</div>
            </div>
            <div class="question">Bu nedir?
                <img :src="currentQuestion?.image || 'https://via.placeholder.com/150?text=Görsel+Yok'" alt="Question Image" class="question-image" />
            </div>
            <div class="options">
                <div class="option" v-for="(option, index) in currentOptions" :key="index" @click="checkAnswer(option)">
                    <img :src="option?.image || 'https://via.placeholder.com/100?text=No+Image'" :alt="option.name" class="option-image" />
                    <span>{{ option.name }}</span>
                </div>
            </div>
            <div class="score-timer">
                <div>Puan: {{ score }}</div>
                <div>Süre: {{ timer }}</div>
            </div>
            <div class="hints">
                <button @click="useHint" :disabled="hintUsed || hints <= 0" class="hint-button">İpucu Kullan (Kalan: {{ hints }})</button>
            </div>
        </div>
        <div v-else class="game-over-screen animated-end">
            <h2 class="game-over-title">Oyun Bitti!</h2>
            <div class="final-score">Toplam Puan: <span class="score-highlight">{{ score }}</span></div>
            <div class="result-details">
                <span class="correct-count">Doğru: {{ correctCount }}</span> |
                <span class="incorrect-count">Yanlış: {{ incorrectCount }}</span>
            </div>
            <button @click="resetGame" class="restart-button">Baştan Başla</button>
        </div>
        <div v-if="correctImages.length > 0 || incorrectImages.length > 0" class="answer-images">
            <div class="correct-wrong-container">
                <div class="correct-wrong-sections">
                    <div class="correct-images-section">
                        <h3>Doğru Yaptığınız Görseller:</h3>
                        <div class="correct-image-list">
                            <div v-for="(image, index) in correctImages" :key="'correct-' + index" class="correct-image-item">
                                <img :src="image" alt="Correct Answer Image" class="correct-image" />
                                <span>Doğru Yaptın!</span>
                            </div>
                        </div>
                    </div>
                    <div class="incorrect-images-section">
                        <h3>Yanlış Yaptığınız Görseller:</h3>
                        <div class="incorrect-image-list">
                            <div v-for="(image, index) in incorrectImages" :key="'incorrect-' + index" class="incorrect-image-item">
                                <img :src="image" alt="Incorrect Answer Image" class="incorrect-image" />
                                <span>Yanlış Yaptın!</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-if="leaderboard.length > 0" class="leaderboard-section">
        <h3>Liderlik Tablosu:</h3>
        <div class="leaderboard-container">
            <div v-for="(entry, index) in leaderboard" :key="index" class="leaderboard-card">
                <div class="leaderboard-rank">#{{ index + 1 }}</div>
                <div class="leaderboard-details">
                    <div class="leaderboard-score">Skor: {{ entry.score }}</div>
                    <div class="leaderboard-date">Tarih: {{ entry.date }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            // Adding a leaderboard feature to show top scores.
            leaderboard: [],
            options: [
                {
                    name: 'Havuç',
                    image: 'https://cdn.pixabay.com/photo/2016/03/05/19/02/carrots-1238250_1280.jpg'
                },
                {
                    name: 'Araba',
                    image: 'https://cdn.pixabay.com/photo/2012/05/29/00/43/car-49278_1280.jpg'
                },
                {
                    name: 'Kedi',
                    image: 'https://cdn.pixabay.com/photo/2017/02/20/18/03/cat-2083492_1280.jpg'
                },

                {
                    name: 'Çiçekler',
                    image: 'https://cdn.pixabay.com/photo/2014/02/27/16/10/flowers-276014_1280.jpg'
                }

            ],

            currentOptions: [],
            previousOptions: null,
            currentQuestion: null,
            score: 0,
            timer: 10,
            gameOver: false,
            hints: 3,
            hintUsed: false,
            correctCount: 0,
            incorrectCount: 0,
            incorrectImages: [],
            correctImages: [],
            bonusPoints: 0,
            level: 1,
            streak: 0,
            achievements: []
        };
    },
    created() {
        this.loadLeaderboard();
        this.startGame();
        this.startTimer();
    },
    methods: {
        // Load the leaderboard from localStorage or initialize it
        loadLeaderboard() {
            const savedLeaderboard = localStorage.getItem('leaderboard');
            if (savedLeaderboard) {
                this.leaderboard = JSON.parse(savedLeaderboard);
            }
        },
        // Save the leaderboard to localStorage
        saveLeaderboard() {
            localStorage.setItem('leaderboard', JSON.stringify(this.leaderboard));
        },
        // Update leaderboard when the game ends
        updateLeaderboard() {
            const playerResult = {score: this.score, date: new Date().toLocaleString()};
            this.leaderboard.push(playerResult);
            this.leaderboard.sort((a, b) => b.score - a.score);
            if (this.leaderboard.length > 5) {
                this.leaderboard.pop();
            }
            this.saveLeaderboard();
        },
        startGame() {
            this.selectRandomOptions();
            this.setCorrectAnswer();
        },
        startTimer() {
            this.timerInterval = setInterval(() => {
                if (this.timer > 0) {
                    this.timer--;
                } else {
                    this.endGame();
                }
            }, 1000);
        },
        selectRandomOptions() {
            let newOptions;
            do {
                newOptions = this.shuffleArray([...this.options]).slice(0, 4);
            } while (this.previousOptions && this.areOptionsEqual(newOptions, this.previousOptions));
            this.currentOptions = newOptions;
            this.previousOptions = newOptions;
        },
        areOptionsEqual(optionsA, optionsB) {
            return optionsA.every((option, index) => option.name === optionsB[index].name);
        },
        shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array;
        },
        setCorrectAnswer() {
            const randomIndex = Math.floor(Math.random() * this.currentOptions.length);
            this.currentOptions.forEach((option, index) => {
                option.isCorrect = index === randomIndex;
            });
            this.currentQuestion = this.currentOptions[randomIndex];
        },
        checkAnswer(option) {
            if (option.isCorrect) {
                this.score += 10;
                this.correctCount++;
                this.correctImages.push(option.image);
                this.streak++;
                if (this.streak % 3 === 0) {
                    this.bonusPoints += 5;
                    this.score += 5;
                    this.achievements.push(`3 doğru cevap serisi! +5 bonus puan!`);
                }
                if (this.correctCount % 5 === 0) {
                    this.level++;
                    this.achievements.push(`Seviye ${this.level} tamamlandı!`);
                }
            } else {
                this.score -= 5;
                this.incorrectImages.push(option.image);
                this.incorrectCount++;
                this.streak = 0;
            }
            this.hintUsed = false;
            this.selectRandomOptions();
            this.setCorrectAnswer();
        },
        useHint() {
            if (this.hints > 0 && !this.hintUsed) {
                this.hints--;
                this.hintUsed = true;
                const incorrectOptions = this.currentOptions.filter(option => !option.isCorrect);
                const optionToRemove = incorrectOptions[Math.floor(Math.random() * incorrectOptions.length)];
                this.currentOptions = this.currentOptions.filter(option => option !== optionToRemove);
            }
        },
        resetGame() {
            // Adding congratulatory messages based on the final score
            if (this.score >= 50) {
                alert('Harika! 50+ puan aldınız!');
            } else if (this.score >= 30) {
                alert('İyi iş! 30+ puan aldınız!');
            } else {
                alert('Daha fazla çalışmalısınız!');
            }
            clearInterval(this.timerInterval);
            this.score = 0;
            this.timer = 10;
            this.gameOver = false;
            this.hints = 3;
            this.hintUsed = false;
            this.correctCount = 0;
            this.incorrectCount = 0;
            this.incorrectImages = [];
            this.correctImages = [];
            this.bonusPoints = 0;
            this.level = 1;
            this.streak = 0;
            this.achievements = [];
            this.startGame();
            this.startTimer();
        },
        endGame() {
            this.updateLeaderboard();
            clearInterval(this.timerInterval);
            this.gameOver = true;
        }
    }
};
</script>

<style scoped>
#app {
    text-align: center;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    padding: 10px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    padding: 10px;
}

.game-container {
    animation: fadeIn 1s;
    background-color: #f0f4f8;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.question {
    font-size: 24px;
    margin-bottom: 20px;
    color: #333;
}

.question-image {
    max-width: 100%;
    height: auto;
    margin: 20px 0;
    border: 2px solid #333;
    border-radius: 10px;
}

.options {
    display: flex;
    justify-content: space-evenly;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 20px;
}

.option {
    cursor: pointer;
    padding: 10px;
    border: 1px solid #ccc;
    width: 120px;
    transition: background-color 0.3s, transform 0.3s;
    text-align: center;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    background-color: #fff;
}

.option:hover {
    background-color: #e0f7fa;
    transform: scale(1.05);
}

.option-image {
    max-width: 100%;
    height: 80px;
    object-fit: contain;
    border-radius: 5px;
}

.score-timer {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
    font-size: 20px;
    font-weight: bold;
    color: #333;
}

.hints {
    margin-top: 10px;
}

.hint-button {
    padding: 10px;
    background-color: #ffa726;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.hint-button:disabled {
    background-color: #ffcc80;
    cursor: not-allowed;
}

.hint-button:hover:not(:disabled) {
    background-color: #fb8c00;
}

.correct-images {
    margin-top: 20px;
    animation: fadeInUp 1s;
}

.correct-wrong-container {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    flex-wrap: wrap;
}

.correct-wrong-sections {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    width: 100%;
}

.correct-image-list,
.incorrect-image-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.correct-image-item,
.incorrect-image-item {
    text-align: center;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    background-color: #ffffff;
}

.correct-image,
.incorrect-image {
    max-width: 100px;
    height: auto;
    margin-bottom: 5px;
    border-radius: 5px;
}

.game-over-screen {
    background-color: #ffd966;
    padding: 15px;
    border-radius: 15px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    color: #333;
    animation: fadeInDown 1s;
    max-width: 400px;
    margin: 10px auto;
}

.game-over-title {
    font-size: 22px;
    margin-bottom: 15px;
}

.final-score {
    font-size: 24px;
    font-weight: bold;
    margin-top: 10px;
    color: #d32f2f;
}

.score-highlight {
    color: #e53935;
    font-weight: bold;
}

.result-details {
    font-size: 16px;
    margin-top: 10px;
    color: #444;
}

.correct-count,
.incorrect-count {
    font-weight: bold;
}

.restart-button {
    margin-top: 15px;
    padding: 10px 20px;
    font-size: 16px;
    background-color: #4caf50;
    color: white;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.3s;
}

.restart-button:hover {
    background-color: #388e3c;
    transform: scale(1.1);
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes fadeInUp {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes fadeInDown {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.animated-end {
    animation: fadeInDown 1s;
}

.leaderboard-section {
    margin-top: 20px;
    background-color: #f9f9f9;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.leaderboard-container {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.leaderboard-card {
    display: flex;
    align-items: center;
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 15px;
    transition: transform 0.3s, box-shadow 0.3s;
}

.leaderboard-card:hover {
    transform: scale(1.02);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.leaderboard-rank {
    font-size: 24px;
    font-weight: bold;
    color: #4caf50;
    margin-right: 20px;
}

.leaderboard-details {
    text-align: left;
}

.leaderboard-score {
    font-size: 20px;
    font-weight: bold;
}

.leaderboard-date {
    font-size: 16px;
    color: #666;
}
</style>
