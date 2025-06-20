// Import necessary modules
import './bootstrap';
import Alpine from 'alpinejs';

// Make Alpine available globally
window.Alpine = Alpine;
Alpine.start();

// Forum App Class
class ForumApp {
    constructor() {
        this.setupEventListeners();
        this.setupComponents();
    }

    init() {
        console.log('Forum App initialized');
        this.setupCSRF();
        this.setupLanguage();
        this.setupTheme();
        this.setupNotifications();
    }

    // CSRF Setup
    setupCSRF() {
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
        }
    }

    // Language Management
    setupLanguage() {
        this.currentLanguage = document.documentElement.lang || 'en';
        this.isRTL = ['ar', 'he', 'fa'].includes(this.currentLanguage);

        if (this.isRTL) {
            document.documentElement.setAttribute('dir', 'rtl');
            document.body.classList.add('rtl');
        }
    }

    switchLanguage(lang) {
        // Store language preference
        localStorage.setItem('forum_language', lang);

        // Update page language
        document.documentElement.lang = lang;

        // Handle RTL languages
        const rtlLanguages = ['ar', 'he', 'fa'];
        if (rtlLanguages.includes(lang)) {
            document.documentElement.setAttribute('dir', 'rtl');
            document.body.classList.add('rtl');
        } else {
            document.documentElement.setAttribute('dir', 'ltr');
            document.body.classList.remove('rtl');
        }

        // Reload page to apply language changes
        window.location.reload();
    }

    // Theme Management
    setupTheme() {
        this.theme = localStorage.getItem('forum_theme') || 'light';
        this.applyTheme(this.theme);
    }

    applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('forum_theme', theme);
        this.theme = theme;
    }

    toggleTheme() {
        const newTheme = this.theme === 'light' ? 'dark' : 'light';
        this.applyTheme(newTheme);
    }

    // Notification System
    setupNotifications() {
        this.notifications = [];
        this.createNotificationContainer();
    }

    createNotificationContainer() {
        if (!document.getElementById('notification-container')) {
            const container = document.createElement('div');
            container.id = 'notification-container';
            container.className = 'fixed top-4 right-4 z-50 space-y-2';
            document.body.appendChild(container);
        }
    }

    showNotification(message, type = 'info', duration = 5000) {
        const notification = document.createElement('div');
        const id = 'notification-' + Date.now();

        const bgColor = {
            'success': 'bg-green-500',
            'error': 'bg-red-500',
            'warning': 'bg-yellow-500',
            'info': 'bg-blue-500'
        }[type] || 'bg-blue-500';

        notification.id = id;
        notification.className = `${bgColor} text-white px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 ease-in-out translate-x-full opacity-0`;
        notification.innerHTML = `
            <div class="flex items-center justify-between">
                <p class="font-medium">${message}</p>
                <button onclick="forumApp.closeNotification('${id}')" class="ml-4 text-white hover:text-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;

        const container = document.getElementById('notification-container');
        container.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full', 'opacity-0');
        }, 100);

        // Auto-close
        setTimeout(() => {
            this.closeNotification(id);
        }, duration);
    }

    closeNotification(id) {
        const notification = document.getElementById(id);
        if (notification) {
            notification.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }
    }

    // Event Listeners Setup
    setupEventListeners() {
        // Navigation
        this.setupNavigation();

        // Forms
        this.setupForms();

        // Search
        this.setupSearch();

        // Language switcher
        this.setupLanguageSwitcher();

        // Dropdowns
        this.setupDropdowns();

        // Modal handling
        this.setupModals();

        // Topic interactions
        this.setupTopicInteractions();

        // Real-time updates
        this.setupRealTimeUpdates();
    }

    setupNavigation() {
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
                mobileMenu.classList.toggle('animate-slide-in-top');
            });
        }

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    setupForms() {
        // Auto-save drafts
        this.setupDraftSaving();

        // Form validation
        this.setupFormValidation();

        // Character counters
        this.setupCharacterCounters();

        // File upload handling
        this.setupFileUploads();
    }

    setupDraftSaving() {
        const textareas = document.querySelectorAll('textarea[data-draft-key]');

        textareas.forEach(textarea => {
            const draftKey = textarea.dataset.draftKey;

            // Load saved draft
            const savedDraft = localStorage.getItem(`draft_${draftKey}`);
            if (savedDraft && !textarea.value) {
                textarea.value = savedDraft;
                this.showNotification('Draft restored', 'info', 3000);
            }

            // Auto-save on input
            let saveTimeout;
            textarea.addEventListener('input', () => {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(() => {
                    if (textarea.value.trim()) {
                        localStorage.setItem(`draft_${draftKey}`, textarea.value);
                    } else {
                        localStorage.removeItem(`draft_${draftKey}`);
                    }
                }, 2000);
            });

            // Clear draft on form submit
            const form = textarea.closest('form');
            if (form) {
                form.addEventListener('submit', () => {
                    localStorage.removeItem(`draft_${draftKey}`);
                });
            }
        });
    }

    setupFormValidation() {
        const forms = document.querySelectorAll('form[data-validate]');

        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                }
            });

            // Real-time validation
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('blur', () => {
                    this.validateField(input);
                });
            });
        });
    }

    validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input, textarea, select');

        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        return isValid;
    }

    validateField(field) {
        const rules = field.dataset.validate?.split('|') || [];
        let isValid = true;
        let errorMessage = '';

        for (const rule of rules) {
            const [ruleName, ruleValue] = rule.split(':');

            switch (ruleName) {
                case 'required':
                    if (!field.value.trim()) {
                        isValid = false;
                        errorMessage = 'This field is required';
                    }
                    break;
                case 'min':
                    if (field.value.length < parseInt(ruleValue)) {
                        isValid = false;
                        errorMessage = `Minimum ${ruleValue} characters required`;
                    }
                    break;
                case 'max':
                    if (field.value.length > parseInt(ruleValue)) {
                        isValid = false;
                        errorMessage = `Maximum ${ruleValue} characters allowed`;
                    }
                    break;
                case 'email':
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (field.value && !emailRegex.test(field.value)) {
                        isValid = false;
                        errorMessage = 'Please enter a valid email address';
                    }
                    break;
            }

            if (!isValid) break;
        }

        this.showFieldError(field, isValid ? '' : errorMessage);
        return isValid;
    }

    showFieldError(field, message) {
        // Remove existing error
        const existingError = field.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }

        if (message) {
            field.classList.add('border-red-300');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error text-red-500 text-sm mt-1';
            errorDiv.textContent = message;
            field.parentNode.appendChild(errorDiv);
        } else {
            field.classList.remove('border-red-300');
        }
    }

    setupCharacterCounters() {
        document.querySelectorAll('[data-char-limit]').forEach(element => {
            const limit = parseInt(element.dataset.charLimit);
            const counter = document.createElement('div');
            counter.className = 'text-sm text-gray-500 mt-1 text-right';
            element.parentNode.appendChild(counter);

            const updateCounter = () => {
                const remaining = limit - element.value.length;
                counter.textContent = `${remaining} characters remaining`;
                counter.className = remaining < 0
                    ? 'text-sm text-red-500 mt-1 text-right'
                    : 'text-sm text-gray-500 mt-1 text-right';
            };

            element.addEventListener('input', updateCounter);
            updateCounter();
        });
    }

    setupFileUploads() {
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', (e) => {
                const files = Array.from(e.target.files);
                const preview = input.parentNode.querySelector('.file-preview');

                if (preview) {
                    preview.innerHTML = '';
                    files.forEach(file => {
                        const fileDiv = document.createElement('div');
                        fileDiv.className = 'flex items-center space-x-2 p-2 bg-gray-50 rounded';
                        fileDiv.innerHTML = `
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="text-sm text-gray-700">${file.name}</span>
                            <span class="text-xs text-gray-500">(${this.formatFileSize(file.size)})</span>
                        `;
                        preview.appendChild(fileDiv);
                    });
                }
            });
        });
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    setupSearch() {
        const searchInput = document.querySelector('input[name="q"]');
        const searchForm = document.querySelector('form[action*="search"]');
        const searchButton = searchForm?.querySelector('button[type="submit"]');
        const advancedSearchToggle = document.querySelector('button[onclick*="toggleAdvanced"]');
        const advancedSearch = document.getElementById('advanced-search');

        if (searchInput) {
            // Focus search input on page load if on search page
            if (window.location.pathname.includes('/search')) {
                searchInput.focus();

                // Show advanced search if any filters are set
                if (advancedSearch) {
                    const hasFilters = ['author', 'category', 'date_from', 'date_to'].some(param =>
                        new URLSearchParams(window.location.search).has(param)
                    );

                    if (hasFilters) {
                        advancedSearch.classList.remove('hidden');
                    }
                }
            }

            // Handle search form submission
            if (searchForm) {
                searchForm.addEventListener('submit', (e) => {
                    const query = searchInput.value.trim();

                    if (!query) {
                        e.preventDefault();
                        searchInput.focus();
                        return false;
                    }

                    // Disable button to prevent double submission
                    if (searchButton) {
                        searchButton.disabled = true;
                        searchButton.innerHTML = '🔍 Поиск...';
                    }

                    // Continue with normal form submission
                    return true;
                });
            }

            // Handle date range validation
            const dateFrom = document.getElementById('date_from');
            const dateTo = document.getElementById('date_to');

            if (dateFrom && dateTo) {
                dateFrom.addEventListener('change', function () {
                    if (dateTo.value && new Date(dateFrom.value) > new Date(dateTo.value)) {
                        dateTo.value = dateFrom.value;
                    }
                });

                dateTo.addEventListener('change', function () {
                    if (dateFrom.value && new Date(dateTo.value) < new Date(dateFrom.value)) {
                        dateFrom.value = dateTo.value;
                    }
                });
            }
        }
    }

    async performSearch(query, resultsContainer) {
        if (!resultsContainer || !query) return;

        try {
            // Show loading indicator
            resultsContainer.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Поиск...</p></div>';

            // Get form data if we're on the search page
            let formData = {};
            const searchForm = document.querySelector('form[action*="search"]');
            if (searchForm) {
                const formElements = searchForm.elements;
                for (let i = 0; i < formElements.length; i++) {
                    const element = formElements[i];
                    if (element.name && element.name !== 'q') {
                        if (element.type === 'checkbox') {
                            if (element.checked) {
                                if (formData[element.name]) {
                                    formData[element.name].push(element.value);
                                } else {
                                    formData[element.name] = [element.value];
                                }
                            }
                        } else if (element.value) {
                            formData[element.name] = element.value;
                        }
                    }
                }
            }

            // Build query parameters
            const params = new URLSearchParams();
            params.append('q', query);

            // Add additional form parameters
            for (const [key, value] of Object.entries(formData)) {
                if (Array.isArray(value)) {
                    value.forEach(v => params.append(`${key}[]`, v));
                } else {
                    params.append(key, value);
                }
            }

            const response = await fetch(`/search?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`Search request failed with status: ${response.status}`);
            }

            const data = await response.json();
            this.displaySearchResults(data, resultsContainer);
        } catch (error) {
            console.error('Search error:', error);
            resultsContainer.innerHTML = `
                <div class="alert alert-danger">
                    <p>Произошла ошибка при поиске. Пожалуйста, попробуйте еще раз.</p>
                    <small class="text-muted">${error.message}</small>
                </div>
            `;
        }
    }

    displaySearchResults(data, container) {
        if (!data.results || data.results.length === 0) {
            container.innerHTML = `
                <div class="text-center py-6">
                    <div class="text-4xl mb-3">🔍</div>
                    <h3 class="text-xl font-semibold mb-2">Ничего не найдено</h3>
                    <p class="text-gray-600">Попробуйте изменить параметры поиска</p>
                </div>
            `;
            return;
        }

        // Redirect to search page with results
        window.location.href = `/search?${new URLSearchParams(window.location.search)}`;
    }

    setupLanguageSwitcher() {
        document.querySelectorAll('[data-language]').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const language = button.dataset.language;
                this.switchLanguage(language);
            });
        });
    }

    setupDropdowns() {
        document.querySelectorAll('[data-dropdown]').forEach(dropdown => {
            const trigger = dropdown.querySelector('[data-dropdown-trigger]');
            const menu = dropdown.querySelector('[data-dropdown-menu]');

            if (trigger && menu) {
                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    menu.classList.toggle('hidden');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', () => {
                    menu.classList.add('hidden');
                });
            }
        });
    }

    setupModals() {
        // Modal triggers
        document.querySelectorAll('[data-modal-trigger]').forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                const modalId = trigger.dataset.modalTrigger;
                this.openModal(modalId);
            });
        });

        // Modal close buttons
        document.querySelectorAll('[data-modal-close]').forEach(closeBtn => {
            closeBtn.addEventListener('click', () => {
                this.closeModal();
            });
        });

        // Close modal on backdrop click
        document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
            backdrop.addEventListener('click', (e) => {
                if (e.target === backdrop) {
                    this.closeModal();
                }
            });
        });

        // Close modal on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeModal();
            }
        });
    }

    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            modal.querySelector('.modal-content')?.classList.add('animate-fade-in');
        }
    }

    closeModal() {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.classList.add('hidden');
        });
        document.body.classList.remove('overflow-hidden');
    }

    setupTopicInteractions() {
        // Like/dislike buttons
        document.querySelectorAll('.like-btn, .dislike-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                const postId = btn.dataset.postId;
                const action = btn.classList.contains('like-btn') ? 'like' : 'dislike';

                try {
                    const response = await fetch(`/posts/${postId}/${action}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();
                    if (data.success) {
                        this.updateLikeButtons(postId, data);
                    }
                } catch (error) {
                    console.error('Like/dislike error:', error);
                }
            });
        });

        // Quote buttons
        document.querySelectorAll('.quote-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const postContent = btn.closest('.post').querySelector('.post-content').textContent;
                const author = btn.dataset.author;
                const replyTextarea = document.getElementById('reply-content');

                if (replyTextarea) {
                    const quote = `[quote="${author}"]${postContent.trim()}[/quote]\n\n`;
                    replyTextarea.value += quote;
                    replyTextarea.focus();
                    replyTextarea.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        // Report buttons
        document.querySelectorAll('.report-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const postId = btn.dataset.postId;
                this.openReportModal(postId);
            });
        });
    }

    updateLikeButtons(postId, data) {
        const likeBtn = document.querySelector(`.like-btn[data-post-id="${postId}"]`);
        const dislikeBtn = document.querySelector(`.dislike-btn[data-post-id="${postId}"]`);

        if (likeBtn) {
            likeBtn.querySelector('.like-count').textContent = data.likes;
            likeBtn.classList.toggle('active', data.user_liked);
        }

        if (dislikeBtn) {
            dislikeBtn.querySelector('.dislike-count').textContent = data.dislikes;
            dislikeBtn.classList.toggle('active', data.user_disliked);
        }
    }

    openReportModal(postId) {
        // Implementation for report modal
        const modal = document.getElementById('report-modal');
        if (modal) {
            document.getElementById('report-post-id').value = postId;
            this.openModal('report-modal');
        }
    }

    setupRealTimeUpdates() {
        // Check for new posts every 30 seconds
        setInterval(() => {
            this.checkForUpdates();
        }, 30000);

        // Update user online status
        this.updateUserStatus();
        setInterval(() => {
            this.updateUserStatus();
        }, 60000);
    }

    async checkForUpdates() {
        const topicId = document.querySelector('meta[name="topic-id"]')?.content;
        if (!topicId) return;

        try {
            const response = await fetch(`/topics/${topicId}/check-updates`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            if (data.new_posts > 0) {
                this.showNotification(`${data.new_posts} new post(s) available`, 'info');
            }
        } catch (error) {
            console.error('Update check error:', error);
        }
    }

    async updateUserStatus() {
        try {
            await fetch('/user/update-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
        } catch (error) {
            console.error('Status update error:', error);
        }
    }

    // Component Setup
    setupComponents() {
        this.setupTabs();
        this.setupAccordions();
        this.setupTooltips();
        this.setupLazyLoading();
    }

    setupTabs() {
        document.querySelectorAll('[data-tabs]').forEach(tabContainer => {
            const tabs = tabContainer.querySelectorAll('[data-tab]');
            const panels = tabContainer.querySelectorAll('[data-tab-panel]');

            tabs.forEach(tab => {
                tab.addEventListener('click', (e) => {
                    e.preventDefault();
                    const targetPanel = tab.dataset.tab;

                    // Deactivate all tabs and panels
                    tabs.forEach(t => t.classList.remove('active'));
                    panels.forEach(p => p.classList.add('hidden'));

                    // Activate current tab and panel
                    tab.classList.add('active');
                    document.getElementById(targetPanel)?.classList.remove('hidden');
                });
            });
        });
    }

    setupAccordions() {
        document.querySelectorAll('[data-accordion]').forEach(accordion => {
            const triggers = accordion.querySelectorAll('[data-accordion-trigger]');

            triggers.forEach(trigger => {
                trigger.addEventListener('click', () => {
                    const content = trigger.nextElementSibling;
                    const isOpen = !content.classList.contains('hidden');

                    // Close all other accordion items
                    triggers.forEach(t => {
                        if (t !== trigger) {
                            t.nextElementSibling.classList.add('hidden');
                            t.setAttribute('aria-expanded', 'false');
                        }
                    });

                    // Toggle current item
                    content.classList.toggle('hidden', isOpen);
                    trigger.setAttribute('aria-expanded', !isOpen);
                });
            });
        });
    }

    setupTooltips() {
        document.querySelectorAll('[data-tooltip]').forEach(element => {
            let tooltip;

            element.addEventListener('mouseenter', () => {
                const text = element.dataset.tooltip;
                tooltip = document.createElement('div');
                tooltip.className = 'absolute z-50 bg-gray-900 text-white text-xs rounded py-1 px-2 pointer-events-none';
                tooltip.textContent = text;

                document.body.appendChild(tooltip);

                const rect = element.getBoundingClientRect();
                tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
                tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
            });

            element.addEventListener('mouseleave', () => {
                if (tooltip) {
                    tooltip.remove();
                    tooltip = null;
                }
            });
        });
    }

    setupLazyLoading() {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    observer.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
}

// Initialize the forum app
document.addEventListener('DOMContentLoaded', function () {
    window.forumApp = new ForumApp();
    window.forumApp.init();
});

// Service Worker registration for offline functionality
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                console.log('SW registered: ', registration);
            })
            .catch(registrationError => {
                console.log('SW registration failed: ', registrationError);
            });
    });
}

// Export for external use
export default ForumApp;

// Import Alpine.js if not globally loaded
document.addEventListener('DOMContentLoaded', function () {
    // Initialize Alpine.js related features
    window.openReportForm = function (type, id) {
        const form = document.getElementById('report-form');
        if (!form) return;

        document.getElementById('reportable_type').value = type;
        document.getElementById('reportable_id').value = id;

        // Use Alpine.js if available
        if (window.Alpine) {
            const modal = Alpine.$data(form);
            if (modal) {
                modal.open = true;
            } else {
                console.error('Alpine data not found for report form');
                form.style.display = 'block';
            }
        } else {
            console.error('Alpine.js not initialized');
            form.style.display = 'block';
        }
    };
});