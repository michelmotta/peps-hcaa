import { initQuizEditor } from './quiz-editor';
import { initQuizPlayer } from './quiz-player';

export class QuizModule {
    static init() {
        const quizDataInput = document.getElementById('avaliacaoJson');
        let initialData = [];

        if (quizDataInput?.value && quizDataInput.value.trim() !== '[]' && quizDataInput.value.trim() !== '') {
            try {
                initialData = JSON.parse(quizDataInput.value);
            } catch (e) {
                console.error('Could not parse quiz JSON. Initializing an empty editor.', e);
            }
        }

        initQuizEditor(initialData);
        initQuizPlayer();
    }
}