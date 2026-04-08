<div id="messenger-root" class="messenger-root">
    
    <div id="chat-tray" class="chat-tray"></div>

    
    <div id="messenger-drawer" class="messenger-drawer">
        <div class="drawer-header">
            <h3>Messages</h3>
            <button id="close-messenger-drawer" class="icon-btn"><i data-lucide="x"></i></button>
        </div>
        <div class="drawer-search">
            <i data-lucide="search"></i>
            <input type="text" id="messenger-user-search" placeholder="Search people...">
            <div id="messenger-search-results" class="search-results-popover"></div>
        </div>
        <div id="conversation-list" class="conversation-list">
            
        </div>
    </div>
</div>

<template id="chat-box-template">
    <div class="chat-box" data-conversation-id="">
        <div class="chat-box-header">
            <div class="chat-box-user">
                <img src="" alt="" class="chat-avatar">
                <div class="chat-user-info">
                    <span class="chat-user-name"></span>
                    <span class="chat-user-status"></span>
                </div>
            </div>
            <div class="chat-box-actions">
                <button class="minimize-chat"><i data-lucide="minus"></i></button>
                <button class="close-chat"><i data-lucide="x"></i></button>
            </div>
        </div>
        <div class="chat-box-messages">
            
        </div>

        <div class="chat-pending-overlay" style="display: none;">
            <div class="pending-info">
                <div class="pending-user-name"></div>
                <div class="pending-request-text"></div>
            </div>
            <div class="pending-actions">
                <button class="decline-request-btn">Decline</button>
                <button class="accept-request-btn">Accept</button>
            </div>
        </div>

        <div class="typing-indicator">
            <div class="dot"></div>
            <span class="typing-text">someone is typing...</span>
        </div>
        <div class="chat-box-input">
            <textarea placeholder="Type a message..."></textarea>
            <button class="send-message-btn"><i data-lucide="send"></i></button>
        </div>
    </div>
</template>
<?php /**PATH C:\websystem\resources\views/partials/messenger.blade.php ENDPATH**/ ?>