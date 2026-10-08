@extends('layouts.student')
@section('title', 'Chat with eBook')
@section('nav_worksheets', 'active')

@push('styles')
<style>
    /* Hide Layout Navbars */
    .sidebar { display: none !important; }
    .main    { padding-bottom: 0 !important; margin: 0 !important; width: 100% !important; background: transparent !important; }
    .content { padding: 0 !important; background: transparent !important; }
    
    html, body {
      overscroll-behavior-y: none; /* Prevent pull to refresh */
    }

    body {
        font-family: 'Quicksand', sans-serif;
        background: linear-gradient(135deg, #f0fdf4 0%, #e0e7ff 100%);
        min-height: 100vh;
        margin: 0;
    }

    .chat-page {
        padding: 80px 16px 40px;
        max-width: 800px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        height: 90vh;
    }

    /* Bubbly Header */
    .chat-header {
        text-align: center;
        margin-bottom: 20px;
        font-family: 'Bubblegum Sans', cursive;
        color: #475569;
        font-size: 2.5rem;
        text-shadow: 2px 2px 0px rgba(0,0,0,0.05);
    }

    /* Bubbly Container */
    .bubbly-container {
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        padding: 20px;
        display: flex;
        flex-direction: column;
        flex: 1;
        overflow: hidden;
    }

    .setup-panel {
        background: #f8fafc;
        padding: 16px;
        border-radius: 16px;
        border: 2px dashed #cbd5e1;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        gap: 12px;
        align-items: center;
    }
    
    .form-group input, .form-group select {
        flex: 1;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 15px;
        font-family: 'Quicksand', sans-serif;
        font-weight: 600;
        color: #1e293b;
        outline: none;
        transition: border-color 0.2s;
    }
    .form-group input:focus, .form-group select:focus {
        border-color: #6a1b9a;
    }

    .btn-bubbly {
        background: linear-gradient(135deg, #9333ea, #6a1b9a);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 16px;
        font-size: 15px;
        font-weight: 700;
        font-family: 'Quicksand', sans-serif;
        cursor: pointer;
        box-shadow: 0 4px 0 #581c87, 0 8px 15px rgba(107, 70, 193, 0.3);
        transition: transform 0.1s, box-shadow 0.1s;
    }
    .btn-bubbly:active {
        transform: translateY(4px);
        box-shadow: 0 0 0 #581c87, 0 4px 10px rgba(107, 70, 193, 0.2);
    }
    .btn-bubbly:disabled {
        background: #94a3b8;
        box-shadow: 0 4px 0 #64748b;
        cursor: not-allowed;
        transform: none;
    }

    /* Chat Area */
    .chat-box {
        flex: 1;
        background: #f1f5f9;
        border-radius: 16px;
        border: 2px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .messages {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .message {
        max-width: 85%;
        padding: 14px 18px;
        border-radius: 20px;
        line-height: 1.5;
        font-size: 15px;
        font-weight: 500;
        position: relative;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .message.bot {
        background: #ffffff;
        align-self: flex-start;
        color: #334155;
        border: 1px solid #e2e8f0;
        border-bottom-left-radius: 4px;
    }

    .message.user {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        align-self: flex-end;
        color: #ffffff;
        border-bottom-right-radius: 4px;
    }

    .message.system {
        align-self: center;
        background: rgba(0,0,0,0.05);
        color: #64748b;
        font-size: 13px;
        font-style: italic;
        padding: 8px 16px;
        border-radius: 20px;
        box-shadow: none;
    }

    .chat-input-area {
        padding: 16px;
        background: #ffffff;
        border-top: 2px solid #e2e8f0;
        display: flex;
        gap: 12px;
    }

    .chat-input-area input {
        flex: 1;
        padding: 14px 20px;
        border: 2px solid #e2e8f0;
        border-radius: 24px;
        font-size: 15px;
        font-family: 'Quicksand', sans-serif;
        font-weight: 600;
        outline: none;
        transition: border-color 0.2s;
    }
    .chat-input-area input:focus {
        border-color: #3b82f6;
    }

    .btn-send {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border: none;
        padding: 0 24px;
        border-radius: 24px;
        font-size: 15px;
        font-weight: 700;
        font-family: 'Quicksand', sans-serif;
        cursor: pointer;
        box-shadow: 0 4px 0 #1d4ed8, 0 8px 15px rgba(37, 99, 235, 0.3);
        transition: transform 0.1s, box-shadow 0.1s;
    }
    .btn-send:active {
        transform: translateY(4px);
        box-shadow: 0 0 0 #1d4ed8;
    }
    .btn-send:disabled {
        background: #94a3b8;
        box-shadow: 0 4px 0 #64748b;
        cursor: not-allowed;
    }

    #loading { display: none; color: #64748b; font-size: 14px; margin-top: 10px; font-weight: 600; text-align: center; }

    @media (max-width: 600px) {
        .chat-page {
            padding: 10px 10px 20px;
        }
        .form-group {
            flex-direction: column;
            align-items: stretch;
        }
    }

</style>
@endpush

@section('content')

<!-- Back Button -->
<a href="javascript:history.back()" style="position: fixed; top: 20px; left: 10px; z-index: 1000; transition: transform 0.15s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
    <img src="{{ asset('uploads/images/buttons/Previous button.png') }}" alt="Back" style="height: 52px; object-fit: contain;">
</a>

<div class="chat-page">
    <h1 class="chat-header">Chat with eBook</h1>

    <div class="bubbly-container">
        
        <div class="setup-panel" id="setupPanel">
            @if(empty($savedChapters))
            <div class="form-group" style="margin-bottom: 10px;" id="fetchInputsGroup">
                <input type="text" id="ebookUrl" placeholder="eBook URL" value="{{ $ebookUrl ?? '' }}" readonly style="background:#f1f5f9; @if($indexPages) display:none; @endif">
                <input type="text" id="indexPages" value="{{ $indexPages }}" @if($indexPages) style="display:none;" @else placeholder="Index Pages (e.g., 3-5)" @endif>
                
                <button id="fetchBtn" class="btn-bubbly" onclick="fetchChapters()">Fetch Chapters</button>
            </div>
            @else
            <input type="hidden" id="ebookUrl" value="{{ $ebookUrl ?? '' }}">
            <input type="hidden" id="indexPages" value="{{ $indexPages }}">
            @endif
            <div id="loading"><span class="animate-spin" style="display:inline-block; margin-right:5px;">⏳</span> Extracting chapters... Please wait.</div>
            
            <div class="form-group" id="chapterSelectorDiv" style="display: none; margin-top: 10px;">
                <label style="font-size: 15px; font-weight: 700; color: #475569; white-space: nowrap;">Select Chapter:</label>
                <select id="chapterSelect"></select>
            </div>
        </div>

        <div class="chat-box">
            <div class="messages" id="messages">
                <div class="message system">Please fetch chapters and select one to start chatting.</div>
            </div>
            <div class="chat-input-area">
                <input type="text" id="chatMsg" placeholder="Ask a question about this chapter..." onkeypress="if(event.key === 'Enter') sendMessage()" disabled>
                <button id="sendBtn" class="btn-send" onclick="sendMessage()" disabled>Send</button>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentChapters = @json($savedChapters ?? []);
    let chatHistory = [];
    
    document.addEventListener('DOMContentLoaded', function () {
        if (currentChapters.length > 0) {
            // Chapters already exist in database
            populateDropdown();
            const fetchInputs = document.getElementById('fetchInputsGroup');
            if (fetchInputs) {
                fetchInputs.style.display = 'none';
            }
            addSystemMessage("Saved chapters loaded. You can now select a chapter and start chatting.");
        } else if ("{{ $indexPages }}" !== "") {
            // If index pages are provided but chapters aren't fetched yet, auto-fetch!
            const fetchInputs = document.getElementById('fetchInputsGroup');
            if (fetchInputs) fetchInputs.style.display = 'none';
            fetchChapters();
        }
    });

    async function fetchChapters() {
        const btn = document.getElementById('fetchBtn');
        const url = document.getElementById('ebookUrl').value.trim();
        const pages = document.getElementById('indexPages').value.trim();
        const loading = document.getElementById('loading');

        if (!url || !pages) {
            return alert("Please provide URL and index pages.");
        }

        btn.disabled = true;
        loading.style.display = 'block';

        try {
            // ==========================================
            // 1. FETCH CHAPTERS FROM AI API
            // ==========================================
            const res = await fetch('https://autoai.aceqr.space/test-paper/get_titles', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    base_url: url,
                    index_pages: pages
                })
            });

            let data;
            const resText = await res.text();
            try {
                data = JSON.parse(resText);
            } catch (jsonError) {
                throw new Error(`Server returned ${res.status} ${res.statusText}. Response: ${resText.substring(0, 100)}...`);
            }

            if (!res.ok || !data.chapters) {
                alert("Error: " + (data.detail || "Failed to fetch chapters"));
                return;
            }

            // ==========================================
            // 2. SAVE CHAPTERS IN LARAVEL DATABASE
            // ==========================================
            const save = await fetch("{{ route('student.chat.saveChapters') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    ebook_id: "{{ $ebookId }}",
                    chapters: data.chapters
                })
            });

            const saveResult = await save.json();

            if (!save.ok || !saveResult.success) {
                console.error(saveResult);
                alert(saveResult.message || "Chapters fetched but could not be saved.");
                return;
            }

            // ==========================================
            // 3. USE SAVED CHAPTERS
            // ==========================================
            currentChapters = saveResult.chapters;
            populateDropdown();
            
            const fetchInputs = document.getElementById('fetchInputsGroup');
            if (fetchInputs) fetchInputs.style.display = 'none';

            addSystemMessage("Chapters fetched and saved successfully.");

            // ==========================================
            // 4. SAVE INDEX PAGE
            // ==========================================
            @if(!$indexPages)
            const indexSave = await fetch("{{ route('student.chat.saveIndex') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    ebook_id: "{{ $ebookId }}",
                    index_page: pages
                })
            });

            if (!indexSave.ok) {
                console.warn("Index page was not updated.");
            }
            @endif

        } catch (e) {
            console.error(e);
            alert("Network error: " + e.message);
        } finally {
            btn.disabled = false;
            loading.style.display = 'none';
        }
    }

    function populateDropdown() {
        const select = document.getElementById('chapterSelect');
        select.innerHTML = "";
        currentChapters.forEach((ch, idx) => {
            const opt = document.createElement('option');
            opt.value = idx;
            opt.textContent = `${ch.title} (Pages ${ch.start_page} - ${ch.end_page || 'end'})`;
            select.appendChild(opt);
        });
        document.getElementById('chapterSelectorDiv').style.display = 'flex';
        document.getElementById('chatMsg').disabled = false;
        document.getElementById('sendBtn').disabled = false;
        
        addSystemMessage("Chapters loaded! You can now ask questions about the selected chapter.");
    }

    function addMessage(role, text) {
        const msgs = document.getElementById('messages');
        const div = document.createElement('div');
        div.className = `message ${role}`;
        div.textContent = text;
        msgs.appendChild(div);
        msgs.scrollTop = msgs.scrollHeight;
    }
    
    function addSystemMessage(text) {
        const msgs = document.getElementById('messages');
        const div = document.createElement('div');
        div.className = `message system`;
        div.textContent = text;
        msgs.appendChild(div);
        msgs.scrollTop = msgs.scrollHeight;
    }

    async function sendMessage() {
        const input = document.getElementById('chatMsg');
        const msg = input.value.trim();
        const chapterIdx = document.getElementById('chapterSelect').value;
        
        if(!msg || chapterIdx === "") return;
        
        const chapter = currentChapters[chapterIdx];
        if(!chapter.end_page) {
            alert("This chapter has no definitive end_page. The backend requires a start and end page for RAG.");
            return;
        }

        // UI update
        input.value = "";
        addMessage('user', msg);
        const btn = document.getElementById('sendBtn');
        btn.disabled = true;
        input.disabled = true;
        
        addSystemMessage("AI is thinking... (If this is the first time querying this chapter, it may take a moment to fetch images)");

        try {
            const payload = {
                base_url: document.getElementById('ebookUrl').value,
                start_page: parseInt(chapter.start_page),
                end_page: parseInt(chapter.end_page),
                message: msg,
                history: chatHistory
            };

            const res = await fetch('https://autoai.aceqr.space/test-paper/chat_with_chapter', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload)
            });
            
            let data;
            const resText = await res.text();
            try {
                data = JSON.parse(resText);
            } catch (jsonError) {
                throw new Error(`Server returned ${res.status} ${res.statusText}. Response: ${resText.substring(0, 100)}...`);
            }
            
            // Remove the system thinking message
            const msgs = document.getElementById('messages');
            if (msgs.lastChild.classList.contains('system')) {
                msgs.removeChild(msgs.lastChild);
            }

            if(res.ok) {
                addMessage('bot', data.response);
                chatHistory.push({role: 'user', content: msg});
                chatHistory.push({role: 'model', content: data.response});
            } else {
                addMessage('system', "Error: " + (data.detail || "Failed to get response"));
            }
        } catch(e) {
            console.error("Chat Error:", e);
            addMessage('system', "Network error: " + e.message);
        } finally {
            btn.disabled = false;
            input.disabled = false;
            input.focus();
        }
    }
</script>
@endpush
