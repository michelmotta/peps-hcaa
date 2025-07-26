import { initQuizEditor } from './quiz-editor';
import { initQuizPlayer } from './quiz-player';

export class QuizModule {
    static init() {
        this.initQuizFromJson();
        initQuizPlayer();
    }
    
    static initQuizFromJson() {
        const quizJson = document.getElementById('avaliacaoJson')?.value;
        if (quizJson) {
            try {
                const data = JSON.parse(quizJson);
                initQuizEditor(data);
            } catch (e) {
                console.error('Erro ao carregar avaliação salva:', e);
            }
        }
    }
}