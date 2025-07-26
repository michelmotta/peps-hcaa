import Plyr from 'plyr';
import 'plyr/dist/plyr.css';

export class VideoModule {
    static init() {
        this.initPlyrWithTopics();
    }
    
    static initPlyrWithTopics() {
        const videoElement = document.querySelector('.js-player');
        if (!videoElement) return;
        
        const player = new Plyr(videoElement);
        const sentHistory = new Set();
        const topicItems = document.querySelectorAll('.topic-item[data-video]');
        
        if (!topicItems.length) return;
        
        const progressManager = new ProgressManager(topicItems);
        const topicManager = new TopicManager(player, topicItems, sentHistory, progressManager);
        
        topicManager.init();
    }
}

class ProgressManager {
    constructor(topicItems) {
        this.topicItems = topicItems;
        this.progressBar = document.querySelector('.course-progress-bar .progress-bar');
        this.progressText = document.querySelector('.course-progress-bar small');
    }
    
    updateProgressBar() {
        const total = this.topicItems.length;
        const watched = document.querySelectorAll('.topic-item.watched').length;
        const percent = Math.round((watched / total) * 100);
        
        if (this.progressBar) {
            this.progressBar.style.width = percent + '%';
            this.progressBar.setAttribute('aria-valuenow', percent);
        }
        if (this.progressText) {
            this.progressText.textContent = `${percent}% concluído`;
        }
    }
}

class TopicManager {
    constructor(player, topicItems, sentHistory, progressManager) {
        this.player = player;
        this.topicItems = topicItems;
        this.sentHistory = sentHistory;
        this.progressManager = progressManager;
        this.sentEvent = false;
        this.mustWatchPercentage = 0.1;
    }
    
    init() {
        this.setupFirstTopic();
        this.setupTopicClickHandlers();
        this.setupPlayerEvents();
        this.progressManager.updateProgressBar();
    }
    
    setupFirstTopic() {
        const firstItem = this.topicItems[0];
        if (firstItem) {
            firstItem.classList.add('active');
            const firstVideoUrl = firstItem.dataset.video;
            if (firstVideoUrl) {
                this.player.source = {
                    type: 'video',
                    sources: [{ src: firstVideoUrl, type: 'video/mp4' }],
                };
            }
        }
    }
    
    setupTopicClickHandlers() {
        this.topicItems.forEach(item => {
            item.addEventListener('click', () => {
                const videoUrl = item.dataset.video;
                if (!videoUrl) return;
                
                this.setActiveTopic(item);
                this.loadVideo(videoUrl);
            });
        });
    }
    
    setActiveTopic(activeItem) {
        this.topicItems.forEach(el => {
            el.classList.remove('active', 'playing');
            const icon = el.querySelector('.play-indicator');
            if (icon) icon.style.display = 'none';
        });
        
        activeItem.classList.add('active');
        const currentIcon = activeItem.querySelector('.play-indicator');
        if (currentIcon) currentIcon.style.display = 'inline';
    }
    
    loadVideo(videoUrl) {
        this.player.source = {
            type: 'video',
            sources: [{ src: videoUrl, type: 'video/mp4' }],
        };
        this.player.play();
    }
    
    setupPlayerEvents() {
        this.player.on('timeupdate', () => this.handleTimeUpdate());
        this.player.on('loadeddata', () => this.sentEvent = false);
        this.player.on('playing', () => this.handlePlaying());
        this.player.on('pause', () => this.handlePause());
        this.player.on('ended', () => this.handleEnded());
    }
    
    handleTimeUpdate() {
        const currentItem = document.querySelector('.topic-item.active');
        const topicId = currentItem?.dataset.topicId;
        
        const duration = this.player.duration;
        const currentTime = this.player.currentTime;
        
        if (!this.sentEvent && duration && currentTime >= duration * this.mustWatchPercentage) {
            this.sentEvent = true;
            this.sendPlayEvent(topicId, currentItem);
        }
    }
    
    handlePlaying() {
        this.topicItems.forEach(el => el.classList.remove('playing'));
        document.querySelector('.topic-item.active')?.classList.add('playing');
    }
    
    handlePause() {
        document.querySelector('.topic-item.active')?.classList.remove('playing');
    }
    
    handleEnded() {
        document.querySelector('.topic-item.active')?.classList.remove('playing');
    }
    
    sendPlayEvent(topicId, itemElement) {
        if (this.sentHistory.has(topicId)) return;
        
        itemElement.classList.add('watched');
        this.progressManager.updateProgressBar();
        
        axios.post(window.location.pathname + '/history', { topic_id: topicId })
            .then(() => {
                this.sentHistory.add(topicId);
            })
            .catch((error) => {
                console.error('Erro ao salvar histórico:', error);
            });
    }
}