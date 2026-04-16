<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TOOLZ - Aplikasi Peminjaman Alat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ========== STYLE CHAT POPUP ========== */
        .chat-popup {
            position: fixed;
            bottom: 120px;
            right: 30px;
            width: 380px;
            height: 500px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            z-index: 10000;
            display: none;
            flex-direction: column;
            overflow: hidden;
            animation: slideUp 0.3s ease-out;
        }

        .chat-popup.show {
            display: flex;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .chat-header {
            background: linear-gradient(135deg, #44619e 0%, #8cbcd4 100%);
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }

        .chat-header h6 {
            margin: 0;
            font-weight: 600;
        }

        .chat-header i {
            cursor: pointer;
            transition: transform 0.2s;
        }

        .chat-header i:hover {
            transform: scale(1.1);
        }

        .chat-body {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
            background: #f8f9fa;
        }

        .chat-message {
            margin-bottom: 15px;
            display: flex;
            animation: messageIn 0.3s ease-out;
        }

        @keyframes messageIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .chat-message.user {
            justify-content: flex-end;
        }

        .chat-message.bot {
            justify-content: flex-start;
        }

        .message-bubble {
            max-width: 80%;
            padding: 10px 15px;
            border-radius: 18px;
            font-size: 14px;
            line-height: 1.4;
        }

        .chat-message.user .message-bubble {
            background: linear-gradient(135deg, #44619e 0%, #8cbcd4 100%);
            color: white;
            border-bottom-right-radius: 5px;
        }

        .chat-message.bot .message-bubble {
            background: white;
            color: #333;
            border: 1px solid #e0e0e0;
            border-bottom-left-radius: 5px;
        }

        .chat-footer {
            padding: 15px;
            background: white;
            border-top: 1px solid #e0e0e0;
            display: flex;
            gap: 10px;
        }

        .chat-footer input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 25px;
            outline: none;
            font-size: 14px;
        }

        .chat-footer input:focus {
            border-color: #d45c70;
        }

        .chat-footer button {
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            background: linear-gradient(135deg, #44619e 0%, #8cbcd4 100%);
            color: white;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .chat-footer button:hover {
            transform: scale(1.05);
        }

        .typing-indicator {
            display: flex;
            gap: 5px;
            padding: 10px 15px;
            background: white;
            border-radius: 18px;
            border: 1px solid #e0e0e0;
            width: fit-content;
        }

        .typing-indicator span {
            width: 8px;
            height: 8px;
            background: #999;
            border-radius: 50%;
            animation: typing 1.4s infinite;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {

            0%,
            60%,
            100% {
                transform: translateY(0);
                opacity: 0.5;
            }

            30% {
                transform: translateY(-10px);
                opacity: 1;
            }
        }

        /* Floating Character */
        .floating-character {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .floating-character:hover {
            transform: scale(1.05);
        }

        .floating-character img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            background: white;
            padding: 5px;
        }

        /* Badge Notifikasi */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: red;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }
        }
    </style>
</head>

<body class="text-dark m-0">
    <div class="fullscreen-gradient">
        <nav class="navbar navbar-expand-lg navbar-light shadow mb-4 sticky-top">
            <div class="container">
                <a class="navbar-brand" href="#">TOOLZ</a>
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav me-auto">
                        @auth
                            @if (auth()->user()->role == 'admin')
                                <li class="nav-item"><a class="nav-link" href="/admin/dashboard">Dashboard</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('categories.index') }}">Kategori</a>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('tools.index') }}">Alat</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('users.index') }}">User</a></li>
                                <li class="nav-item"><a class="nav-link"
                                        href="{{ route('admin.loans.index') }}">Peminjaman</a></li>
                                <li class="nav-item"><a class="nav-link"
                                        href="{{ route('admin.returns.index') }}">Pengembalian</a></li>
                            @elseif(auth()->user()->role == 'petugas')
                                <li class="nav-item"><a class="nav-link" href="/petugas/dashboard">Validasi Peminjaman</a>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="/petugas/laporan">Laporan</a></li>
                            @elseif(auth()->user()->role == 'peminjam')
                                <li class="nav-item"><a class="nav-link" href="/peminjam/dashboard">Daftar Alat</a></li>
                                <li class="nav-item"><a class="nav-link" href="/peminjam/riwayat">Riwayat Saya</a></li>
                            @endif
                        @endauth
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        @auth
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    {{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @else
                            <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </div>
    </div>

    <!-- Floating Character -->
    <div class="floating-character" id="floatingChar">
        <img src="{{ asset('images/character.png') }}" alt="AI Assistant" id="char">
        <div class="notification-badge" id="notifBadge" style="display: none;">1</div>
    </div>

    <!-- Chat Popup AI -->
    <div class="chat-popup" id="chatPopup">
        <div class="chat-header" id="chatHeader">
            <h6><i class="fas fa-robot me-2"></i>Asisten AI TOOLZ</h6>
            <i class="fas fa-times" id="closeChat"></i>
        </div>
        <div class="chat-body" id="chatBody">
            <div class="chat-message bot">
                <div class="message-bubble">
                    👋 Halo! Saya Asisten AI TOOLZ.<br>
                    Saya bisa membantu Anda tentang:<br>
                    • 📋 Peminjaman alat<br>
                    • ⚠️ Informasi denda<br>
                    • 📅 Jadwal pengembalian<br>
                    • ❓ Pertanyaan lainnya<br><br>
                    Ada yang bisa saya bantu?
                </div>
            </div>
        </div>
        <div class="chat-footer">
            <input type="text" id="chatInput" placeholder="Tulis pesan..." onkeypress="handleKeyPress(event)">
            <button id="sendBtn"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <script>
        const GEMINI_API_KEY = 'AIzaSyCHhswer-eX60zCE4NK-AtkCnLaRapUUu0'; // Ganti dengan API Key asli Anda
        const GEMINI_API_URL =
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent'; // Ganti dengan API Key asli Anda
        // ========== KONFIGURASI AI ==========
        const USE_AI_API = true; // Set true jika punya API Key

        // Knowledge base untuk mode offline
        const knowledgeBase = {
            'denda': 'Denda keterlambatan adalah Rp 5.000 per hari.',
            'pinjam': 'Cara meminjam: Login sebagai peminjam, pilih alat yang tersedia, klik "Ajukan Peminjaman".',
            'kembali': 'Pengembalian alat bisa dilakukan melalui halaman riwayat.',
            'status': 'Status peminjaman: Pending (menunggu), Disetujui (sedang dipinjam), Ditolak, Kembali.',
            'jadwal': 'Jadwal pengembalian bisa dilihat di halaman riwayat peminjaman.',
            'telat': 'Jika terlambat mengembalikan, Anda akan dikenakan denda Rp 5.000 per hari.',
            'alat': 'Daftar alat bisa dilihat di halaman dashboard peminjam.',
            'bantuan': 'Saya siap membantu! Silakan tanyakan tentang peminjaman, denda, atau jadwal pengembalian.'
        };

        // DOM Elements
        const floatingChar = document.getElementById('floatingChar');
        const chatPopup = document.getElementById('chatPopup');
        const closeChat = document.getElementById('closeChat');
        const chatBody = document.getElementById('chatBody');
        const chatInput = document.getElementById('chatInput');
        const sendBtn = document.getElementById('sendBtn');
        const notifBadge = document.getElementById('notifBadge');

        let isChatOpen = false;

        // Tampilkan pesan di chat
        function addMessage(message, isUser = false) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-message ${isUser ? 'user' : 'bot'}`;
            messageDiv.innerHTML = `<div class="message-bubble">${message}</div>`;
            chatBody.appendChild(messageDiv);
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        // Tampilkan typing indicator
        function showTypingIndicator() {
            const typingDiv = document.createElement('div');
            typingDiv.className = 'chat-message bot';
            typingDiv.id = 'typingIndicator';
            typingDiv.innerHTML = `
                <div class="typing-indicator">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            `;
            chatBody.appendChild(typingDiv);
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        function hideTypingIndicator() {
            const indicator = document.getElementById('typingIndicator');
            if (indicator) indicator.remove();
        }

        // Response offline (knowledge base)
        function getOfflineResponse(userMessage) {
            const message = userMessage.toLowerCase();

            if (message.includes('denda') || message.includes('telat') || message.includes('terlambat')) {
                return "⚠️ Denda keterlambatan adalah Rp 5.000 per hari. Semakin cepat dikembalikan, semakin kecil dendanya! 😊";
            }
            if (message.includes('pinjam') || message.includes('cara meminjam')) {
                return "📋 Cara meminjam: Login sebagai peminjam → Pilih alat → Klik 'Ajukan Peminjaman' → Tunggu persetujuan petugas. Mudah kan? 😊";
            }
            if (message.includes('kembali') || message.includes('pengembalian')) {
                return "🔄 Untuk mengembalikan alat, buka halaman 'Riwayat Saya' → Klik 'Ajukan Pengembalian' → Petugas akan memverifikasi kondisi alat.";
            }
            if (message.includes('status') || message.includes('proses')) {
                return "📊 Status peminjaman: 🟡 Pending (menunggu), 🟢 Disetujui (sedang dipinjam), 🔴 Ditolak, ✅ Kembali (sudah dikembalikan).";
            }
            if (message.includes('jadwal') || message.includes('kapan')) {
                return "📅 Jadwal pengembalian bisa dilihat di halaman 'Riwayat Saya'. Pastikan mengembalikan tepat waktu ya!";
            }
            if (message.includes('alat') || message.includes('tersedia')) {
                return "🔧 Daftar alat tersedia bisa dilihat di dashboard peminjam. Stok akan update otomatis setiap ada peminjaman!";
            }
            if (message.includes('help') || message.includes('bantuan') || message.includes('tolong')) {
                return "🤝 Saya siap membantu! Silakan tanyakan tentang: peminjaman, pengembalian, denda, jadwal, atau status peminjaman.";
            }
            if (message.includes('terima kasih') || message.includes('makasih')) {
                return "✨ Sama-sama! Senang bisa membantu. Jika ada pertanyaan lain, tanyakan saja ya! 😊";
            }
            if (message.includes('hallo') || message.includes('halo') || message.includes('hai')) {
                return "👋 Halo! Ada yang bisa saya bantu tentang peminjaman alat?";
            }

            return "🤔 Maaf, saya kurang paham. Coba tanyakan tentang: peminjaman, pengembalian, denda, atau jadwal ya! Atau ketik 'bantuan' untuk info lebih lanjut.";
        }
        // 🔥 FUNGSI GEMINI AI - INI YANG BARU 🔥
        async function getGeminiResponse(userMessage) {
            // System prompt untuk AI
            const systemPrompt = `Kamu adalah asisten AI untuk aplikasi peminjaman alat bernama TOOLZ. 
        Aplikasi ini digunakan untuk meminjam alat sekolah/perkantoran.
        
        Informasi yang kamu tahu:
        - Denda keterlambatan: Rp 5.000 per hari
        - Status peminjaman: pending (menunggu), disetujui (sedang dipinjam), ditolak, kembali (sudah dikembalikan)
        - Pengembalian harus diajukan terlebih dahulu melalui halaman riwayat
        
        Jawab dengan ramah, singkat (maksimal 3 kalimat), dan gunakan emoji yang sesuai.
        Gunakan bahasa Indonesia yang baik dan santai.`;

            try {
                const response = await fetch(`${GEMINI_API_URL}?key=${GEMINI_API_KEY}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        contents: [{
                            parts: [{
                                text: `${systemPrompt}\n\nPertanyaan user: ${userMessage}`
                            }]
                        }],
                        generationConfig: {
                            temperature: 0.7,
                            maxOutputTokens: 150,
                        }
                    })
                });

                const data = await response.json();

                if (data.error) {
                    console.error('Gemini API Error:', data.error);
                    return getOfflineResponse(userMessage);
                }

                if (data.candidates && data.candidates[0]?.content?.parts[0]?.text) {
                    return data.candidates[0].content.parts[0].text;
                }

                return getOfflineResponse(userMessage);

            } catch (error) {
                console.error('Error calling Gemini API:', error);
                return getOfflineResponse(userMessage);
            }
        }

        // Dapatkan response dari AI (Gemini atau Offline)
        async function getAIResponse(userMessage) {
            if (USE_AI_API && GEMINI_API_KEY) {
                return await getGeminiResponse(userMessage);
            } else {
                return getOfflineResponse(userMessage);
            }
        }
        // Proses pesan user
        async function processMessage(userMessage) {
            if (!userMessage.trim()) return;

            chatInput.value = '';
            addMessage(userMessage, true);
            showTypingIndicator();

            const response = await getAIResponse(userMessage);

            hideTypingIndicator();
            addMessage(response);
        }

        // Handle send message
        async function sendMessage() {
            const message = chatInput.value.trim();
            if (message) {
                await processMessage(message);
            }
        }

        // Handle key press (Enter)
        function handleKeyPress(event) {
            if (event.key === 'Enter') {
                sendMessage();
            }
        }

        // ========== CHAT POPUP FUNCTIONS ==========
        function openChat() {
            chatPopup.classList.add('show');
            isChatOpen = true;
            notifBadge.style.display = 'none';
            chatInput.focus();
            localStorage.setItem('chatOpened', 'true');
        }

        function closeChatPopup() {
            chatPopup.classList.remove('show');
            isChatOpen = false;
        }

        function toggleChat() {
            if (isChatOpen) {
                closeChatPopup();
            } else {
                openChat();
            }
        }

        // ========== FLOATING CHARACTER CLICK ==========
        floatingChar.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleChat();

            const img = floatingChar.querySelector('img');
            img.style.transform = 'scale(0.9)';
            setTimeout(() => {
                img.style.transform = 'scale(1)';
            }, 150);
        });

        // Close chat
        closeChat.addEventListener('click', closeChatPopup);

        // Send message
        sendBtn.addEventListener('click', sendMessage);
        chatInput.addEventListener('keypress', handleKeyPress);

        // Notifikasi pertama kali
        setTimeout(() => {
            const hasOpened = localStorage.getItem('chatOpened');
            if (!hasOpened && !isChatOpen) {
                notifBadge.style.display = 'flex';
                setTimeout(() => {
                    notifBadge.style.display = 'none';
                }, 10000);
            }
        }, 3000);

        console.log('AI Chat Assistant Ready!');
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
