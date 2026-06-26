<!-- Floating Chat Widget -->
<div id="student-chat-widget">
    <!-- Chat Button -->
    <button id="scw-fab" onclick="scwToggle()">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
          <path d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4.414A2 2 0 0 0 3 11.586l-2 2V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
          <path d="M5 6a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
        </svg>
        <span id="scw-badge" class="badge bg-danger rounded-pill d-none">0</span>
    </button>

    <!-- Chat Window -->
    <div id="scw-window" class="d-none">
        <!-- Header -->
        <div id="scw-header">
            <span id="scw-title">Chat Groups</span>
            <button class="scw-btn-close" onclick="scwToggle()">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>
                </svg>
            </button>
        </div>

        <!-- Rooms List View -->
        <div id="scw-rooms-view">
            <div class="scw-loading text-center mt-4 text-muted">
                <div class="spinner-border spinner-border-sm" role="status"></div>
                <div class="mt-2" style="font-size: 13px;">Loading groups...</div>
            </div>
            <div id="scw-rooms-list" class="d-none"></div>
        </div>

        <!-- Chat View -->
        <div id="scw-chat-view" class="d-none">
            <div id="scw-chat-header">
                <button class="scw-btn-back" onclick="scwBackToRooms()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                    </svg>
                </button>
                <span id="scw-chat-title">Group Name</span>
            </div>
            <div id="scw-messages-container"></div>
            <div id="scw-input-area">
                <input type="text" id="scw-message-input" placeholder="Type a message..." autocomplete="off">
                <button id="scw-btn-send" onclick="scwSendMessage()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --scw-primary: #3B82F6;
        --scw-primary-hover: #2563EB;
        --scw-bg: #ffffff;
        --scw-border: #E5E7EB;
        --scw-text: #1F2937;
        --scw-muted: #6B7280;
        --scw-mine: #EFF6FF;
        --scw-theirs: #F3F4F6;
    }

    #student-chat-widget {
        position: fixed;
        bottom: 95px;
        right: 24px;
        z-index: 10000;
        font-family: 'Inter', system-ui, sans-serif;
    }

    #scw-fab {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: var(--scw-primary);
        color: white;
        border: none;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        font-size: 24px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s, background-color 0.2s;
        position: relative;
    }

    #scw-fab:hover {
        background-color: var(--scw-primary-hover);
        transform: scale(1.05);
    }

    #scw-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        font-size: 11px;
        border: 2px solid white;
    }

    #scw-window {
        position: absolute;
        bottom: 80px;
        right: 0;
        width: 340px;
        height: 500px;
        max-height: calc(100vh - 120px);
        background: var(--scw-bg);
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid var(--scw-border);
    }

    #scw-header {
        background: var(--scw-primary);
        color: white;
        padding: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
    }

    .scw-btn-close, .scw-btn-back {
        background: transparent;
        border: none;
        color: white;
        font-size: 18px;
        cursor: pointer;
        padding: 0;
        line-height: 1;
    }

    /* Rooms View */
    #scw-rooms-view {
        flex: 1;
        overflow-y: auto;
    }

    .scw-room-item {
        padding: 16px;
        border-bottom: 1px solid var(--scw-border);
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: background-color 0.2s;
    }

    .scw-room-item:hover {
        background-color: #F9FAFB;
    }

    .scw-room-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #EEF2FF;
        color: var(--scw-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .scw-room-details {
        flex: 1;
    }

    .scw-room-name {
        font-weight: 600;
        font-size: 14px;
        color: var(--scw-text);
        margin-bottom: 2px;
    }

    .scw-room-type {
        font-size: 12px;
        color: var(--scw-muted);
        text-transform: capitalize;
    }

    /* Chat View */
    #scw-chat-view {
        flex: 1;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    #scw-chat-header {
        background: #F8FAFC;
        padding: 12px 16px;
        border-bottom: 1px solid var(--scw-border);
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 600;
        font-size: 14px;
        color: var(--scw-text);
    }
    
    #scw-chat-header .scw-btn-back {
        color: var(--scw-text);
    }

    #scw-messages-container {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 16px;
        background: #F9FAFB;
    }

    .scw-msg {
        display: flex;
        flex-direction: column;
        max-width: 85%;
    }

    .scw-msg.mine {
        align-self: flex-end;
    }

    .scw-msg.theirs {
        align-self: flex-start;
    }

    .scw-msg-header {
        font-size: 11px;
        color: var(--scw-muted);
        margin-bottom: 4px;
        display: flex;
        gap: 6px;
    }

    .scw-msg.mine .scw-msg-header {
        flex-direction: row-reverse;
    }

    .scw-msg-bubble {
        padding: 10px 14px;
        border-radius: 12px;
        font-size: 13.5px;
        line-height: 1.4;
        color: var(--scw-text);
    }

    .scw-msg.mine .scw-msg-bubble {
        background: var(--scw-mine);
        border-bottom-right-radius: 4px;
        color: var(--scw-primary-hover);
    }

    .scw-msg.theirs .scw-msg-bubble {
        background: var(--scw-theirs);
        border-bottom-left-radius: 4px;
    }

    #scw-input-area {
        padding: 12px;
        background: var(--scw-bg);
        border-top: 1px solid var(--scw-border);
        display: flex;
        gap: 8px;
    }

    #scw-message-input {
        flex: 1;
        border: 1px solid var(--scw-border);
        border-radius: 20px;
        padding: 8px 16px;
        font-size: 14px;
        outline: none;
    }
    
    #scw-message-input:focus {
        border-color: var(--scw-primary);
    }

    #scw-btn-send {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: var(--scw-primary);
        color: white;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    #scw-btn-send:disabled {
        background: #9CA3AF;
        cursor: not-allowed;
    }

