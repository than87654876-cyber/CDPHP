<!-- Gemini AI Chatbot Floating Widget -->
<div id="gemini-chat-widget">
    <!-- Chat Toggle Button -->
    <div id="gemini-chat-btn">
        <i class="bi bi-chat-dots-fill fs-4"></i>
    </div>

    <!-- Chat Window -->
    <div id="gemini-chat-window">
        <div id="gemini-chat-header">
            <span class="d-flex align-items-center gap-2">
                <img src="{{ asset('logo.jpg') }}" class="rounded-circle" style="width: 24px; height: 24px;">
                Trợ lý FOODDAILY AI
            </span>
            <button type="button" class="btn-close btn-close-white btn-sm" id="close-chat" aria-label="Close"></button>
        </div>
        <div id="gemini-chat-messages">
            <div class="chat-msg bot">
                Xin chào! Tôi là Trợ lý ảo FOODDAILY AI. Tôi có thể giúp gì cho bạn hôm nay?
            </div>
        </div>
        <div id="gemini-chat-input-area">
            <input type="text" id="gemini-chat-input" placeholder="Hỏi tôi món ăn ngon hôm nay..." autocomplete="off">
            <button id="gemini-chat-send">
                <i class="bi bi-send-fill" style="font-size: 0.85rem;"></i>
            </button>
        </div>
    </div>
</div>

<style>
    #gemini-chat-widget {
        position: fixed;
        bottom: 95px;
        right: 25px;
        z-index: 9999;
        font-family: 'Roboto', 'Inter', sans-serif;
    }
    #gemini-chat-btn {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        background-color: #ce1126;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 16px rgba(206, 17, 38, 0.4);
        cursor: pointer;
        transition: transform 0.2s, background-color 0.2s;
    }
    #gemini-chat-btn:hover {
        transform: scale(1.08);
        background-color: #a00d20;
    }
    #gemini-chat-window {
        width: 330px;
        height: 430px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.15);
        display: none;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid #eaeaea;
        position: absolute;
        bottom: 68px;
        right: 0;
        transition: all 0.3s ease;
    }
    #gemini-chat-header {
        background-color: #ce1126;
        color: white;
        padding: 10px 14px;
        font-weight: bold;
        font-size: 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    #gemini-chat-messages {
        flex: 1;
        padding: 14px;
        overflow-y: auto;
        background-color: #f8f9fa;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .chat-msg {
        max-width: 85%;
        padding: 8px 12px;
        border-radius: 12px;
        font-size: 13.5px;
        line-height: 1.45;
        word-wrap: break-word;
    }
    .chat-msg.bot {
        background-color: #e9ecef;
        color: #212529;
        align-self: flex-start;
        border-bottom-left-radius: 2px;
    }
    .chat-msg.user {
        background-color: #ce1126;
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 2px;
    }
    .chat-msg.loading-dots {
        align-self: flex-start;
        background-color: #e9ecef;
        padding: 8px 16px;
    }
    #gemini-chat-input-area {
        padding: 10px;
        border-top: 1px solid #eee;
        display: flex;
        gap: 6px;
        background: white;
        align-items: center;
    }
    #gemini-chat-input {
        flex: 1;
        border: 1px solid #ddd;
        border-radius: 20px;
        padding: 6px 14px;
        outline: none;
        font-size: 13px;
        color: #212529;
    }
    #gemini-chat-input:focus {
        border-color: #ce1126;
    }
    #gemini-chat-send {
        background-color: #ce1126;
        color: white;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    #gemini-chat-send:hover {
        background-color: #a00d20;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const chatWidget = document.getElementById('gemini-chat-widget');
        const chatBtn = document.getElementById('gemini-chat-btn');
        const chatWindow = document.getElementById('gemini-chat-window');
        const closeChat = document.getElementById('close-chat');
        const chatInput = document.getElementById('gemini-chat-input');
        const chatSend = document.getElementById('gemini-chat-send');
        const chatMessages = document.getElementById('gemini-chat-messages');

        let chatHistory = []; // Lưu trữ hội thoại

        // Toggle chat window
        chatBtn.addEventListener('click', function () {
            if (chatWindow.style.display === 'none' || !chatWindow.style.display) {
                chatWindow.style.display = 'flex';
                chatInput.focus();
            } else {
                chatWindow.style.display = 'none';
            }
        });

        // Close chat
        closeChat.addEventListener('click', function () {
            chatWindow.style.display = 'none';
        });

        // Send Message action
        function sendMessage() {
            const text = chatInput.value.trim();
            if (!text) return;

            // Add user message to UI
            appendMessage(text, 'user');
            chatInput.value = '';

            // Add loading dots indicator
            const loadingMsg = appendMessage('<span class="spinner-grow spinner-grow-sm text-secondary" role="status"></span> Đang trả lời...', 'bot loading-dots');
            chatMessages.scrollTop = chatMessages.scrollHeight;

            // Call Backend API
            fetch('{{ route("api.gemini.chat") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    message: text,
                    history: chatHistory
                })
            })
            .then(res => res.json())
            .then(data => {
                // Remove loading message
                loadingMsg.remove();

                if (data.success && data.reply) {
                    appendMessage(formatMarkdown(data.reply), 'bot');
                    // Save history
                    chatHistory.push({ role: 'user', content: text });
                    chatHistory.push({ role: 'model', content: data.reply });
                    // Keep history brief (last 10 messages)
                    if (chatHistory.length > 20) {
                        chatHistory = chatHistory.slice(-20);
                    }
                } else {
                    appendMessage('Có lỗi xảy ra, vui lòng thử lại sau.', 'bot');
                }
                chatMessages.scrollTop = chatMessages.scrollHeight;
            })
            .catch(err => {
                loadingMsg.remove();
                console.error(err);
                appendMessage('Mất kết nối với máy chủ.', 'bot');
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
        }

        // Send button click
        chatSend.addEventListener('click', sendMessage);

        // Enter key press
        chatInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        function appendMessage(html, type) {
            const div = document.createElement('div');
            div.className = `chat-msg ${type}`;
            div.innerHTML = html;
            chatMessages.appendChild(div);
            return div;
        }

        // Simple helper to format Markdown bold text to HTML
        function formatMarkdown(text) {
            // Replace **bold** with <strong>bold</strong>
            return text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                       .replace(/\n/g, '<br>');
        }
    });
</script>
