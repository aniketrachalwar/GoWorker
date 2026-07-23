// GoWorker Premium Chat System Engine
// chat.js

document.addEventListener("DOMContentLoaded", () => {
    // 1. STATE & CONSTANTS
    const chatInput = document.getElementById("chat-message-input");
    const sendBtn = document.getElementById("chat-send-btn");
    const msgArea = document.getElementById("chat-message-area");
    const layoutWrapper = document.getElementById("chat-layout-wrapper");
    const backBtn = document.getElementById("chat-back-btn");
    
    // Conversation switching elements
    const activeChatAvatar = document.getElementById("active-chat-avatar");
    const activeChatName = document.getElementById("active-chat-name");
    const activeChatProf = document.getElementById("active-chat-prof");
    const rightSideAvatar = document.getElementById("right-side-avatar");
    const rightSideName = document.getElementById("right-side-name");
    const rightSideProf = document.getElementById("right-side-prof");
    const convCards = document.querySelectorAll(".conv-card");
    const convSearchInput = document.getElementById("conversation-search");

    // Emoji elements
    const emojiPicker = document.getElementById("emoji-picker");
    const emojiTriggerBtn = document.getElementById("emoji-trigger-btn");
    const emojiItems = document.querySelectorAll(".emoji-item");

    // File elements
    const fileTriggerBtn = document.getElementById("file-trigger-btn");
    const hiddenFileInput = document.getElementById("hidden-file-input");
    const documentPreviewBar = document.getElementById("document-preview-bar");
    const docPreviewName = document.getElementById("doc-preview-name");
    const docPreviewIcon = document.getElementById("doc-preview-icon");
    const cancelDocUploadBtn = document.getElementById("cancel-doc-upload");

    // Image preview modal elements
    const imagePreviewModal = document.getElementById("image-preview-modal");
    const closeImageModalBtn = document.getElementById("close-image-modal");
    const imageModalPreviewSrc = document.getElementById("image-modal-preview-src");
    const btnCancelImageSend = document.getElementById("btn-cancel-image-send");
    const btnConfirmImageSend = document.getElementById("btn-confirm-image-send");

    // Voice recording elements
    const micTriggerBtn = document.getElementById("mic-trigger-btn");
    const voiceRecordBar = document.getElementById("voice-record-bar");
    const inputControlsRow = document.getElementById("input-controls-row");
    const recordTimerEl = document.getElementById("record-timer");
    const btnCancelRecord = document.getElementById("btn-cancel-record");
    const btnStopRecord = document.getElementById("btn-stop-record");
    const btnSendRecord = document.getElementById("btn-send-record");

    // Drag & Drop elements
    const dragOverlay = document.getElementById("drag-overlay");

    // Active State
    let activeWorkerId = "2"; // Default is Ramesh Kumar
    let pendingDocument = null;
    let pendingImageSrc = null;
    
    // Voice Recording variables
    let recordInterval = null;
    let recordSeconds = 0;
    let isRecording = false;
    let recordedAudioUrl = null;

    // In-memory chat database to persist conversations during active session
    const chatHistory = {
        "2": [
            { type: "incoming", text: "Hello, I have confirmed your booking request for the Emergency Electrical Troubleshooting.", time: "08:35 AM", status: "read" },
            { type: "outgoing", text: "Great, thank you Ramesh. How long will it take for you to reach my address?", time: "08:42 AM", status: "read" },
            { type: "incoming", text: "I am packing my tools now and starting my bike. I will reach in about 15 minutes.", time: "09:12 AM", status: "read" }
        ],
        "3": [
            { type: "incoming", text: "Hi there, this is Sohan Singh. I've looked at the leakage request.", time: "Yesterday 02:00 PM", status: "read" },
            { type: "outgoing", text: "Leakage is resolved, thank you!", time: "Yesterday 03:30 PM", status: "read" },
            { type: "incoming", text: "Plumber - Pipe leakage resolved!", time: "Yesterday 03:40 PM", status: "read" }
        ]
    };

    // 2. HELPER FUNCTIONS
    function getCurrentTime() {
        const d = new Date();
        let hours = d.getHours();
        let minutes = d.getMinutes();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12;
        minutes = minutes < 10 ? '0' + minutes : minutes;
        return hours + ':' + minutes + ' ' + ampm;
    }

    function escapeHTML(str) {
        return str.replace(/[&<>'"]/g, 
            tag => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                "'": '&#39;',
                '"': '&quot;'
            }[tag] || tag)
        );
    }

    function scrollToBottom() {
        if (msgArea) {
            msgArea.scrollTo({
                top: msgArea.scrollHeight,
                behavior: "smooth"
            });
        }
    }

    // 3. RENDER CONVERSATION HISTORY
    function renderConversation(workerId) {
        if (!msgArea) return;
        msgArea.innerHTML = "";
        
        const history = chatHistory[workerId] || [];
        
        history.forEach(msg => {
            const bubble = document.createElement("div");
            bubble.className = `msg-bubble msg-${msg.type}`;
            
            let innerHTML = "";
            
            // Check media types
            if (msg.mediaType === "image") {
                innerHTML = `
                    <div class="msg-image-content">
                        <img src="${msg.mediaSrc}" alt="Sent image" onclick="window.open('${msg.mediaSrc}')">
                    </div>
                `;
                if (msg.text) {
                    innerHTML += `<p style="margin: 0;">${escapeHTML(msg.text)}</p>`;
                }
            } else if (msg.mediaType === "document") {
                innerHTML = `
                    <div class="msg-file-content">
                        <i class="fa-solid ${msg.fileIcon || 'fa-file'} msg-file-icon"></i>
                        <div class="msg-file-info">
                            <div class="msg-file-name">${escapeHTML(msg.fileName)}</div>
                            <div class="msg-file-size">${msg.fileSize || 'Unknown size'}</div>
                        </div>
                        <button type="button" class="btn-file-dl" onclick="alert('Downloading: ${escapeHTML(msg.fileName)}')">
                            <i class="fa-solid fa-download"></i>
                        </button>
                    </div>
                `;
            } else if (msg.mediaType === "voice") {
                innerHTML = `
                    <div class="msg-voice-content">
                        <button class="voice-play-btn" onclick="toggleVoicePlay(this)">
                            <i class="fa-solid fa-play"></i>
                        </button>
                        <div class="voice-waveform-container">
                            <div class="voice-wave-bar active"></div>
                            <div class="voice-wave-bar active"></div>
                            <div class="voice-wave-bar"></div>
                            <div class="voice-wave-bar"></div>
                            <div class="voice-wave-bar"></div>
                            <div class="voice-wave-bar"></div>
                            <div class="voice-wave-bar"></div>
                            <div class="voice-wave-bar"></div>
                            <div class="voice-wave-bar"></div>
                            <div class="voice-wave-bar"></div>
                        </div>
                        <span class="voice-duration" style="font-size: 11px; margin-right: 4px;">${msg.duration}</span>
                    </div>
                `;
            } else {
                innerHTML = `<p style="margin: 0;">${escapeHTML(msg.text)}</p>`;
            }
            
            // Append time and ticks for outgoing
            if (msg.type === "outgoing") {
                innerHTML += `
                    <div class="msg-status-row">
                        <span class="msg-time">${msg.time}</span>
                        <span class="msg-status-ticks"><i class="fa-solid fa-check-double"></i></span>
                    </div>
                `;
            } else {
                innerHTML += `
                    <div class="msg-status-row">
                        <span class="msg-time">${msg.time}</span>
                    </div>
                `;
            }
            
            bubble.innerHTML = innerHTML;
            msgArea.appendChild(bubble);
        });
        
        scrollToBottom();
    }

    // Dynamic voice message playback simulation
    window.toggleVoicePlay = function(btn) {
        const icon = btn.querySelector("i");
        const waveform = btn.nextElementSibling;
        
        if (icon.classList.contains("fa-play")) {
            // Start Playing
            icon.className = "fa-solid fa-pause";
            waveform.classList.add("playing");
            
            // Highlight wave bars
            const bars = waveform.querySelectorAll(".voice-wave-bar");
            let index = 0;
            const highlightInterval = setInterval(() => {
                if (index < bars.length) {
                    bars[index].classList.add("active");
                    index++;
                } else {
                    clearInterval(highlightInterval);
                }
            }, 300);
            
            setTimeout(() => {
                icon.className = "fa-solid fa-play";
                waveform.classList.remove("playing");
                bars.forEach((b, i) => {
                    if (i > 1) b.classList.remove("active");
                });
                clearInterval(highlightInterval);
            }, 3000);
        } else {
            // Pause
            icon.className = "fa-solid fa-play";
            waveform.classList.remove("playing");
        }
    };

    // 4. CONVERSATION SWITCHING
    convCards.forEach(card => {
        card.addEventListener("click", () => {
            convCards.forEach(c => c.classList.remove("active"));
            card.classList.add("active");
            
            activeWorkerId = card.getAttribute("data-worker-id");
            const name = card.getAttribute("data-name");
            const profession = card.getAttribute("data-profession");
            const avatar = card.getAttribute("data-avatar");
            const locationStr = card.getAttribute("data-location");
            
            // Clear unread badge
            const badge = card.querySelector(".unread-badge");
            const badgeContainer = card.querySelector(".conv-badge-container");
            if (badge) badge.textContent = "0";
            if (badgeContainer) badgeContainer.style.display = "none";
            
            // Update Active Chat Header
            if (activeChatAvatar) activeChatAvatar.src = avatar;
            if (activeChatName) activeChatName.textContent = name;
            if (activeChatProf) activeChatProf.textContent = `${profession} • Online`;
            
            // Update Right Sidebar
            if (rightSideAvatar) rightSideAvatar.src = avatar;
            if (rightSideName) rightSideName.textContent = name;
            if (rightSideProf) rightSideProf.textContent = `${profession} • ${locationStr}`;
            
            // Switch responsive layout for mobile
            if (layoutWrapper) {
                layoutWrapper.classList.add("chat-active");
            }
            
            renderConversation(activeWorkerId);
        });
    });

    if (backBtn && layoutWrapper) {
        backBtn.addEventListener("click", () => {
            layoutWrapper.classList.remove("chat-active");
        });
    }

    // 5. SEARCH CONVERSATIONS
    if (convSearchInput) {
        convSearchInput.addEventListener("input", (e) => {
            const query = e.target.value.toLowerCase().trim();
            convCards.forEach(card => {
                const name = card.getAttribute("data-name").toLowerCase();
                const profession = card.getAttribute("data-profession").toLowerCase();
                const lastMsg = card.querySelector(".conv-last-msg").textContent.toLowerCase();
                
                if (name.includes(query) || profession.includes(query) || lastMsg.includes(query)) {
                    card.style.display = "flex";
                } else {
                    card.style.display = "none";
                }
            });
        });
    }

    // 6. EMOJI SYSTEM
    if (emojiTriggerBtn && emojiPicker) {
        emojiTriggerBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            if (emojiPicker.style.display === "flex") {
                emojiPicker.style.display = "none";
            } else {
                emojiPicker.style.display = "flex";
            }
        });

        // Close on outside click
        document.addEventListener("click", (e) => {
            if (!emojiPicker.contains(e.target) && e.target !== emojiTriggerBtn) {
                emojiPicker.style.display = "none";
            }
        });
    }

    emojiItems.forEach(item => {
        item.addEventListener("click", () => {
            if (chatInput) {
                const text = item.textContent;
                const startPos = chatInput.selectionStart;
                const endPos = chatInput.selectionEnd;
                chatInput.value = chatInput.value.substring(0, startPos) + text + chatInput.value.substring(endPos);
                chatInput.focus();
                chatInput.selectionStart = startPos + text.length;
                chatInput.selectionEnd = startPos + text.length;
            }
        });
    });

    // 7. FILE UPLOAD & IMAGE PREVIEW
    if (fileTriggerBtn && hiddenFileInput) {
        fileTriggerBtn.addEventListener("click", () => {
            hiddenFileInput.click();
        });
    }

    if (hiddenFileInput) {
        hiddenFileInput.addEventListener("change", (e) => {
            if (e.target.files.length > 0) {
                handleFileUpload(e.target.files[0]);
            }
        });
    }

    function handleFileUpload(file) {
        // Size Check (10MB)
        const maxSize = 10 * 1024 * 1024;
        if (file.size > maxSize) {
            alert("File size exceeds 10MB limit! Please upload a smaller file.");
            return;
        }

        const ext = file.name.split('.').pop().toLowerCase();
        
        if (["jpg", "jpeg", "png", "webp"].includes(ext)) {
            // Image Preview Modal
            const reader = new FileReader();
            reader.onload = (e) => {
                pendingImageSrc = e.target.result;
                if (imageModalPreviewSrc) {
                    imageModalPreviewSrc.src = pendingImageSrc;
                }
                if (imagePreviewModal) {
                    imagePreviewModal.style.display = "flex";
                }
            };
            reader.readAsDataURL(file);
        } else if (["pdf", "doc", "docx"].includes(ext)) {
            // Document Pre-send preview strip
            pendingDocument = {
                file: file,
                name: file.name,
                size: (file.size / (1024 * 1024)).toFixed(2) + " MB",
                icon: ext === "pdf" ? "fa-file-pdf" : "fa-file-word"
            };
            
            if (docPreviewName) docPreviewName.textContent = pendingDocument.name;
            if (docPreviewIcon) {
                docPreviewIcon.className = `fa-solid ${pendingDocument.icon} msg-file-icon`;
            }
            if (documentPreviewBar) {
                documentPreviewBar.style.display = "flex";
            }
            if (chatInput) {
                chatInput.placeholder = "Click Send button to upload document...";
                chatInput.disabled = true;
            }
        } else {
            alert("Unsupported file format! Allowed: JPG, PNG, WEBP, PDF, DOC, DOCX");
        }
    }

    // Cancel Document Pre-send Upload
    if (cancelDocUploadBtn) {
        cancelDocUploadBtn.addEventListener("click", () => {
            pendingDocument = null;
            if (documentPreviewBar) documentPreviewBar.style.display = "none";
            if (chatInput) {
                chatInput.placeholder = "Type a message...";
                chatInput.disabled = false;
                chatInput.focus();
            }
            if (hiddenFileInput) hiddenFileInput.value = "";
        });
    }

    // Image Modal Control Actions
    const closeImageModal = () => {
        pendingImageSrc = null;
        if (imagePreviewModal) imagePreviewModal.style.display = "none";
        if (hiddenFileInput) hiddenFileInput.value = "";
    };

    if (closeImageModalBtn) closeImageModalBtn.addEventListener("click", closeImageModal);
    if (btnCancelImageSend) btnCancelImageSend.addEventListener("click", closeImageModal);

    if (btnConfirmImageSend) {
        btnConfirmImageSend.addEventListener("click", () => {
            if (!pendingImageSrc) return;
            
            // Append outgoing image bubble
            const newMsg = {
                type: "outgoing",
                mediaType: "image",
                mediaSrc: pendingImageSrc,
                text: "",
                time: getCurrentTime(),
                status: "sent"
            };
            
            if (!chatHistory[activeWorkerId]) chatHistory[activeWorkerId] = [];
            chatHistory[activeWorkerId].push(newMsg);
            
            closeImageModal();
            renderConversation(activeWorkerId);
            updateLastMessageOnCard(activeWorkerId, "Sent an image");
            
            // Trigger simulated worker response
            simulateWorkerReply("image");
        });
    }

    // 8. VOICE MESSAGE RECORDING SYSTEM
    if (micTriggerBtn) {
        micTriggerBtn.addEventListener("click", () => {
            // Start recording state
            isRecording = true;
            recordSeconds = 0;
            if (recordTimerEl) recordTimerEl.textContent = "00:00";
            
            if (inputControlsRow) inputControlsRow.style.display = "none";
            if (voiceRecordBar) voiceRecordBar.style.display = "flex";
            if (btnSendRecord) btnSendRecord.style.display = "none";
            if (btnStopRecord) btnStopRecord.style.display = "inline-block";

            // Timer Interval
            recordInterval = setInterval(() => {
                recordSeconds++;
                const mins = Math.floor(recordSeconds / 60).toString().padStart(2, "0");
                const secs = (recordSeconds % 60).toString().padStart(2, "0");
                if (recordTimerEl) recordTimerEl.textContent = `${mins}:${secs}`;
            }, 1000);
        });
    }

    if (btnCancelRecord) {
        btnCancelRecord.addEventListener("click", () => {
            stopRecordingSession();
            if (voiceRecordBar) voiceRecordBar.style.display = "none";
            if (inputControlsRow) inputControlsRow.style.display = "flex";
        });
    }

    if (btnStopRecord) {
        btnStopRecord.addEventListener("click", () => {
            // Stop recording timer
            isRecording = false;
            clearInterval(recordInterval);
            
            // Show Send Button
            if (btnStopRecord) btnStopRecord.style.display = "none";
            if (btnSendRecord) btnSendRecord.style.display = "inline-block";
        });
    }

    if (btnSendRecord) {
        btnSendRecord.addEventListener("click", () => {
            const mins = Math.floor(recordSeconds / 60).toString().padStart(2, "0");
            const secs = (recordSeconds % 60).toString().padStart(2, "0");
            const finalDuration = `${mins}:${secs}`;
            
            // Create playable simulated voice bubble
            const newMsg = {
                type: "outgoing",
                mediaType: "voice",
                duration: finalDuration,
                time: getCurrentTime(),
                status: "sent"
            };
            
            if (!chatHistory[activeWorkerId]) chatHistory[activeWorkerId] = [];
            chatHistory[activeWorkerId].push(newMsg);
            
            stopRecordingSession();
            if (voiceRecordBar) voiceRecordBar.style.display = "none";
            if (inputControlsRow) inputControlsRow.style.display = "flex";
            
            renderConversation(activeWorkerId);
            updateLastMessageOnCard(activeWorkerId, "Voice message (" + finalDuration + ")");
            
            // Trigger simulated worker response
            simulateWorkerReply("voice");
        });
    }

    function stopRecordingSession() {
        isRecording = false;
        clearInterval(recordInterval);
        recordSeconds = 0;
    }

    // 9. DRAG & DROP FILE UPLOAD
    if (layoutWrapper && dragOverlay) {
        layoutWrapper.addEventListener("dragenter", (e) => {
            e.preventDefault();
            dragOverlay.style.display = "flex";
            dragOverlay.classList.add("drag-over");
        });

        dragOverlay.addEventListener("dragover", (e) => {
            e.preventDefault();
        });

        dragOverlay.addEventListener("dragleave", (e) => {
            e.preventDefault();
            dragOverlay.style.display = "none";
            dragOverlay.classList.remove("drag-over");
        });

        dragOverlay.addEventListener("drop", (e) => {
            e.preventDefault();
            dragOverlay.style.display = "none";
            dragOverlay.classList.remove("drag-over");
            
            if (e.dataTransfer.files.length > 0) {
                handleFileUpload(e.dataTransfer.files[0]);
            }
        });
    }

    // Helper: update card's snippet row
    function updateLastMessageOnCard(workerId, text) {
        const card = document.querySelector(`.conv-card[data-worker-id="${workerId}"]`);
        if (card) {
            const snippet = card.querySelector(".conv-last-msg");
            const timeSpan = card.querySelector(".conv-time");
            
            if (snippet) snippet.textContent = text;
            if (timeSpan) timeSpan.textContent = getCurrentTime();
            
            // Move to top of card container
            const container = document.getElementById("conversations-container");
            if (container) {
                container.prepend(card);
            }
        }
    }

    // 10. TYPING INDICATOR & SYSTEM AUTO-REPLY
    function showTypingIndicator() {
        const bubble = document.createElement("div");
        bubble.className = "typing-indicator-bubble";
        bubble.id = "active-typing-indicator";
        
        const activeName = document.getElementById("active-chat-name").textContent;
        bubble.innerHTML = `
            <span>${escapeHTML(activeName)} is typing</span>
            <div class="bouncing-dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
        `;
        msgArea.appendChild(bubble);
        scrollToBottom();
    }

    function removeTypingIndicator() {
        const el = document.getElementById("active-typing-indicator");
        if (el) el.remove();
    }

    function simulateWorkerReply(triggerType) {
        showTypingIndicator();
        
        setTimeout(() => {
            removeTypingIndicator();
            
            let replyText = "Hello! Yes, I am on my way to your address. I will reach in about 15 minutes. See you soon!";
            
            if (triggerType === "image") {
                replyText = "That image looks helpful! Let me take a look at the details.";
            } else if (triggerType === "voice") {
                replyText = "Got your voice message! Give me one moment to listen to it.";
            } else if (triggerType === "document") {
                replyText = "Received the document. Let me download and review the specifications.";
            } else {
                const customerMsg = triggerType.toLowerCase();
                if (customerMsg.includes("charge") || customerMsg.includes("price")) {
                    replyText = "The starting rate for my service is ₹299. If any additional wire replacement is needed, I'll inform you beforehand!";
                } else if (customerMsg.includes("tool") || customerMsg.includes("ladder")) {
                    replyText = "Yes, I carry all the standard electrical tools, testing units, and ladders required for troubleshooting.";
                } else if (customerMsg.includes("delay") || customerMsg.includes("late")) {
                    replyText = "Apologies for the delay! There is a bit of traffic on the highway, but I will make it in another 10 minutes.";
                } else if (customerMsg.includes("leak") || customerMsg.includes("water")) {
                    replyText = "I will bring pipeline sealing tapes and new copper pipes just in case.";
                }
            }
            
            const newMsg = {
                type: "incoming",
                text: replyText,
                time: getCurrentTime()
            };
            
            if (!chatHistory[activeWorkerId]) chatHistory[activeWorkerId] = [];
            chatHistory[activeWorkerId].push(newMsg);
            
            renderConversation(activeWorkerId);
            updateLastMessageOnCard(activeWorkerId, replyText);
        }, 1800);
    }

    // 11. SEND MESSAGE HANDLING
    const handleSendAction = () => {
        // Document send branch
        if (pendingDocument) {
            const newMsg = {
                type: "outgoing",
                mediaType: "document",
                fileName: pendingDocument.name,
                fileSize: pendingDocument.size,
                fileIcon: pendingDocument.icon,
                time: getCurrentTime(),
                status: "sent"
            };
            
            if (!chatHistory[activeWorkerId]) chatHistory[activeWorkerId] = [];
            chatHistory[activeWorkerId].push(newMsg);
            
            // Clean document pre-send bar
            pendingDocument = null;
            if (documentPreviewBar) documentPreviewBar.style.display = "none";
            if (chatInput) {
                chatInput.placeholder = "Type a message...";
                chatInput.disabled = false;
                chatInput.focus();
            }
            
            renderConversation(activeWorkerId);
            updateLastMessageOnCard(activeWorkerId, "Sent document: " + newMsg.fileName);
            simulateWorkerReply("document");
            return;
        }

        // Text send branch
        if (!chatInput) return;
        const text = chatInput.value.trim();
        if (text === "") return;

        const newMsg = {
            type: "outgoing",
            text: text,
            time: getCurrentTime(),
            status: "sent"
        };
        
        if (!chatHistory[activeWorkerId]) chatHistory[activeWorkerId] = [];
        chatHistory[activeWorkerId].push(newMsg);
        
        chatInput.value = "";
        renderConversation(activeWorkerId);
        updateLastMessageOnCard(activeWorkerId, text);
        
        // Trigger simulated response
        simulateWorkerReply(text);
    };

    if (sendBtn && chatInput) {
        sendBtn.addEventListener("click", handleSendAction);
        chatInput.addEventListener("keydown", (e) => {
            if (e.key === "Enter") {
                e.preventDefault();
                handleSendAction();
            }
        });
    }

    // Initial load: render default worker history
    renderConversation("2");
});
