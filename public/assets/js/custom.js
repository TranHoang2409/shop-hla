document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar') || document.getElementById('adminSidebar');
    const content = document.getElementById('content');
    const topbar = document.getElementById('topbar');
    const toggleBtn = document.getElementById('toggleBtn') || document.querySelector('[data-admin-sidebar-toggle]');
    const mobileBtn = document.getElementById('mobileBtn');
    const overlay = document.getElementById('overlay') || document.getElementById('adminOverlay');
    const body = document.body;

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            if (window.innerWidth < 992) {
                sidebar.classList.add('mobile-show');
                body.classList.add('admin-sidebar-open');
                if (overlay) {
                    overlay.classList.add('show');
                }

                return;
            }

            sidebar.classList.toggle('collapsed');

            if (content) {
                content.classList.toggle('full');
            }

            if (topbar) {
                topbar.classList.toggle('full');
            }
        });
    }

    if (mobileBtn && sidebar) {
        mobileBtn.addEventListener('click', () => {
            sidebar.classList.add('mobile-show');
            body.classList.add('admin-sidebar-open');

            if (overlay) {
                overlay.classList.add('show');
            }
        });
    }

    if (overlay && sidebar) {
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('mobile-show');
            body.classList.remove('admin-sidebar-open');
            overlay.classList.remove('show');
        });
    }

    const navLinks = document.querySelectorAll('.sidebar .nav-link');

    if (navLinks.length > 0 && !document.body.classList.contains('admin-template-body')) {
        const currentPage = window.location.pathname.split('/').pop() || 'index.html';

        navLinks.forEach((link) => {
            link.classList.remove('active');

            if (link.getAttribute('href') === currentPage) {
                link.classList.add('active');
            }
        });
    }

    const flashItems = document.querySelectorAll('[data-store-flash]');

    flashItems.forEach((flash) => {
        const closeButton = flash.querySelector('[data-store-flash-close]');
        const hideFlash = () => {
            flash.classList.add('is-hiding');
            window.setTimeout(() => flash.remove(), 220);
        };

        if (closeButton) {
            closeButton.addEventListener('click', hideFlash);
        }

        window.setTimeout(hideFlash, 4200);
    });

    const paymentMethods = document.querySelectorAll('[data-payment-method]');
    const bankTransferBox = document.querySelector('[data-bank-transfer-box]');

    if (paymentMethods.length > 0 && bankTransferBox) {
        const syncPaymentBox = () => {
            const selected = document.querySelector('[data-payment-method]:checked');
            bankTransferBox.classList.toggle('d-none', selected?.value !== 'bank_transfer');
        };

        paymentMethods.forEach((input) => {
            input.addEventListener('change', syncPaymentBox);
        });

        syncPaymentBox();
    }

    const chatbot = document.querySelector('[data-chatbot]');

    if (chatbot) {
        const panel = chatbot.querySelector('[data-chatbot-panel]');
        const toggleButton = chatbot.querySelector('[data-chatbot-toggle]');
        const closeButton = chatbot.querySelector('[data-chatbot-close]');
        const resetButton = chatbot.querySelector('[data-chatbot-reset]');
        const form = chatbot.querySelector('[data-chatbot-form]');
        const input = chatbot.querySelector('[data-chatbot-input]');
        const messages = chatbot.querySelector('[data-chatbot-messages]');
        const suggestions = chatbot.querySelector('[data-chatbot-suggestions]');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const endpoint = chatbot.dataset.endpoint;
        const historyKey = 'hla_chatbot_history';
        const suggestionsKey = 'hla_chatbot_suggestions';
        const openKey = 'hla_chatbot_open';
        const maxStoredMessages = 30;
        const initialSuggestions = [
            { label: 'Router gia đình', message: 'Tư vấn router cho gia đình' },
            { label: 'Mesh WiFi', message: 'Tư vấn mesh WiFi phủ sóng toàn nhà' },
            { label: 'Bảo hành', message: 'Chính sách bảo hành như thế nào?' },
        ];

        const readJson = (key, fallback) => {
            try {
                const value = window.localStorage.getItem(key);
                return value ? JSON.parse(value) : fallback;
            } catch (error) {
                return fallback;
            }
        };

        const writeJson = (key, value) => {
            try {
                window.localStorage.setItem(key, JSON.stringify(value));
            } catch (error) {
                // localStorage can be disabled in private browsing or strict browser modes.
            }
        };

        const storedMessages = () => readJson(historyKey, []);

        const storeMessage = (content, type) => {
            const nextMessages = [...storedMessages(), { content, type }].slice(-maxStoredMessages);
            writeJson(historyKey, nextMessages);
        };

        const setOpen = (isOpen) => {
            chatbot.classList.toggle('is-open', isOpen);
            try {
                window.localStorage.setItem(openKey, isOpen ? '1' : '0');
            } catch (error) {
                // Ignore storage failures.
            }

            if (isOpen) {
                window.setTimeout(() => input?.focus(), 80);
            }
        };

        const appendMessage = (content, type, shouldStore = true) => {
            const bubble = document.createElement('div');
            bubble.className = `chatbot-message is-${type}`;
            bubble.textContent = content;
            messages.appendChild(bubble);
            messages.scrollTop = messages.scrollHeight;

            if (shouldStore) {
                storeMessage(content, type);
            }

            return bubble;
        };

        const setSuggestions = (items = [], shouldStore = true) => {
            suggestions.innerHTML = '';

            items.forEach((item) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = item.label;
                button.dataset.message = item.message;
                suggestions.appendChild(button);
            });

            if (shouldStore) {
                writeJson(suggestionsKey, items);
            }
        };

        const restoreChatbot = () => {
            const savedMessages = storedMessages();

            if (savedMessages.length > 0) {
                messages.innerHTML = '';
                savedMessages.forEach((item) => {
                    if (item?.content && ['bot', 'user'].includes(item.type)) {
                        appendMessage(item.content, item.type, false);
                    }
                });
            }

            const savedSuggestions = readJson(suggestionsKey, null);

            if (Array.isArray(savedSuggestions) && savedSuggestions.length > 0) {
                setSuggestions(savedSuggestions, false);
            }

            try {
                if (window.localStorage.getItem(openKey) === '1') {
                    setOpen(true);
                }
            } catch (error) {
                // Ignore storage failures.
            }
        };

        const resetChatbot = () => {
            try {
                window.localStorage.removeItem(historyKey);
                window.localStorage.removeItem(suggestionsKey);
            } catch (error) {
                // Ignore storage failures.
            }

            messages.innerHTML = '';
            appendMessage('Chào bạn, mình có thể tư vấn router, mesh WiFi, bảo hành, giao hàng và thanh toán.', 'bot', false);
            setSuggestions(initialSuggestions);
            input?.focus();
        };

        const sendMessage = async (message) => {
            const cleanMessage = message.trim();

            if (!cleanMessage) {
                return;
            }

            appendMessage(cleanMessage, 'user');
            input.value = '';
            input.disabled = true;

            const typing = appendMessage('Đang trả lời...', 'bot', false);

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ message: cleanMessage }),
                });

                const data = await response.json();
                if (!response.ok) {
                    typing.textContent = data.reply || 'Chatbot request failed';
                    storeMessage(typing.textContent, 'bot');
                    setSuggestions(data.suggestions || []);
                    return;
                }
                typing.textContent = data.reply || 'Mình chưa có câu trả lời phù hợp.';
                storeMessage(typing.textContent, 'bot');
                setSuggestions(data.suggestions || []);
            } catch (error) {
                typing.textContent = 'Xin lỗi, hiện trợ lý chưa phản hồi được. Bạn thử lại sau nhé.';
                storeMessage(typing.textContent, 'bot');
            } finally {
                input.disabled = false;
                input.focus();
                messages.scrollTop = messages.scrollHeight;
            }
        };

        restoreChatbot();

        toggleButton?.addEventListener('click', () => setOpen(!chatbot.classList.contains('is-open')));
        closeButton?.addEventListener('click', () => setOpen(false));
        resetButton?.addEventListener('click', resetChatbot);

        form?.addEventListener('submit', (event) => {
            event.preventDefault();
            sendMessage(input.value);
        });

        suggestions?.addEventListener('click', (event) => {
            const button = event.target.closest('button[data-message]');

            if (button) {
                sendMessage(button.dataset.message || button.textContent);
            }
        });
    }
});