</style>

<script>
    let scwIsOpen = false;
    let scwCurrentRoom = null;
    let scwPollInterval = null;

    function scwToggle() {
        scwIsOpen = !scwIsOpen;
        const windowEl = document.getElementById('scw-window');
        
        if (scwIsOpen) {
            windowEl.classList.remove('d-none');
            scwLoadRooms();
        } else {
            windowEl.classList.add('d-none');
            scwStopPolling();
        }
    }

    function scwBackToRooms() {
        scwCurrentRoom = null;
        scwStopPolling();
        document.getElementById('scw-chat-view').classList.add('d-none');
        document.getElementById('scw-rooms-view').classList.remove('d-none');
        scwLoadRooms();
    }

    function scwLoadRooms() {
        fetch("{{ route('student.chat.rooms') }}")
            .then(res => res.json())
            .then(data => {
                document.querySelector('#scw-rooms-view .scw-loading').classList.add('d-none');
                const list = document.getElementById('scw-rooms-list');
                list.classList.remove('d-none');
                list.innerHTML = '';
                
                let totalUnread = 0;

                // Auto-open if there is exactly 1 room
                if (data.rooms.length === 1) {
                    scwOpenRoom(data.rooms[0].id, data.rooms[0].name);
                }

                data.rooms.forEach(room => {
                    totalUnread += room.unread_count;
                    const icon = room.type === 'global' ? 
                        `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m7.5-6.923c-.67.204-1.335.82-1.887 1.855A8 8 0 0 0 5.145 4H7.5zM4.09 4a9.3 9.3 0 0 1 .64-1.539 7 7 0 0 1 .597-.933A7.03 7.03 0 0 0 2.256 4zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a7 7 0 0 0-.656 2.5zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5zM8.5 5v2.5h2.99a12.5 12.5 0 0 0-.337-2.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5zM5.145 12q.208.58.468 1.068c.552 1.035 1.218 1.65 1.887 1.855V12zm.182 2.472a7 7 0 0 1-.597-.933A9.3 9.3 0 0 1 4.09 12H2.255a7 7 0 0 0 3.072 2.472M3.82 11a13.7 13.7 0 0 1-.312-2.5h-1.83a7 7 0 0 0 .656 2.5zm6.853 3.472A7 7 0 0 0 13.745 12H11.91a9.3 9.3 0 0 1-.64 1.539 7 7 0 0 1-.597.933M8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855q.26-.487.468-1.068zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-1.83a13.7 13.7 0 0 1-.312 2.5m-.312-4.5h1.83a7 7 0 0 0-.656-2.5h-2.146c.174.782.282 1.623.312 2.5M11.27 2.461c.247.464.462.98.64 1.539h1.835a7 7 0 0 0-3.072-2.472c.218.284.418.598.597.933M10.855 4a8 8 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4z"/></svg>` : 
                        `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a3 3 0 1 0 0-6 3 3 0 0 0 0 6m0-5a2 2 0 1 1 0 4 2 2 0 0 1 0-4m-6 8s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zM1 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm7.022-1H2.004c-.005-.234-.15-.997-.73-1.685C.712 10.629-.258 10 1.458 10c1.393 0 2.455.517 3.09 1.183.336.352.576.745.69 1.06.183-.037.382-.058.595-.058q.49 0 .937.124zM5.5 7a3 3 0 1 0 0-6 3 3 0 0 0 0 6m0-5a2 2 0 1 1 0 4 2 2 0 0 1 0-4"/></svg>`;
                    
                    const unreadBadge = room.unread_count > 0 ? `<span class="badge bg-danger rounded-pill ms-auto">${room.unread_count}</span>` : '';
                    
                    list.innerHTML += `
                        <div class="scw-room-item" onclick="scwOpenRoom(${room.id}, '${room.name}')">
                            <div class="scw-room-icon">${icon}</div>
                            <div class="scw-room-details">
                                <div class="scw-room-name">${room.name}</div>
                                <div class="scw-room-type">${room.type} Group</div>
                            </div>
                            ${unreadBadge}
                        </div>
                    `;
                });

                const badge = document.getElementById('scw-badge');
                if (totalUnread > 0) {
                    badge.textContent = totalUnread;
                    badge.classList.remove('d-none');
                } else {
                    badge.classList.add('d-none');
                }
            })
            .catch(err => console.error("Chat error:", err));
    }

    function scwOpenRoom(id, name) {
        scwCurrentRoom = id;
        document.getElementById('scw-chat-title').innerText = name;
        document.getElementById('scw-rooms-view').classList.add('d-none');
        document.getElementById('scw-chat-view').classList.remove('d-none');
        document.getElementById('scw-messages-container').innerHTML = '<div class="text-center text-muted mt-4"><div class="spinner-border spinner-border-sm"></div></div>';
        
        scwLoadMessages();
        scwStartPolling();
    }

    function scwLoadMessages() {
        if (!scwCurrentRoom) return;
        
        fetch(`/student/chat/rooms/${scwCurrentRoom}/messages`)
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('scw-messages-container');
                container.innerHTML = '';
                
                data.messages.forEach(msg => {
                    const typeClass = msg.is_mine ? 'mine' : 'theirs';
                    const sender = msg.is_mine ? 'You' : msg.sender_name;
                    container.innerHTML += `
                        <div class="scw-msg ${typeClass}">
                            <div class="scw-msg-header">
                                <span>${sender}</span>
                                <span>•</span>
                                <span>${msg.created_at}</span>
                            </div>
                            <div class="scw-msg-bubble">${msg.message}</div>
                        </div>
                    `;
                });
                container.scrollTop = container.scrollHeight;
            });
    }

    function scwSendMessage() {
        if (!scwCurrentRoom) return;
        const input = document.getElementById('scw-message-input');
        const text = input.value.trim();
        if (!text) return;
        
        input.value = '';
        const btn = document.getElementById('scw-btn-send');
        btn.disabled = true;

        fetch(`/student/chat/rooms/${scwCurrentRoom}/send`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: text })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            if(data.success) {
                // Immediately render the message
                const container = document.getElementById('scw-messages-container');
                const msg = data.message;
                container.innerHTML += `
                    <div class="scw-msg mine">
                        <div class="scw-msg-header">
                            <span>You</span>
                            <span>•</span>
                            <span>${msg.created_at}</span>
                        </div>
                        <div class="scw-msg-bubble">${msg.message}</div>
                    </div>
                `;
                container.scrollTop = container.scrollHeight;
            }
        })
        .catch(err => {
            btn.disabled = false;
            console.error(err);
        });
    }

    // Handle enter key
    document.getElementById('scw-message-input').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            scwSendMessage();
        }
    });

    // Polling logic
    function scwStartPolling() {
        if(scwPollInterval) clearInterval(scwPollInterval);
        scwPollInterval = setInterval(() => {
            if (scwCurrentRoom) {
                scwLoadMessages(); // In a production app, we'd only fetch NEW messages to save bandwidth, but this is fine for now
            }
        }, 5000);
    }

    function scwStopPolling() {
        if(scwPollInterval) {
            clearInterval(scwPollInterval);
            scwPollInterval = null;
        }
    }

    // Initial background poll for unread badge
    setTimeout(() => {
        if(!scwIsOpen) {
            fetch("{{ route('student.chat.rooms') }}")
                .then(res => res.json())
                .then(data => {
                    let totalUnread = 0;
                    data.rooms.forEach(r => totalUnread += r.unread_count);
                    const badge = document.getElementById('scw-badge');
                    if (totalUnread > 0) {
                        badge.textContent = totalUnread;
                        badge.classList.remove('d-none');
                    }
                });
        }
    }, 2000);

</script>
