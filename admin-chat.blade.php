@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- User List Column -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Conversations</div>
                <ul class="list-group list-group-flush">
                    @foreach($users as $user)
                        <li class="list-group-item user-item" data-user-id="{{ $user->id }}" style="cursor: pointer;">
                            {{ $user->name }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <!-- Chat Box Column -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header" id="chat-header">Select a conversation</div>
                <div class="card-body" id="chat-box" style="height: 400px; overflow-y: scroll;"></div>
                <div class="card-footer" id="chat-footer" style="display: none;">
                    <form id="message-form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="receiver_id" id="receiver_id">
                        <div class="input-group">
                            <input type="text" name="message" class="form-control" placeholder="Type your message...">
                            <input type="file" name="file" class="form-control">
                            <button class="btn btn-primary" type="submit">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pusherKey = "{{ env('PUSHER_APP_KEY') }}";
        const pusherCluster = "{{ env('PUSHER_APP_CLUSTER') }}";
        const adminId = "{{ Auth::id() }}";
        let activeUserId = null;
        let pusher = new Pusher(pusherKey, { cluster: pusherCluster, encrypted: true });
        let channel = null;

        function loadChat(userId, userName) {
            if (activeUserId === userId) return;

            activeUserId = userId;
            if (channel) {
                pusher.unsubscribe(channel.name);
            }
            
            document.getElementById('chat-header').innerText = `Chat with ${userName}`;
            document.getElementById('receiver_id').value = userId;
            const chatBox = document.getElementById('chat-box');
            chatBox.innerHTML = 'Loading...';

            axios.get(`/chat/messages/${userId}`).then(response => {
                chatBox.innerHTML = '';
                response.data.forEach(msg => appendMessage(msg, userName));
                document.getElementById('chat-footer').style.display = 'block';
            });
            
            channel = pusher.subscribe(`private-chat.${adminId}.${userId}`);
            channel.bind('App\\Events\\MessageSent', function(data) {
                appendMessage(data.message, userName);
            });
        }

        function appendMessage(message, userName) {
            const chatBox = document.getElementById('chat-box');
            const el = document.createElement('div');
            let content = message.from_id == adminId ? '<strong>You:</strong> ' : `<strong>${userName}:</strong> `;
            content += message.body ? message.body : '';
            if (message.attachment) {
                const attachment = JSON.parse(message.attachment);
                const url = `/storage/chat_attachments/${attachment.new_name}`;
                content += `<br><a href="${url}" target="_blank">Attachment: ${attachment.old_name}</a>`;
            }
            el.innerHTML = content;
            chatBox.appendChild(el);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        document.querySelectorAll('.user-item').forEach(item => {
            item.addEventListener('click', function() {
                loadChat(this.dataset.userId, this.innerText);
            });
        });

        document.getElementById('message-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            axios.post('/chat/send', formData)
                .then(() => form.reset())
                .catch(err => console.error(err));
        });
    });
</script>
@endpush