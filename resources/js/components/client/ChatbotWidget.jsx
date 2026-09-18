import React, { useState, useEffect, useRef } from 'react';

export default function ChatbotWidget({ apiUrl = '/api/gemini/chat', csrfToken = '' }) {
    const [isOpen, setIsOpen] = useState(false);
    const [messages, setMessages] = useState([
        { role: 'model', text: 'Xin chào! Tôi là Trợ lý ảo FOODDAILY AI 🤖. Bạn cần gợi ý món ngon, kiểm tra đơn hàng hay tư vấn dinh dưỡng hôm nay?' }
    ]);
    const [input, setInput] = useState('');
    const [loading, setLoading] = useState(false);
    const messagesEndRef = useRef(null);

    const scrollToBottom = () => {
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    };

    useEffect(() => {
        if (isOpen) {
            scrollToBottom();
        }
    }, [messages, isOpen]);

    const handleSend = async (e) => {
        e?.preventDefault();
        const query = input.trim();
        if (!query || loading) return;

        const newMessages = [...messages, { role: 'user', text: query }];
        setMessages(newMessages);
        setInput('');
        setLoading(true);

        try {
            const token = csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    message: query,
                    history: messages.map(m => ({ role: m.role, content: m.text }))
                })
            });

            const data = await res.json();
            if (data.success && data.reply) {
                setMessages([...newMessages, { role: 'model', text: data.reply }]);
            } else {
                setMessages([...newMessages, { role: 'model', text: 'Dạ, hiện tại hệ thống AI đang bận. Bạn vui lòng thử lại sau giây lát nha!' }]);
            }
        } catch (err) {
            setMessages([...newMessages, { role: 'model', text: 'Không thể kết nối đến máy chủ AI. Vui lòng kiểm tra lại kết nối mạng.' }]);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="fixed bottom-6 right-6 z-50 font-sans">
            {/* Toggle Button */}
            {!isOpen && (
                <button
                    onClick={() => setIsOpen(true)}
                    className="flex items-center gap-2.5 px-4 py-3 bg-gradient-to-r from-red-600 to-amber-500 text-white rounded-full shadow-xl hover:shadow-2xl hover:scale-105 active:scale-95 transition-all duration-200 cursor-pointer"
                >
                    <span className="text-xl">🤖</span>
                    <span className="text-xs font-bold tracking-wide">Hỏi AI Tư Vấn Món</span>
                </button>
            )}

            {/* Chat Window */}
            {isOpen && (
                <div className="w-[360px] sm:w-[400px] h-[520px] bg-white rounded-2xl shadow-2xl border border-slate-200 flex flex-col overflow-hidden animate-in fade-in slide-in-from-bottom-5 duration-200">
                    {/* Header */}
                    <div className="px-4 py-3.5 bg-gradient-to-r from-red-600 to-amber-600 text-white flex items-center justify-between shadow-xs">
                        <div className="flex items-center gap-2.5">
                            <div className="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-lg">
                                🤖
                            </div>
                            <div>
                                <h3 className="text-sm font-extrabold leading-tight">Trợ Lý Ảo FOODDAILY AI</h3>
                                <p className="text-[10px] text-white/80 font-medium">Gợi ý món ăn & dinh dưỡng thông minh</p>
                            </div>
                        </div>
                        <button
                            onClick={() => setIsOpen(false)}
                            className="w-7 h-7 rounded-full hover:bg-white/20 flex items-center justify-center text-white text-base transition-colors"
                        >
                            ✕
                        </button>
                    </div>

                    {/* Messages */}
                    <div className="flex-1 p-4 overflow-y-auto space-y-3 bg-slate-50 text-xs">
                        {messages.map((m, idx) => (
                            <div
                                key={idx}
                                className={`flex ${m.role === 'user' ? 'justify-end' : 'justify-start'}`}
                            >
                                <div
                                    className={`max-w-[82%] px-3.5 py-2.5 rounded-2xl leading-relaxed whitespace-pre-line ${
                                        m.role === 'user'
                                            ? 'bg-red-600 text-white rounded-br-xs font-medium'
                                            : 'bg-white text-slate-800 border border-slate-200/80 rounded-bl-xs shadow-xs'
                                    }`}
                                >
                                    {m.text}
                                </div>
                            </div>
                        ))}
                        {loading && (
                            <div className="flex justify-start">
                                <div className="bg-white border border-slate-200 px-3.5 py-2 rounded-2xl rounded-bl-xs text-slate-500 text-xs flex items-center gap-1.5 shadow-xs">
                                    <span className="inline-block w-1.5 h-1.5 bg-red-600 rounded-full animate-bounce"></span>
                                    <span className="inline-block w-1.5 h-1.5 bg-red-600 rounded-full animate-bounce [animation-delay:0.2s]"></span>
                                    <span className="inline-block w-1.5 h-1.5 bg-red-600 rounded-full animate-bounce [animation-delay:0.4s]"></span>
                                    <span className="ml-1 text-[11px] font-medium">AI đang suy nghĩ...</span>
                                </div>
                            </div>
                        )}
                        <div ref={messagesEndRef} />
                    </div>

                    {/* Quick Suggestion Chips */}
                    <div className="px-3 py-2 bg-slate-100 border-t border-slate-200/60 flex items-center gap-1.5 overflow-x-auto text-[11px] text-slate-700 font-semibold no-scrollbar">
                        <button
                            type="button"
                            onClick={() => { setInput('Món nào dưới 50k ngon nhất?'); }}
                            className="px-2.5 py-1 bg-white hover:bg-red-50 hover:text-red-600 rounded-full border border-slate-200 whitespace-nowrap transition-colors"
                        >
                            💰 Món dưới 50k
                        </button>
                        <button
                            type="button"
                            onClick={() => { setInput('Gợi ý thực đơn trưa nhiều đạm'); }}
                            className="px-2.5 py-1 bg-white hover:bg-red-50 hover:text-red-600 rounded-full border border-slate-200 whitespace-nowrap transition-colors"
                        >
                            💪 Trưa giàu đạm
                        </button>
                        <button
                            type="button"
                            onClick={() => { setInput('Chính sách giao hàng thế nào?'); }}
                            className="px-2.5 py-1 bg-white hover:bg-red-50 hover:text-red-600 rounded-full border border-slate-200 whitespace-nowrap transition-colors"
                        >
                            🚀 Giao hàng
                        </button>
                    </div>

                    {/* Input */}
                    <form onSubmit={handleSend} className="p-3 bg-white border-t border-slate-200 flex items-center gap-2">
                        <input
                            type="text"
                            value={input}
                            onChange={(e) => setInput(e.target.value)}
                            placeholder="Hỏi về món ăn, giá cả, calo..."
                            className="flex-1 px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"
                        />
                        <button
                            type="submit"
                            disabled={loading || !input.trim()}
                            className="px-4 py-2 bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white text-xs font-bold rounded-xl transition-colors cursor-pointer"
                        >
                            Gửi
                        </button>
                    </form>
                </div>
            )}
        </div>
    );
}
