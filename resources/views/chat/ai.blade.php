{{-- resources/views/chat/index.blade.php --}}
@extends('layouts.app')

@section('title','Trợ lý AI')

@section('content')
<div class="container py-4">
  <div class="row">
    <div class="col-lg-3">
      <h5 class="mb-3">Hội thoại</h5>
      <ul class="list-group">
        @foreach($chats as $c)
          <li class="list-group-item {{ $c->id === $active->id ? 'active' : '' }}">
            <a href="{{ route('chat.index') }}?id={{ $c->id }}" class="{{ $c->id === $active->id ? 'text-white' : '' }}">
              {{ $c->title ?? ('Chat #'.$c->id) }}
            </a>
          </li>
        @endforeach
      </ul>
    </div>

    <div class="col-lg-9">
      <div id="messages" class="border rounded p-3 mb-3" style="height: 60vh; overflow:auto; white-space:pre-wrap;"></div>

      <form id="composer" class="d-flex gap-2">
        @csrf
        <input type="hidden" id="chatId" value="{{ $active->id }}">
        <input class="form-control" id="prompt" placeholder="Nhập câu hỏi của bạn..." required />
        <button class="btn btn-primary">Gửi</button>
      </form>
    </div>
  </div>
</div>

<script>
const chatId = document.getElementById('chatId').value;
const box = document.getElementById('messages');
const form = document.getElementById('composer');
const input = document.getElementById('prompt');

async function loadHistory(){
  const res = await fetch(`{{ url('/chat') }}/${chatId}/messages`);
  const data = await res.json();
  box.innerHTML = '';
  for(const m of data){
    append(m.role, m.content);
  }
  box.scrollTop = box.scrollHeight;
}
function append(role, text){
  const who = role === 'assistant' ? '🤖' : role === 'user' ? '🧑' : '⚙️';
  const div = document.createElement('div');
  div.className = 'mb-2';
  div.innerHTML = `<strong>${who} ${role}:</strong> ${escapeHtml(text)}`;
  box.appendChild(div);
}
function escapeHtml(s){return s.replace(/[&<>"']/g, m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;' }[m]))}

form.addEventListener('submit', async (e)=>{
  e.preventDefault();
  const content = input.value.trim();
  if(!content) return;

  // 1) Lưu tin nhắn user
  await fetch(`{{ url('/chat') }}/${chatId}/message`,{
    method:'POST',
    headers:{'Content-Type':'application/json','X-CSRF-TOKEN': '{{ csrf_token() }}'},
    body: JSON.stringify({ content })
  });
  append('user', content);
  input.value = '';

  // 2) Stream phản hồi assistant
  const res = await fetch(`{{ url('/chat') }}/${chatId}/stream`, {
    method: 'POST',
    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
  });

  const reader = res.body.getReader();
  const decoder = new TextDecoder();
  let assistantDiv = document.createElement('div');
  assistantDiv.className = 'mb-2';
  assistantDiv.innerHTML = `<strong>🤖 assistant:</strong> `;
  box.appendChild(assistantDiv);

  while (true) {
    const {value, done} = await reader.read();
    if (done) break;
    const chunk = decoder.decode(value, {stream:true});

    // SSE lines: tách theo \n\n
    const events = chunk.split('\n\n');
    for (const ev of events) {
      if (!ev.trim()) continue;
      // Bạn có thể parse JSON sau 'data: '
      const line = ev.split('\n').find(l=>l.startsWith('data: '));
      if (!line) continue;
      if (line.trim() === 'data: [DONE]') { continue; }

      try {
        const payload = JSON.parse(line.substring(6));
        // Responses API có trường output_text tiện lợi; tùy bản trả về
        if (payload.output_text) {
          assistantDiv.innerHTML = `<strong>🤖 assistant:</strong> ${escapeHtml(payload.output_text)}`;
        } else if (payload.delta && payload.delta?.type === 'output_text.delta') {
          assistantDiv.innerHTML += escapeHtml(payload.delta?.text || '');
        }
      } catch(e) {
        // fallback: ghép raw
        assistantDiv.innerHTML += escapeHtml(line.replace(/^data:\s*/,''));
      }
      box.scrollTop = box.scrollHeight;
    }
  }
});

loadHistory();
</script>
@endsection
