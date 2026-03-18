<style>
/* AI Chat Modal */
.ai-modal-overlay {
    position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
    background: rgba(0,0,0,0.5); z-index: 10000;
    display: none; align-items: center; justify-content: center;
}
.ai-modal-content {
    background: var(--panel, #ffffff); color: var(--text, #333);
    width: 95%; max-width: 900px; border-radius: 12px;
    display: flex; flex-direction: row; overflow: hidden;
    height: 85vh; max-height: 700px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}
.ai-modal-sidebar {
    width: 320px; background: var(--input-bg, #f8fafc); border-right: 1px solid var(--border);
    padding: 25px 20px; overflow-y: auto; font-size: 0.9rem;
    display: flex; flex-direction: column;
}
.ai-modal-sidebar h4 { margin-top: 0; font-size: 1.25rem; font-weight: 700; display: flex; align-items: center; gap: 8px; color: var(--text); margin-bottom: 20px; }
.ai-modal-sidebar h5 { margin-top: 20px; margin-bottom: 8px; font-size: 0.95rem; font-weight: 600; color: var(--text); }
.ai-modal-sidebar p, .ai-modal-sidebar ul { margin-bottom: 10px; color: var(--text-muted); line-height: 1.5; font-size: 0.85rem; }
.ai-modal-sidebar ul { padding-left: 20px; }
.ai-modal-sidebar hr { border: none; border-top: 1px solid var(--border); margin: 20px 0; }
.btn-export-pdf {
    background: var(--panel); border: 1px solid var(--border); color: var(--text);
    padding: 10px 15px; border-radius: 8px; font-weight: 600; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s;
    margin-top: auto;
}
.btn-export-pdf:hover { background: var(--hover); }

.ai-chat-section { flex: 1; display: flex; flex-direction: column; background: var(--panel); position: relative; }
.ai-modal-header {
    padding: 15px 20px; background: var(--hover, #f8fafc); border-bottom: 1px solid var(--border);
    display: flex; justify-content: space-between; align-items: center;
}
.ai-modal-header h3 { margin: 0; font-size: 16px; display: flex; align-items: center; gap: 8px; }
.ai-modal-close { background: none; border: none; font-size: 24px; color: var(--text-muted); cursor: pointer; line-height: 1; transition: color 0.2s; }
.ai-modal-close:hover { color: #ef4444; }
.ai-chat-body { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 15px; }
.ai-message { max-width: 85%; padding: 12px 16px; border-radius: 12px; font-size: 0.95rem; line-height: 1.5; }
.ai-message.assistant { background: var(--hover, #f8fafc); border-bottom-left-radius: 2px; align-self: flex-start; border: 1px solid var(--border); }
.ai-message.user { background: var(--brand, var(--primary, #7c3aed)); color: white; border-bottom-right-radius: 2px; align-self: flex-end; box-shadow: 0 2px 5px rgba(124, 58, 237, 0.2); }
.ai-chat-footer { padding: 15px 20px; border-top: 1px solid var(--border); display: flex; gap: 10px; background: var(--panel); }
.ai-chat-input { flex: 1; padding: 12px 20px; border-radius: 24px; border: 1px solid var(--border); background: var(--input-bg, #fff); color: var(--text); outline: none; transition: border-color 0.2s; }
.ai-chat-input:focus { border-color: var(--brand, var(--primary, #7c3aed)); }
.ai-chat-send { background: var(--brand, var(--primary, #7c3aed)); color: white; border: none; border-radius: 24px; padding: 0 24px; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 8px; transition: opacity 0.2s; }
.ai-chat-send:hover { opacity: 0.9; }
.ai-chat-typing { font-size: 13px; color: var(--muted, #64748b); align-self: flex-start; display: none; margin-left: 10px; font-style: italic; }
.ai-doctors-list { display: flex; flex-direction: column; gap: 10px; margin-top: 15px; }
.ai-doctor-card { display: flex; align-items: center; gap: 12px; padding: 12px; background: var(--hover, #f8fafc); border: 1px solid var(--border); border-radius: 8px; }
.ai-doctor-avatar img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
.ai-doctor-info { flex: 1; }
.ai-doctor-name { font-weight: 600; font-size: 14px; margin-bottom: 2px; }
.ai-doctor-title { font-size: 12px; color: var(--muted, #64748b); }
.ai-req-btn { background: var(--brand, #7c3aed); color: white; border: none; padding: 6px 14px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600; }
</style>

<!-- Load html2pdf -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<div class="ai-modal-overlay" id="aiModal">
    <div class="ai-modal-content">
        <!-- Sidebar -->
        <div class="ai-modal-sidebar">
            <h4><i data-lucide="bot" style="width: 24px; height: 24px; color: var(--primary);"></i> About Bot</h4>
            
            <h5>Description</h5>
            <p>An AI-powered chatbot designed to provide mental health support.</p>
            
            <h5>Goals</h5>
            <ul>
                <li>Provide 24/7 mental health support</li>
                <li>Offer crisis intervention when needed</li>
                <li>Connect users with professional resources</li>
            </ul>
            
            <h5>Purpose</h5>
            <p style="text-align: justify;">Designed as a stigma-free entry point to mental health support in India—where people often feel too shy to seek traditional therapy—this chatbot tackles interpersonal harassment, workplace stress, and suicidal ideation by offering empathetic listening, practical coping strategies, and evidence-based guidance from the WHO and India's Ministry of Health and Family Welfare. By normalizing conversations about emotional well-being and delivering timely, trustworthy advice, it bridges users to professional therapists when they're ready.</p>
            
            <h5>Our Values</h5>
            <ul>
                <li>Empathy</li>
                <li>Professional Ethics</li>
                <li>User Safety</li>
            </ul>
            
            <hr>
            
            <button class="btn-export-pdf" id="aiExportPdfBtn">
                <i data-lucide="file-down" style="width: 18px; height: 18px;"></i> Export Chat to PDF
            </button>
        </div>

        <!-- Chat Area -->
        <div class="ai-chat-section">
            <div class="ai-modal-header">
                <h3>Mental Health Chatbot</h3>
                <button class="ai-modal-close" id="aiCloseBtn" aria-label="Close">&times;</button>
            </div>
            <div class="ai-chat-body" id="aiChatBody">
                <div class="ai-message assistant">Hi there. I'm an AI screener here to help you find the right professional. Can you tell me a little bit about what you're feeling right now?</div>
                <div class="ai-chat-typing" id="aiTypingIndicator">Typing...</div>
            </div>
            <div class="ai-chat-footer">
                <input type="text" class="ai-chat-input" id="aiChatInput" placeholder="Ask a question about Mental Health..." />
                <button class="ai-chat-send" id="aiSendBtn">Send <i data-lucide="send" style="width: 16px; height: 16px;"></i></button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const getHelpBtn = document.getElementById('getHelpBtn');
    const aiModal = document.getElementById('aiModal');
    const aiCloseBtn = document.getElementById('aiCloseBtn');
    const aiChatBody = document.getElementById('aiChatBody');
    const aiChatInput = document.getElementById('aiChatInput');
    const aiSendBtn = document.getElementById('aiSendBtn');
    const aiTypingIndicator = document.getElementById('aiTypingIndicator');
    
    let chatHistory = [];
    
    const aiExportPdfBtn = document.getElementById('aiExportPdfBtn');
    
    // PDF Export Logic
    if (aiExportPdfBtn) {
        aiExportPdfBtn.addEventListener('click', () => {
            if (chatHistory.length === 0) {
                alert('No chat history to export!');
                return;
            }
            
            aiExportPdfBtn.innerHTML = 'Exporting...';
            aiExportPdfBtn.disabled = true;
            
            // Create a temporary layout for PDF rendering
            const tempDiv = document.createElement('div');
            tempDiv.style.padding = '20px';
            tempDiv.style.fontFamily = 'Arial, sans-serif';
            tempDiv.style.color = '#333';
            
            const header = document.createElement('h2');
            header.innerText = 'Mental Health Chatbot - Conversation History';
            header.style.textAlign = 'center';
            header.style.marginBottom = '5px';
            tempDiv.appendChild(header);
            
            const dateP = document.createElement('p');
            dateP.innerText = 'Date: ' + new Date().toLocaleString();
            dateP.style.textAlign = 'center';
            dateP.style.marginBottom = '20px';
            dateP.style.color = '#666';
            tempDiv.appendChild(dateP);
            
            chatHistory.forEach(msg => {
                const roleHeader = document.createElement('h4');
                roleHeader.innerText = msg.role.charAt(0).toUpperCase() + msg.role.slice(1);
                roleHeader.style.margin = '15px 0 5px 0';
                roleHeader.style.color = msg.role === 'user' ? '#7c3aed' : '#333';
                
                const contentText = document.createElement('p');
                contentText.innerText = msg.content;
                contentText.style.margin = '0';
                contentText.style.lineHeight = '1.5';
                
                tempDiv.appendChild(roleHeader);
                tempDiv.appendChild(contentText);
            });
            
            const opt = {
              margin:       10,
              filename:     'mental_health_chat_' + Date.now() + '.pdf',
              image:        { type: 'jpeg', quality: 0.98 },
              html2canvas:  { scale: 2 },
              jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            
            if (typeof html2pdf !== 'undefined') {
                html2pdf().set(opt).from(tempDiv).save().then(() => {
                    aiExportPdfBtn.innerHTML = '<i data-lucide="file-down" style="width: 18px; height: 18px;"></i> Export Chat to PDF';
                    aiExportPdfBtn.disabled = false;
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                }).catch(err => {
                    console.error("PDF Export Error", err);
                    aiExportPdfBtn.innerHTML = '<i data-lucide="file-down" style="width: 18px; height: 18px;"></i> Export Chat to PDF';
                    aiExportPdfBtn.disabled = false;
                });
            } else {
                alert('PDF library not loaded.');
                aiExportPdfBtn.innerHTML = '<i data-lucide="file-down" style="width: 18px; height: 18px;"></i> Export Chat to PDF';
                aiExportPdfBtn.disabled = false;
            }
        });
    }
    
    if (getHelpBtn) {
        getHelpBtn.addEventListener('click', () => {
            aiModal.style.display = 'flex';
        });
    }
    if (aiCloseBtn) {
        aiCloseBtn.addEventListener('click', () => {
            aiModal.style.display = 'none';
        });
    }
    
    function appendMessage(role, content) {
        const div = document.createElement('div');
        div.className = 'ai-message ' + role;
        div.innerText = content;
        aiChatBody.insertBefore(div, aiTypingIndicator);
        aiChatBody.scrollTop = aiChatBody.scrollHeight;
    }

    async function sendMessage() {
        const text = aiChatInput.value.trim();
        if(!text) return;
        
        appendMessage('user', text);
        chatHistory.push({ role: 'user', content: text });
        aiChatInput.value = '';
        
        aiTypingIndicator.style.display = 'block';
        aiChatBody.scrollTop = aiChatBody.scrollHeight;
        
        try {
            const res = await fetch('{{ url("/api/help/chat") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ messages: chatHistory })
            });
            const data = await res.json();
            aiTypingIndicator.style.display = 'none';
            
            if (data.role) {
                appendMessage('assistant', data.content);
                chatHistory.push({ role: 'assistant', content: data.content });
                
                if (data.suggested_title) {
                    // Fetch doctors with this title
                    fetchDoctors(data.suggested_title);
                }
            } else {
                appendMessage('assistant', "I'm having trouble connecting right now.");
            }
        } catch(e) {
            aiTypingIndicator.style.display = 'none';
            appendMessage('assistant', 'Error communicating with server.');
        }
    }
    
    async function fetchDoctors(title) {
        const res = await fetch(`{{ url("/api/help/doctors") }}?title=${encodeURIComponent(title)}`, {
            headers: {'Accept': 'application/json'}
        });
        const data = await res.json();
        
        const listDiv = document.createElement('div');
        listDiv.className = 'ai-doctors-list';
        
        if (data.doctors && data.doctors.length > 0) {
            const h = document.createElement('div');
            h.innerHTML = `<strong>Here are some available professionals:</strong>`;
            listDiv.appendChild(h);
            
            data.doctors.forEach(doc => {
                const card = document.createElement('div');
                card.className = 'ai-doctor-card';
                card.innerHTML = `
                    <div class="ai-doctor-avatar"><img src="${doc.avatar}" /></div>
                    <div class="ai-doctor-info">
                        <div class="ai-doctor-name">${doc.name}</div>
                        <div class="ai-doctor-title">${doc.title}</div>
                    </div>
                    <button class="ai-req-btn" data-id="${doc.id}" data-title="${title}">Request Chat</button>
                `;
                listDiv.appendChild(card);
            });
        } else {
            listDiv.innerHTML = `<div>No available professionals matching this right now. Please check back later.</div>`;
        }
        
        aiChatBody.insertBefore(listDiv, aiTypingIndicator);
        aiChatBody.scrollTop = aiChatBody.scrollHeight;
        
        // attach events
        const reqBtns = listDiv.querySelectorAll('.ai-req-btn');
        reqBtns.forEach(btn => {
            btn.addEventListener('click', async (e) => {
                const docId = e.target.getAttribute('data-id');
                const t = e.target.getAttribute('data-title');
                e.target.disabled = true;
                e.target.innerText = 'Requesting...';
                
                try {
                    const r = await fetch('{{ url("/api/help/request") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ doctor_id: docId, suggested_title: t })
                    });
                    const resData = await r.json();
                    if (resData.success) {
                        e.target.innerText = 'Sent!';
                        appendMessage('assistant', 'Your request has been sent! Please wait for the doctor to accept it. You will be redirected or notified.');
                    } else {
                        e.target.innerText = 'Failed';
                        e.target.disabled = false;
                    }
                } catch(error) {
                    e.target.innerText = 'Failed';
                    e.target.disabled = false;
                }
            });
        });
    }
    
    if (aiSendBtn) {
        aiSendBtn.addEventListener('click', sendMessage);
    }
    if (aiChatInput) {
        aiChatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendMessage();
        });
    }
});
</script>
