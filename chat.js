// GoWorker Messaging Center
// chat.js

document.addEventListener("DOMContentLoaded", () => {
    const chatInput = document.getElementById("chat-message-input");
    const sendBtn = document.getElementById("chat-send-btn");
    const msgArea = document.getElementById("chat-message-area");
    
    // Auto-scroll chat area on load
    if (msgArea) {
        msgArea.scrollTop = msgArea.scrollHeight;
    }

    // 1. SEND MESSAGE IMPLEMENTATION
    const sendMessage = () => {
        if (!chatInput || !msgArea) return;
        const text = chatInput.value.trim();
        if (text === "") return;

        // Create outgoing bubble
        const bubble = document.createElement("div");
        bubble.className = "msg-bubble msg-outgoing";
        bubble.innerHTML = `
            ${escapeHTML(text)}
            <span class="msg-time">${getCurrentTime()}</span>
        `;
        msgArea.appendChild(bubble);
        chatInput.value = "";
        msgArea.scrollTop = msgArea.scrollHeight;

        // Simulate incoming auto-reply check
        simulateWorkerReply(text);
    };

    if (sendBtn && chatInput) {
        sendBtn.addEventListener("click", sendMessage);
        chatInput.addEventListener("keydown", (e) => {
            if (e.key === "Enter") {
                e.preventDefault();
                sendMessage();
            }
        });
    }

    // 2. SIMULATE RESPONSE SYSTEM
    const simulateWorkerReply = (customerMsg) => {
        setTimeout(() => {
            let replyText = "Hello! Yes, I am on my way to your address. I will reach in about 15 minutes. See you soon!";
            if (customerMsg.toLowerCase().includes("charge") || customerMsg.toLowerCase().includes("price")) {
                replyText = "The starting rate for Emergency Troubleshooting is ₹299. If any additional wire replacement is needed, I'll inform you beforehand!";
            } else if (customerMsg.toLowerCase().includes("tool") || customerMsg.toLowerCase().includes("ladder")) {
                replyText = "Yes, I carry all the standard electrical tools, testing units, and ladders required for troubleshooting.";
            }

            const bubble = document.createElement("div");
            bubble.className = "msg-bubble msg-incoming";
            bubble.innerHTML = `
                ${replyText}
                <span class="msg-time">${getCurrentTime()}</span>
            `;
            msgArea.appendChild(bubble);
            msgArea.scrollTop = msgArea.scrollHeight;
        }, 1500);
    };

    // Helper functions
    function getCurrentTime() {
        const d = new Date();
        let hours = d.getHours();
        let minutes = d.getMinutes();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        minutes = minutes < 10 ? '0'+minutes : minutes;
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

    // 3. CONVERSATION SWITCHING
    const convCards = document.querySelectorAll(".conv-card");
    convCards.forEach(card => {
        card.addEventListener("click", () => {
            convCards.forEach(c => c.classList.remove("active"));
            card.classList.add("active");
            
            const name = card.querySelector(".conv-name-row span").textContent;
            const profession = card.querySelector(".conv-last-msg").textContent;
            
            // Update Active Chat Header & Sidebar info
            document.getElementById("active-chat-name").textContent = name;
            document.getElementById("active-chat-prof").textContent = profession.split(" - ")[0];
            
            document.getElementById("right-side-name").textContent = name;
            document.getElementById("right-side-prof").textContent = profession.split(" - ")[0];
            
            // Clean active chat history and insert placeholder hello
            msgArea.innerHTML = `
                <div class="msg-bubble msg-incoming">
                    Hi there, this is ${name}. How can I assist you with your booking today?
                    <span class="msg-time">${getCurrentTime()}</span>
                </div>
            `;
        });
    });
});
