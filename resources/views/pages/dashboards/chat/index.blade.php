<x-default-layout>
    <style>
        #messages {
            max-height: 400px;
            overflow-y: auto;
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .chat-message {
            display: flex;
            align-items: flex-end;
            max-width: 80%;
            padding: 10px;
            border-radius: 10px;
            position: relative;
        }

        .sent {
            align-self: flex-end;
            background: #DCF8C6;
            text-align: right;
        }

        .received {
            align-self: flex-start;
            background: #FFF;
        }

        .chat-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 10px;
        }

        .chat-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .chat-content {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .chat-header {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #555;
        }

        .chat-text {
            font-size: 14px;
            line-height: 1.4;
            word-wrap: break-word;
        }
    </style>
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="col-lg-4">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5 d-flex justify-content-between align-items-center">
                    <h3 class="card-title fw-bold fs-3 mb-1">
                        <i class="fas fa-comments me-2"></i> المحادثات
                    </h3>
                    {{-- <button class="btn btn-sm btn-light btn-active-primary" data-bs-toggle="modal"
                        data-bs-target="#addConversationModal">
                        <i class="ki-duotone ki-plus fs-2"></i> {{ __('lang.add') }}
                    </button> --}}
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach ($conversations as $conversation)
                            <button class="list-group-item list-group-item-action conversation-btn"
                                data-id="{{ $conversation->id }}">
                                <i class="fas fa-comment-dots me-2"></i>
                                {{ $conversation->name ?? 'Private Chat' }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title fw-bold fs-3">
                        <i class="fas fa-comments me-2"></i> المحادثة
                    </h3>
                </div>
                <div class="card-body chat-box" id="messages"
                    style="height: 400px; overflow-y: auto; background: #f9f9f9; padding: 15px;">
                    <p class="text-center text-muted">اختر محادثة لعرض الرسائل</p>
                </div>
                <div class="card-footer">
                    <form id="chat-form" class="d-flex">
                        <input type="hidden" id="conversation_id">
                        <input type="text" id="message-input" class="form-control me-2" placeholder="اكتب رسالتك">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> إرسال</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @section('script')
        <script>
            const CURRENT_USER_ID = {{ auth()->id() }};

            document.addEventListener('DOMContentLoaded', function() {
                let conversationId = null;

                document.querySelectorAll('.conversation-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        conversationId = this.dataset.id;
                        document.getElementById('conversation_id').value = conversationId;
                        loadMessages(conversationId);
                    });
                });


                function loadMessages(id) {
                    fetch(`/chat/messages/${id}`)
                        .then(response => response.json())
                        .then(messages => {
                            let messagesDiv = document.getElementById('messages');
                            messagesDiv.innerHTML = '';

                            messages.forEach(msg => {
                                let userName =
                                    `${msg.user?.first_name ?? 'Unknown'} ${msg.user?.last_name ?? ''}`;
                                let userImage = msg.user?.avatar ?? '/default-avatar.png';
                                let isSender = msg.user_id === CURRENT_USER_ID;

                                let messageBubble = `
                                <div class="chat-message ${isSender ? 'sent' : 'received'}">                                  
                                    <div class="chat-content">
                                        <div class="chat-header">
                                            <strong class="chat-username">${userName}</strong>
                                            <span class="chat-time">${formatTime(msg.created_at)}</span>
                                        </div>
                                        <p class="chat-text">${msg.message}</p>
                                    </div>
                                </div>
                            `;

                                messagesDiv.innerHTML += messageBubble;
                            });

                            messagesDiv.scrollTop = messagesDiv.scrollHeight;
                        })
                        .catch(error => console.error("Error loading messages:", error));
                }

                function formatTime(timestamp) {
                    let date = new Date(timestamp);
                    return date.toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }




                document.getElementById('chat-form').addEventListener('submit', function(e) {
                    e.preventDefault();
                    let messageInput = document.getElementById('message-input');
                    let message = messageInput.value.trim();

                    if (!message || !conversationId) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Oops...',
                            text: 'Please select a conversation and enter a message!',
                        });
                        return;
                    }

                    fetch('/chat/send', {
                            method: 'POST',
                            body: JSON.stringify({
                                conversation_id: conversationId,
                                message: message
                            }),
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                messageInput.value = '';
                                loadMessages(conversationId);

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Message Sent!',
                                    text: 'Your message has been successfully sent.',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Something went wrong while sending your message.',
                                });
                            }
                        })
                        .catch(error => {
                            console.error("Error sending message:", error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to send message. Please try again.',
                            });
                        });
                });
            });
        </script>
    @endsection
</x-default-layout>
