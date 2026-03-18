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

    // If messenger isn't on this page (like for Admins), exit safely.
    if (!messengerDrawer || !conversationList || !chatBoxTemplate) {
        return;
    }

    let openChats = new Map(); // conversationId -> { element, lastMsgId, pollInterval, typingInterval, isTyping }
    let convPollInterval = null;

    // Toggle Drawer
    if (messengerToggle) {
        messengerToggle.addEventListener('click', () => {
            messengerDrawer.classList.toggle('open');
            if (messengerDrawer.classList.contains('open')) {
                loadConversations();
                if (!convPollInterval) {
                    convPollInterval = setInterval(loadConversations, 5000);
                }
            } else {
                clearInterval(convPollInterval);
                convPollInterval = null;
            }
        });
    }

    if (closeDrawer) {
        closeDrawer.addEventListener('click', () => {
            messengerDrawer.classList.remove('open');
            clearInterval(convPollInterval);
            convPollInterval = null;
        });
    }

    // Load Conversations
    async function loadConversations() {
        if (document.activeElement && (document.activeElement.id === 'messenger-user-search')) return;
        if (document.querySelector('.conv-menu-popover.open')) return;

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
                    ${isConversation ? `
                    <div class="conv-actions">
                        <button class="conv-menu-btn" type="button"><i data-lucide="more-vertical"></i></button>
                        <div class="conv-menu-popover">
                            <div class="conv-menu-item danger delete-conv" data-id="${conv.id}">
                                Archive
                            </div>
                        </div>
                    </div>
                    ` : ''}
                `;

                item.addEventListener('click', (e) => {
                    if (e.target.closest('.conv-actions')) return;
                    openChatBox(conv);
                });

                if (isConversation) {
                    const menuBtn = item.querySelector('.conv-menu-btn');
                    const popover = item.querySelector('.conv-menu-popover');
                    const deleteBtn = item.querySelector('.delete-conv');

                    menuBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        document.querySelectorAll('.conv-menu-popover.open').forEach(p => {
                            if (p !== popover) p.classList.remove('open');
                        });
                        popover.classList.toggle('open');
                    });

                    deleteBtn.addEventListener('click', async (e) => {
                        e.stopPropagation();
                        if (!confirm('Are you sure you want to archive this conversation? It will hide for you but re-appear if you send a new message.')) return;
                        
                        try {
                            const response = await fetch(`/api/messenger/conversations/${conv.id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                            if (response.ok) {
                                item.remove();
                                // Logic update: Archive only removes from list, doesn't close box
                            }
                        } catch (err) {
                            console.error('Archive failed:', err);
                        }
                    });
                }

                conversationList.appendChild(item);
            });
            lucide.createIcons();
        } catch (error) {
            console.error('Error loading conversations:', error);
        }
    }

    document.addEventListener('click', () => {
        document.querySelectorAll('.conv-menu-popover.open').forEach(p => p.classList.remove('open'));
    });

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
        if (openChats.has(convId)) {
            const chatObj = openChats.get(convId);
            chatObj.element.classList.remove('minimized');
            return;
        }

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
            const chatObj = openChats.get(convId);
            if (chatObj) {
                clearInterval(chatObj.pollInterval);
                clearInterval(chatObj.typingInterval);
            }
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
                
                if (!conv.id) {
                    chatBox.dataset.conversationId = msg.conversation_id;
                    openChats.delete(convId);
                    
                    const newChatObj = {
                        element: chatBox,
                        lastMsgId: msg.id,
                        pollInterval: setInterval(() => pollForMessages(chatBox, msg.conversation_id), 3000),
                        typingInterval: setInterval(() => pollForTyping(chatBox, msg.conversation_id), 3000),
                        isTyping: false
                    };
                    openChats.set(msg.conversation_id, newChatObj);
                    conv.id = msg.conversation_id;
                } else {
                    const chatObj = openChats.get(conv.id);
                    if (chatObj) chatObj.lastMsgId = msg.id;
                }
                
                setTypingStatus(conv.id, false);
                loadConversations();
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

        let typingTimeout;
        input.addEventListener('input', () => {
            if (!conv.id) return;
            setTypingStatus(conv.id, true);
            
            clearTimeout(typingTimeout);
            typingTimeout = setTimeout(() => {
                setTypingStatus(conv.id, false);
            }, 3000);
        });

        chatTray.appendChild(chatBox);
        
        const chatObj = {
            element: chatBox,
            lastMsgId: 0,
            pollInterval: conv.id ? setInterval(() => pollForMessages(chatBox, conv.id), 3000) : null,
            typingInterval: conv.id ? setInterval(() => pollForTyping(chatBox, conv.id), 3000) : null,
            isTyping: false
        };
        openChats.set(convId, chatObj);
        
        lucide.createIcons();

        if (conv.id) {
            loadMessages(chatBox, conv.id);
        }
    }

    async function setTypingStatus(convId, isTyping) {
        if (!convId) return;
        try {
            fetch('/api/messenger/typing', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ conversation_id: convId, is_typing: isTyping })
            });
        } catch (e) {}
    }

    async function pollForTyping(chatBox, convId) {
        if (!convId || chatBox.classList.contains('minimized')) return;
        try {
            const response = await fetch(`/api/messenger/typing/${convId}`);
            const data = await response.json();
            const statusLabel = chatBox.querySelector('.chat-user-status');
            const typingIndicator = chatBox.querySelector('.typing-indicator');
            const typingText = typingIndicator.querySelector('.typing-text');
            const otherUserName = chatBox.querySelector('.chat-user-name').textContent;

            if (data.is_typing) {
                statusLabel.textContent = 'typing...';
                statusLabel.style.fontSize = '11px';
                statusLabel.style.opacity = '0.8';
                
                typingText.textContent = `${otherUserName} is typing...`;
                typingIndicator.style.display = 'flex';
                
                const container = chatBox.querySelector('.chat-box-messages');
                container.scrollTop = container.scrollHeight;
            } else {
                statusLabel.textContent = '';
                typingIndicator.style.display = 'none';
            }
        } catch (e) {}
    }

    async function pollForMessages(chatBox, convId) {
        const chatObj = openChats.get(convId);
        if (!chatObj || chatBox.classList.contains('minimized')) return;

        try {
            const response = await fetch(`/api/messenger/messages/${convId}?after_id=${chatObj.lastMsgId}`);
            const messages = await response.json();
            if (messages.length > 0) {
                messages.forEach(msg => {
                    if (msg.sender_user_id != window.MY_ID) {
                        addMessageToBox(chatBox, msg, 'received');
                    }
                    chatObj.lastMsgId = Math.max(chatObj.lastMsgId, msg.id);
                });
                loadConversations();
            }
        } catch (e) {}
    }

    async function loadMessages(chatBox, convId) {
        try {
            const response = await fetch(`/api/messenger/messages/${convId}`);
            const messages = await response.json();
            const container = chatBox.querySelector('.chat-box-messages');
            container.innerHTML = '';
            
            const chatObj = openChats.get(convId);
            
            messages.forEach(msg => {
                const type = msg.sender_user_id == window.MY_ID ? 'sent' : 'received';
                addMessageToBox(chatBox, msg, type);
                if (chatObj) chatObj.lastMsgId = Math.max(chatObj.lastMsgId, msg.id);
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
