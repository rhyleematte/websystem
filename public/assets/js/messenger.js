/**
 * Messenger JS
 */
document.addEventListener('DOMContentLoaded', function() {
    const messengerToggle = document.querySelector('.messenger-toggle');
    const messengerDrawer = document.querySelector('#messenger-drawer');
    const closeDrawer = document.querySelector('#close-messenger-drawer');
    const conversationList = document.querySelector('#conversation-list');
    const userSearchInput = document.querySelector('#messenger-user-search');
    const searchResults = document.querySelector('#messenger-search-results');
    const chatTray = document.querySelector('#chat-tray');
    const chatBoxTemplate = document.querySelector('#chat-box-template');

    let openChats = new Map(); // conversationId -> DOM Element

    // Toggle Drawer
    if (messengerToggle) {
        messengerToggle.addEventListener('click', () => {
            messengerDrawer.classList.toggle('open');
            if (messengerDrawer.classList.contains('open')) {
                loadConversations();
            }
        });
    }

    if (closeDrawer) {
        closeDrawer.addEventListener('click', () => {
            messengerDrawer.classList.remove('open');
        });
    }

    // Load Conversations
    async function loadConversations() {
        try {
            const response = await fetch('/api/messenger/conversations');
            const conversations = await response.json();
            
            conversationList.innerHTML = '';
            conversations.forEach(conv => {
                const isMutual = conv.other_user.is_mutual;
                const isConversation = conv.is_conversation;
                
                const item = document.createElement('div');
                item.className = 'conversation-item';
                if (!isConversation) item.classList.add('contact-item');
                
                let lastMsg = 'No messages yet';
                if (conv.latest_message) {
                    lastMsg = conv.latest_message.body;
                } else if (isMutual) {
                    lastMsg = '<span class="mutual-badge">Mutual Follower</span>';
                } else if (!isConversation) {
                    lastMsg = '<span class="following-badge">Following</span>';
                }

                item.innerHTML = `
                    <div class="chat-avatar-wrapper">
                        <img src="${conv.other_user.avatar}" class="chat-avatar">
                        ${isMutual ? '<div class="mutual-dot" title="Mutual Follower"></div>' : ''}
                    </div>
                    <div class="conv-info">
                        <div class="conv-name">
                            ${conv.other_user.name}
                            ${conv.other_user.is_doctor ? '<i data-lucide="badge-check" class="doctor-badge"></i>' : ''}
                        </div>
                        <div class="conv-last-msg">${lastMsg}</div>
                    </div>
                `;
                item.addEventListener('click', () => openChatBox(conv));
                conversationList.appendChild(item);
            });
            lucide.createIcons();
        } catch (error) {
            console.error('Error loading conversations:', error);
        }
    }

    // Search Users
    userSearchInput.addEventListener('input', async (e) => {
        const query = e.target.value;
        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        try {
            const response = await fetch(`/api/messenger/search?q=${query}`);
            const users = await response.json();

            searchResults.innerHTML = '';
            if (users.length > 0) {
                users.forEach(user => {
                    const item = document.createElement('div');
                    item.className = 'search-result-item';
                    
                    let status = '';
                    if (user.priority === 1) status = 'Mutual';
                    else if (user.priority === 2) status = 'Following';

                    item.innerHTML = `
                        <img src="${user.avatar}" class="chat-avatar">
                        <div class="conv-info">
                            <div class="conv-name">
                                ${user.name}
                                ${user.is_doctor ? '<i data-lucide="badge-check" class="doctor-badge"></i>' : ''}
                            </div>
                            <div class="conv-last-msg">${status}</div>
                        </div>
                    `;
                    item.addEventListener('click', () => {
                        openChatBox({ other_user: user, id: null });
                        searchResults.style.display = 'none';
                        userSearchInput.value = '';
                    });
                    searchResults.appendChild(item);
                });
                searchResults.style.display = 'block';
            } else {
                searchResults.style.display = 'none';
            }
        } catch (error) {
            console.error('Error searching users:', error);
        }
    });

    // Open Chat Box
    function openChatBox(conv) {
        const convId = conv.id || `temp-${conv.other_user.id}`;
        if (openChats.has(convId)) return;

        const clone = chatBoxTemplate.content.cloneNode(true);
        const chatBox = clone.querySelector('.chat-box');
        chatBox.dataset.conversationId = convId;
        chatBox.dataset.receiverId = conv.other_user.id;

        chatBox.querySelector('.chat-user-name').textContent = conv.other_user.name;
        chatBox.querySelector('.chat-avatar').src = conv.other_user.avatar;

        if (conv.other_user.is_doctor) {
            const badge = document.createElement('i');
            badge.dataset.lucide = 'badge-check';
            badge.className = 'doctor-badge';
            chatBox.querySelector('.chat-user-info').appendChild(badge);
        }

        // Actions
        chatBox.querySelector('.close-chat').addEventListener('click', (e) => {
            e.stopPropagation();
            chatBox.remove();
            openChats.delete(convId);
        });

        chatBox.querySelector('.minimize-chat').addEventListener('click', (e) => {
            e.stopPropagation();
            chatBox.classList.toggle('minimized');
        });

        chatBox.querySelector('.chat-box-header').addEventListener('click', () => {
            chatBox.classList.toggle('minimized');
        });

        // Send Message
        const input = chatBox.querySelector('textarea');
        const sendBtn = chatBox.querySelector('.send-message-btn');

        const sendMessage = async () => {
            const body = input.value.trim();
            if (!body) return;

            try {
                const response = await fetch('/api/messenger/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        conversation_id: conv.id,
                        receiver_id: conv.other_user.id,
                        body: body
                    })
                });

                const msg = await response.json();
                input.value = '';
                addMessageToBox(chatBox, msg, 'sent');
                
                // If it was a temp chat, update conversation ID
                if (!conv.id) {
                    chatBox.dataset.conversationId = msg.conversation_id;
                    openChats.delete(convId);
                    openChats.set(msg.conversation_id, chatBox);
                    conv.id = msg.conversation_id;
                }
            } catch (error) {
                console.error('Error sending message:', error);
            }
        };

        sendBtn.addEventListener('click', sendMessage);
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        chatTray.appendChild(chatBox);
        openChats.set(convId, chatBox);
        lucide.createIcons();

        if (conv.id) {
            loadMessages(chatBox, conv.id);
        }
    }

    // Load Messages
    async function loadMessages(chatBox, convId) {
        try {
            const response = await fetch(`/api/messenger/messages/${convId}`);
            const messages = await response.json();
            const container = chatBox.querySelector('.chat-box-messages');
            container.innerHTML = '';
            messages.forEach(msg => {
                const type = msg.sender_user_id == window.MY_ID ? 'sent' : 'received';
                addMessageToBox(chatBox, msg, type);
            });
        } catch (error) {
            console.error('Error loading messages:', error);
        }
    }

    function addMessageToBox(chatBox, msg, type) {
        const container = chatBox.querySelector('.chat-box-messages');
        const bubble = document.createElement('div');
        bubble.className = `message-bubble ${type}`;
        bubble.textContent = msg.body;
        container.appendChild(bubble);
        container.scrollTop = container.scrollHeight;
    }
});
