import './bootstrap';
import './theme.min';

// Core modules
import { AlertManager } from './modules/AlertManager';
import { ComponentInitializer } from './modules/ComponentInitializer';

// Feature modules
import { QuizModule } from './modules/QuizModule';
import { VideoModule } from './modules/VideoModule';
import { FileUploadModule } from './modules/FileUploadModule';
import { FormModule } from './modules/FormModule';
import { FeedbackModule } from './modules/FeedbackModule';
import { VideoUploadModule } from './modules/VideoUploadModule';

// Global libraries setup
import { setupGlobalLibraries } from './utils/globalSetup';

// Initialize everything when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    setupGlobalLibraries();

    // Initialize all modules
    ComponentInitializer.init();
    QuizModule.init();
    VideoModule.init();
    FileUploadModule.init();
    FormModule.init();
    FeedbackModule.init();
    VideoUploadModule.init();
});

window.addEventListener('load', () => {
    AlertManager.showFeedbackAlert();
});