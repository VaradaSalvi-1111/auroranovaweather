const API_KEY = ""; // Replace with your actual API key
let isSending = false;
let debounceTimeout;

// Function to add messages to chat
function addMessage(text, sender) {
  const div = document.createElement("div");
  div.className = "msg " + sender;
  div.innerText = text;
  const chatBox = document.getElementById("chatBox");
  chatBox.appendChild(div);
  chatBox.scrollTop = chatBox.scrollHeight;
}

// Function to speak the Gemini reply (optional)
function speakGeminiResponse(text) {
  if ('speechSynthesis' in window) {
    const utter = new SpeechSynthesisUtterance(text);
    window.speechSynthesis.speak(utter);
  }
}

// Main function to send message to Gemini API
async function sendMessage() {
  if (isSending) return; // Prevent multiple simultaneous calls

  const input = document.getElementById("userInput");
  const text = input.value.trim();
  if (!text) return;

  addMessage(text, "user");
  input.value = "";

  const sendBtn = document.getElementById("sendBtn");
  sendBtn.disabled = true;
  isSending = true;

  const url = `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=${API_KEY}`;
  const body = {
    contents: [
      { parts: [{ text: text }] }
    ]
  };

  try {
    const res = await fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(body)
    });

    if (res.status === 429) {
      const errorMsg = "Free limit reached. Please wait 1 minute and try again.";
      addMessage(errorMsg, "bot");
      speakGeminiResponse(errorMsg);
      return;
    }

    const data = await res.json();
    console.log("Gemini:", data);

    if (!data.candidates) {
      const errorMsg = "API not enabled or key invalid";
      addMessage(errorMsg, "bot");
      speakGeminiResponse(errorMsg);
      return;
    }

    const reply = data.candidates[0].content.parts[0].text;
    addMessage(reply, "bot");
    speakGeminiResponse(reply);

  } catch (err) {
    console.error(err);
    const errorMsg = "Connection error";
    addMessage(errorMsg, "bot");
    speakGeminiResponse(errorMsg);
  } finally {
    isSending = false;
    sendBtn.disabled = false;
  }
}

// Debounce wrapper
function debounceSend() {
  if (debounceTimeout) clearTimeout(debounceTimeout);
  debounceTimeout = setTimeout(sendMessage, 300); // 300ms debounce
}

// Event listeners
document.getElementById("sendBtn").addEventListener("click", debounceSend);
document.getElementById("userInput").addEventListener("keydown", (e) => {
  if (e.key === "Enter") {
    debounceSend();
  }
});