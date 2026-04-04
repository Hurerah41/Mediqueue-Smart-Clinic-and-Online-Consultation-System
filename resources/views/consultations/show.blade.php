@extends('layouts.app', ['title' => 'Consultation | MediQueue'])

@section('content')
    <div class="grid lg:grid-cols-3 gap-6">
        <section class="lg:col-span-2 bg-white rounded-[2rem] border border-slate-100 p-8 shadow-soft">
            <p class="text-sm uppercase tracking-widest text-primary font-bold">Online Consultation</p>
            <h1 class="text-3xl font-extrabold tracking-tight text-dark mt-2">Dr. {{ $consultation->appointment->doctor->user->name }} & {{ $consultation->patient->name }}</h1>

            @if ($consultation->video_room_url)
                <a href="{{ $consultation->video_room_url }}" target="_blank" class="inline-flex mt-5 bg-gradient-premium text-white px-6 py-3 rounded-2xl font-bold shadow-glow">Join Video Room</a>
            @endif

            <div id="chat-thread" class="mt-8 rounded-[2rem] bg-slate-50 p-5 h-[420px] overflow-y-auto space-y-4 hide-scrollbar">
                <p class="text-slate-500">Loading messages...</p>
            </div>

            <form id="chat-form" method="POST" action="{{ route('consultations.messages.store', $consultation) }}" class="flex gap-3 mt-5">
                @csrf
                <input id="chat-message" name="message" class="form-control flex-1" placeholder="Type your message..." required autocomplete="off">
                <button class="bg-dark text-white px-6 py-3 rounded-2xl font-bold hover:shadow-lg transition-all" type="submit">Send</button>
            </form>
        </section>

        <aside class="glass-panel rounded-[2rem] p-8 shadow-glass">
            <h2 class="text-2xl font-extrabold text-dark mb-4">Session Info</h2>
            <div class="space-y-3 text-slate-700">
                <div><strong>Clinic:</strong> {{ $consultation->appointment->clinic->name }}</div>
                <div><strong>Date:</strong> {{ $consultation->appointment->appointment_date->format('d M Y') }}</div>
                <div><strong>Status:</strong> {{ ucfirst($consultation->status) }}</div>
                <div><strong>Mode:</strong> {{ ucfirst($consultation->mode) }}</div>
            </div>
            <a href="{{ route('appointments.show', $consultation->appointment) }}" class="inline-flex mt-8 text-primary font-semibold items-center gap-2">Back to appointment <i class="ph-bold ph-arrow-right"></i></a>
        </aside>
    </div>

    <script>
        const chatThread = document.getElementById('chat-thread');
        const chatForm = document.getElementById('chat-form');
        const chatMessage = document.getElementById('chat-message');
        const currentUserId = {{ auth()->id() }};
        const messageFeedUrl = "{{ route('consultations.messages.index', $consultation) }}";
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function escapeHtml(value) {
            return String(value)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function renderMessages(messages) {
            if (!messages.length) {
                chatThread.innerHTML = '<p class="text-slate-500">No messages yet. Start the consultation chat.</p>';
                return;
            }

            chatThread.innerHTML = messages.map((message) => {
                const ownMessage = Number(message.sender_id) === Number(currentUserId);
                const bubbleClass = ownMessage
                    ? 'bg-gradient-premium text-white ml-auto shadow-glow'
                    : 'bg-white border border-slate-100 shadow-sm';

                return `
                    <div class="max-w-xl rounded-3xl p-4 ${bubbleClass}">
                        <div class="text-xs opacity-75 mb-1">${escapeHtml(message.sender_name)} | ${escapeHtml(message.sent_at)}</div>
                        <div>${escapeHtml(message.message)}</div>
                    </div>
                `;
            }).join('');

            chatThread.scrollTop = chatThread.scrollHeight;
        }

        async function loadMessages() {
            const response = await fetch(messageFeedUrl, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            renderMessages(data.messages);
        }

        chatForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const response = await fetch(chatForm.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: chatMessage.value })
            });

            if (response.ok) {
                chatMessage.value = '';
                await loadMessages();
            }
        });

        loadMessages();
        setInterval(loadMessages, 3000);
    </script>
@endsection
