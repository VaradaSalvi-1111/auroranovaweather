/**
 * AI Voice Assistant - Standalone Version
 * No Node.js required - Direct Gemini API integration
 * Version: 2.0.0
 */

class VoiceAssistant {
    constructor(config = {}) {
        // Configuration
        this.config = {
            geminiApiKey: config.geminiApiKey || 'AIzaSyCdBnSmxpy1XNAhOn36KiO2ap_1QOXXtDU', // Set your API key here
            language: config.language || 'en-US',
            continuous: false,
            interimResults: false,
            maxAlternatives: 1
        };

        // Intent mapping
        this.intentMap = {
            'home': {
                page: 'home.php',
                keywords: ['home', 'homepage', 'main page', 'dashboard', 'home page']
            },
            'index': {
                page: 'index.php',
                keywords: ['index', 'landing', 'start', 'beginning', 'first page']
            },
            'login': {
                page: 'login.php',
                keywords: ['login', 'log in', 'sign in', 'signin']
            },
            'register': {
                page: 'register.php',
                keywords: ['register', 'signup', 'sign up', 'create account', 'registration']
            },
            'profile': {
                page: 'profile.php',
                keywords: ['profile', 'my profile', 'account', 'user profile', 'my account']
            },
            'favorites': {
                page: 'favorites.php',
                keywords: ['favorites', 'favourite', 'saved', 'bookmarks', 'wishlist']
            },
            'contact': {
                page: 'contact.php',
                keywords: ['contact', 'contact us', 'reach us', 'get in touch', 'contactus']
            },
            'chat': {
                page: 'chat',
                keywords: ['chat', 'message', 'messages', 'messaging', 'conversation']
            }
        };

        // State management
        this.isListening = false;
        this.recognition = null;
        this.synth = window.speechSynthesis;
        
        // DOM elements
        this.elements = {};

        // Initialize
        this.init();
    }

    /**
     * Initialize the voice assistant
     */
    init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }
    }

    /**
     * Setup after DOM is ready
     */
    setup() {
        this.createUI();
        this.initSpeechRecognition();
        this.attachEventListeners();
        this.checkApiKey();
        console.log('✓ Voice Assistant initialized (Standalone Mode)');
    }

    /**
     * Check if API key is configured
     */
    checkApiKey() {
        if (!this.config.geminiApiKey || this.config.geminiApiKey === '') {
            console.warn('⚠️ Gemini API Key not configured. Using fallback intent matching.');
            console.warn('Set your API key: voiceAssistant.config.geminiApiKey = "YOUR_KEY"');
        }
    }

    /**
     * Create floating UI
     */
    createUI() {
        if (document.getElementById('voice-assistant-container')) {
            return;
        }

        const container = document.createElement('div');
        container.id = 'voice-assistant-container';
        container.innerHTML = `
            <div class="voice-assistant-btn" id="voice-assistant-btn" title="Click to speak">
                <svg class="mic-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path>
                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                    <line x1="12" y1="19" x2="12" y2="23"></line>
                    <line x1="8" y1="23" x2="16" y2="23"></line>
                </svg>
                <div class="pulse-ring"></div>
            </div>

            <div class="voice-assistant-panel" id="voice-assistant-panel">
                <div class="panel-header">
                    <h3>🎤 AI Voice Assistant</h3>
                    <button class="close-btn" id="close-panel" aria-label="Close">×</button>
                </div>
                <div class="panel-body">
                    <div class="status-indicator" id="status-indicator">
                        <span class="status-dot"></span>
                        <span class="status-text">Ready to listen</span>
                    </div>
                    <div class="command-display" id="command-display">
                        <p class="placeholder">Click the microphone and say a command...</p>
                    </div>
                    <div class="suggestions">
                        <p class="suggestions-title">Try saying:</p>
                        <div class="suggestion-chips">
                            <span class="chip" data-command="open home page">🏠 "Open home page"</span>
                            <span class="chip" data-command="go to profile">👤 "Go to profile"</span>
                            <span class="chip" data-command="show login">🔐 "Show login"</span>
                            <span class="chip" data-command="open chat">💬 "Open chat"</span>
                            <span class="chip" data-command="go to contact">📧 "Go to contact"</span>
                            <span class="chip" data-command="show favorites">⭐ "Show favorites"</span>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <small>Powered by Google Gemini AI</small>
                </div>
            </div>
        `;

        document.body.appendChild(container);

        this.elements = {
            container: container,
            btn: document.getElementById('voice-assistant-btn'),
            panel: document.getElementById('voice-assistant-panel'),
            closeBtn: document.getElementById('close-panel'),
            statusIndicator: document.getElementById('status-indicator'),
            commandDisplay: document.getElementById('command-display')
        };
    }

    /**
     * Initialize Speech Recognition
     */
    initSpeechRecognition() {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        
        if (!SpeechRecognition) {
            this.showError('Speech recognition not supported. Please use Chrome, Edge, or Safari.');
            console.error('Speech Recognition API not available');
            return;
        }

        this.recognition = new SpeechRecognition();
        this.recognition.continuous = this.config.continuous;
        this.recognition.interimResults = this.config.interimResults;
        this.recognition.lang = this.config.language;
        this.recognition.maxAlternatives = this.config.maxAlternatives;

        this.recognition.onstart = () => this.onRecognitionStart();
        this.recognition.onresult = (event) => this.onRecognitionResult(event);
        this.recognition.onerror = (event) => this.onRecognitionError(event);
        this.recognition.onend = () => this.onRecognitionEnd();
    }

    /**
     * Attach event listeners
     */
    attachEventListeners() {
        this.elements.btn.addEventListener('click', () => this.toggleListening());
        this.elements.closeBtn.addEventListener('click', () => this.closePanel());

        document.querySelectorAll('.suggestion-chips .chip').forEach(chip => {
            chip.addEventListener('click', (e) => {
                const command = e.currentTarget.getAttribute('data-command');
                this.displayCommand(command);
                this.processCommand(command);
            });
        });
    }

    /**
     * Toggle listening state
     */
    toggleListening() {
        if (!this.recognition) {
            this.showError('Speech recognition not available');
            return;
        }

        if (this.isListening) {
            this.stopListening();
        } else {
            this.startListening();
        }
    }

    /**
     * Start listening
     */
    startListening() {
        try {
            this.recognition.start();
            this.elements.btn.classList.add('listening');
            this.elements.panel.classList.add('active');
            this.updateStatus('🎤 Listening...', 'listening');
            this.isListening = true;
        } catch (error) {
            console.error('Start listening error:', error);
            this.showError('Could not start listening. Please try again.');
        }
    }

    /**
     * Stop listening
     */
    stopListening() {
        if (this.recognition) {
            this.recognition.stop();
        }
        this.elements.btn.classList.remove('listening');
        this.updateStatus('Ready to listen', 'ready');
        this.isListening = false;
    }

    /**
     * Recognition callbacks
     */
    onRecognitionStart() {
        console.log('🎤 Voice recognition started');
    }

    onRecognitionResult(event) {
        const result = event.results[0];
        const transcript = result[0].transcript;
        const confidence = result[0].confidence;

        console.log(`Recognized: "${transcript}" (confidence: ${(confidence * 100).toFixed(1)}%)`);

        this.updateStatus('🤔 Processing...', 'processing');
        this.displayCommand(transcript);
        this.processCommand(transcript);
    }

    onRecognitionError(event) {
        console.error('Recognition error:', event.error);
        
        const errorMessages = {
            'no-speech': 'No speech detected. Please try again.',
            'audio-capture': 'Microphone not found. Please check your device.',
            'not-allowed': 'Microphone permission denied. Please allow access.',
            'network': 'Network error. Please check your connection.',
            'aborted': 'Speech recognition aborted.'
        };

        const errorMessage = errorMessages[event.error] || `Error: ${event.error}`;
        this.showError(errorMessage);
        this.stopListening();
    }

    onRecognitionEnd() {
        console.log('Voice recognition ended');
        this.isListening = false;
        this.elements.btn.classList.remove('listening');
    }

    /**
     * Process voice command
     */
    async processCommand(command) {
        try {
            this.updateStatus('🤔 Analyzing command...', 'processing');

            let result;

            // Try Gemini API if key is available
            if (this.config.geminiApiKey && this.config.geminiApiKey !== '') {
                result = await this.analyzeWithGemini(command);
            } else {
                // Fallback to local matching
                result = this.fallbackIntentMatching(command);
            }

            this.handleIntentResult(result);

        } catch (error) {
            console.error('Process command error:', error);
            
            // Try fallback on error
            const fallbackResult = this.fallbackIntentMatching(command);
            this.handleIntentResult(fallbackResult);
        }
    }

    /**
     * Analyze intent using Gemini API
     */
    async analyzeWithGemini(command) {
        const prompt = `You are a navigation intent analyzer for a website.

Analyze this user command and determine which page they want to navigate to.

Available pages:
- home.php: home, homepage, main page, dashboard
- index.php: index, landing page, start
- login.php: login, sign in
- register.php: register, signup, create account
- profile.php: profile, my profile, account
- favorites.php: favorites, saved, bookmarks
- contact.php: contact, contact us
- chat: chat, messages, messaging

User command: "${command}"

Respond ONLY with valid JSON (no markdown, no code blocks):
{
  "intent": "page_name",
  "confidence": 0.95,
  "page": "filename.php",
  "response": "Opening profile page"
}

If not navigation-related, set intent to "unknown".`;

        const response = await fetch(
            `https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=${this.config.geminiApiKey}`,
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    contents: [{
                        parts: [{ text: prompt }]
                    }],
                    generationConfig: {
                        temperature: 0.3,
                        topK: 1,
                        topP: 1,
                        maxOutputTokens: 256,
                    }
                })
            }
        );

        if (!response.ok) {
            throw new Error(`Gemini API error: ${response.status}`);
        }

        const data = await response.json();
        
        if (data.candidates && data.candidates[0]) {
            const text = data.candidates[0].content.parts[0].text;
            
            // Extract JSON from response
            const jsonMatch = text.match(/\{[\s\S]*\}/);
            if (jsonMatch) {
                return JSON.parse(jsonMatch[0]);
            }
        }

        throw new Error('Invalid response from Gemini');
    }

    /**
     * Fallback intent matching (local)
     */
    fallbackIntentMatching(command) {
        const lowerCommand = command.toLowerCase().trim();
        
        // Remove common words
        const cleanCommand = lowerCommand
            .replace(/^(please|can you|could you|would you|i want to|i need to|show me|take me to|navigate to|go to|open|display|show)\s+/gi, '')
            .replace(/\s+(page|section)$/gi, '');

        console.log('Cleaned command:', cleanCommand);

        // Try exact and partial matches
        for (const [intent, config] of Object.entries(this.intentMap)) {
            for (const keyword of config.keywords) {
                if (cleanCommand.includes(keyword) || keyword.includes(cleanCommand)) {
                    return {
                        intent: intent,
                        confidence: 0.85,
                        page: config.page,
                        response: `Opening ${intent} page`,
                        method: 'fallback'
                    };
                }
            }
        }

        return {
            intent: 'unknown',
            confidence: 0.0,
            page: null,
            response: "I didn't understand that. Try saying 'open home page' or 'go to profile'.",
            method: 'fallback'
        };
    }

    /**
     * Handle intent recognition result
     */
    handleIntentResult(result) {
        const { intent, page, response, confidence, method } = result;

        console.log('Intent result:', { intent, page, confidence, method });

        if (intent === 'unknown' || !page) {
            this.speak(response);
            this.updateStatus('❌ Command not recognized', 'error');
            setTimeout(() => {
                this.updateStatus('Ready to listen', 'ready');
            }, 3000);
            return;
        }

        // Success feedback
        this.speak(response);
        this.updateStatus(`✅ ${response}...`, 'success');

        // Navigate after voice feedback
        setTimeout(() => {
            this.navigateToPage(page);
        }, 1500);
    }

    /**
     * Navigate to page
     */
    navigateToPage(page) {
        console.log(`🔗 Navigating to: ${page}`);
        
        // Special handling for chat
        if (page === 'chat') {
            // Try to find and trigger chat functionality
            if (typeof openChat === 'function') {
                openChat();
                this.closePanel();
                return;
            }
            
            const chatBtn = document.querySelector('[data-chat-trigger]') || 
                           document.querySelector('.chat-button') ||
                           document.querySelector('#chat-btn');
            
            if (chatBtn) {
                chatBtn.click();
                this.closePanel();
                return;
            }

            // If no chat function found, try loading chat.html or similar
            const chatPage = 'chat.html';
            if (this.pageExists(chatPage)) {
                window.location.href = chatPage;
                return;
            }
        }

        // Standard page navigation
        window.location.href = page;
    }

    /**
     * Check if page exists (simple check)
     */
    pageExists(page) {
        // This is a basic check - in production you might want to validate differently
        return true;
    }

    /**
     * Text to speech
     */
    speak(text) {
        this.synth.cancel();

        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = this.config.language;
        utterance.rate = 1.0;
        utterance.pitch = 1.0;
        utterance.volume = 1.0;

        // Load voices if not loaded
        let voices = this.synth.getVoices();
        
        if (voices.length === 0) {
            // Wait for voices to load
            this.synth.addEventListener('voiceschanged', () => {
                voices = this.synth.getVoices();
                this.setVoice(utterance, voices);
                this.synth.speak(utterance);
            }, { once: true });
        } else {
            this.setVoice(utterance, voices);
            this.synth.speak(utterance);
        }
    }

    /**
     * Set preferred voice
     */
    setVoice(utterance, voices) {
        // Try to find a good English voice
        const preferredVoice = 
            voices.find(v => v.lang === 'en-US' && v.name.includes('Female')) ||
            voices.find(v => v.lang === 'en-US') ||
            voices.find(v => v.lang.startsWith('en-')) ||
            voices[0];
        
        if (preferredVoice) {
            utterance.voice = preferredVoice;
        }
    }

    /**
     * Update status display
     */
    updateStatus(text, state = 'ready') {
        const indicator = this.elements.statusIndicator;
        indicator.className = `status-indicator ${state}`;
        indicator.querySelector('.status-text').textContent = text;
    }

    /**
     * Display recognized command
     */
    displayCommand(text) {
        const display = this.elements.commandDisplay;
        display.innerHTML = `
            <div class="command-text">
                <svg class="command-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                <p>"${text}"</p>
            </div>
        `;
    }

    /**
     * Show error message
     */
    showError(message) {
        const display = this.elements.commandDisplay;
        display.innerHTML = `
            <div class="error-message">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <p>${message}</p>
            </div>
        `;
        this.updateStatus('❌ Error', 'error');
    }

    /**
     * Close panel
     */
    closePanel() {
        this.elements.panel.classList.remove('active');
        this.stopListening();
    }

    /**
     * Set API key programmatically
     */
    setApiKey(apiKey) {
        this.config.geminiApiKey = apiKey;
        console.log('✓ Gemini API key updated');
    }

    /**
     * Add custom intent
     */
    addIntent(intent, page, keywords) {
        this.intentMap[intent] = {
            page: page,
            keywords: keywords
        };
        console.log(`✓ Added custom intent: ${intent}`);
    }

    /**
     * Destroy instance
     */
    destroy() {
        if (this.recognition) {
            this.recognition.stop();
        }
        this.synth.cancel();
        if (this.elements.container) {
            this.elements.container.remove();
        }
    }
}

// Auto-initialize
let voiceAssistant;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        voiceAssistant = new VoiceAssistant({
            geminiApiKey: 'AIzaSyCdBnSmxpy1XNAhOn36KiO2ap_1QOXXtDU' // Add your API key here or set it later
        });
    });
} else {
    voiceAssistant = new VoiceAssistant({
        geminiApiKey: 'AIzaSyCdBnSmxpy1XNAhOn36KiO2ap_1QOXXtDU' // Add your API key here or set it later
    });
}

// Make available globally
window.VoiceAssistant = VoiceAssistant;
window.voiceAssistant = voiceAssistant;