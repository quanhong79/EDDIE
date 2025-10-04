{{-- resources/views/partials/ai_chat.blade.php --}}
@php $isLogged = auth()->check(); @endphp

<style>
  :root{
    --ai-primary:#6C5CE7; --ai-accent:#00D1FF; --ai-bg:#0f1221; --ai-fg:#e8eafd;
    --ai-muted:#9aa0c3; --ai-success:#22c55e;
  }
  .ai-fab{position:fixed;right:18px;bottom:18px;z-index:1050;width:64px;height:64px;border-radius:18px;
    background:linear-gradient(135deg,var(--ai-primary),var(--ai-accent));
    box-shadow:0 18px 45px rgba(79,70,229,.35), inset 0 1px 0 rgba(255,255,255,.25);
    display:flex;align-items:center;justify-content:center;cursor:pointer;border:none;outline:none;
    transition:transform .15s ease,filter .2s ease}
  .ai-fab:hover{transform:translateY(-2px);filter:saturate(1.1) brightness(1.05)}
  .ai-fab svg{width:28px;height:28px;color:#fff}

  .ai-panel{position:fixed;right:100px;bottom:100px;z-index:1050;display:none;width:380px;max-height:72vh;
    border-radius:20px;overflow:hidden;background:rgba(17,22,40,.45);
    -webkit-backdrop-filter:blur(16px) saturate(1.2);backdrop-filter:blur(16px) saturate(1.2);
    border:1px solid rgba(255,255,255,.08);box-shadow:0 24px 60px rgba(15,18,33,.45);color:var(--ai-fg)}
  .ai-header{display:flex;align-items:center;gap:10px;padding:14px 16px;border-bottom:1px solid rgba(255,255,255,.06);
    background:linear-gradient(180deg,rgba(255,255,255,.06),rgba(255,255,255,0))}
  .ai-avatar{width:36px;height:36px;border-radius:12px;display:grid;place-items:center;
    background:radial-gradient(100% 100% at 70% 0%,#a78bfa 0%,#2563eb 100%);box-shadow:inset 0 1px 0 rgba(255,255,255,.25)}
  .ai-title{font:700 14px/1.1 ui-sans-serif,system-ui,-apple-system}
  .ai-sub{font:400 12px/1 ui-sans-serif,system-ui,-apple-system;color:var(--ai-muted)}
  .ai-dot{width:8px;height:8px;border-radius:50%;background:var(--ai-success);box-shadow:0 0 0 4px rgba(34,197,94,.15);display:inline-block;margin-right:6px}
  .ai-actions{margin-left:auto;display:flex;gap:8px}
  .ai-iconbtn{width:36px;height:36px;border-radius:10px;border:1px solid rgba(255,255,255,.08);
    background:rgba(255,255,255,.06);color:var(--ai-fg);display:grid;place-items:center;cursor:pointer;
    transition:background .2s ease,transform .1s ease}
  .ai-iconbtn:hover{background:rgba(255,255,255,.12)}

  .ai-body{height:46vh;overflow:auto;padding:14px}
  .ai-msg{max-width:80%;margin:0 0 12px;padding:10px 12px;border-radius:14px;
    font:500 14px/1.35 ui-sans-serif,system-ui,-apple-system;background:rgba(255,255,255,.08);color:var(--ai-fg)}
  .ai-me{margin-left:auto;border-bottom-right-radius:6px;background:rgba(0,209,255,.12)}
  .ai-bot{margin-right:auto;border-bottom-left-radius:6px;background:rgba(167,139,250,.12)}
  .ai-time{margin-top:6px;font:400 11px/1 ui-sans-serif,system-ui,-apple-system;color:var(--ai-muted)}

  .ai-cards{display:grid;gap:10px;margin-top:6px}
  .ai-card{display:flex;gap:10px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);
    border-radius:12px;padding:8px;align-items:center}
  .ai-thumb{width:52px;height:52px;border-radius:10px;flex:none;background:#00000010;overflow:hidden;display:grid;place-items:center}
  .ai-thumb img{width:100%;height:100%;object-fit:cover}
  .ai-card .t{font-weight:600;font-size:13px;line-height:1.25}
  .ai-card .p{font-weight:700;font-size:13px;opacity:.9}
  .ai-card .actions{margin-left:auto;display:flex;gap:8px}
  .ai-card a{text-decoration:none;font-weight:700;font-size:12px;padding:6px 10px;border-radius:10px;
    background:linear-gradient(135deg,var(--ai-accent),var(--ai-primary));color:#fff}

  .ai-input{border-top:1px solid rgba(255,255,255,.08);padding:10px;display:flex;gap:10px;align-items:flex-end;
    background:linear-gradient(0deg,rgba(255,255,255,.05),rgba(255,255,255,0))}
  .ai-textarea{flex:1;min-height:44px;max-height:140px;resize:vertical;padding:10px 12px;border-radius:12px;
    border:1px solid rgba(255,255,255,.10);background:rgba(255,255,255,.06);color:var(--ai-fg)}
  .ai-send{min-width:44px;height:44px;border-radius:12px;border:none;cursor:pointer;
    background:linear-gradient(135deg,var(--ai-accent),var(--ai-primary));box-shadow:0 12px 30px rgba(79,70,229,.35);
    color:#fff;font-weight:700;transition:transform .1s ease,filter .2s ease,opacity .2s ease}
  .ai-send[disabled]{opacity:.6;cursor:not-allowed}

  .ai-typing{display:inline-flex;gap:4px;align-items:center}
  .ai-typing .dot{width:6px;height:6px;border-radius:50%;background:rgba(255,255,255,.7);animation:ai-b 1s infinite ease-in-out}
  .ai-typing .dot:nth-child(2){animation-delay:.15s}.ai-typing .dot:nth-child(3){animation-delay:.3s}
  @keyframes ai-b{0%,80%,100%{transform:translateY(0);opacity:.6}40%{transform:translateY(-4px);opacity:1}}

  @media (prefers-color-scheme: light){.ai-panel{background:rgba(255,255,255,.85);color:#1b1f3a}.ai-msg{color:#1b1f3a}}
  @media (max-width:480px){.ai-panel{right:14px;left:14px;bottom:90px;width:auto}}
</style>

<button id="ai-fab" class="ai-fab" type="button" aria-label="Mở Trợ lý AI">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
    <path d="M7 17l-3.5 3.5a.7.7 0 01-1.2-.5V7a4 4 0 014-4h8a4 4 0 014 4v7a4 4 0 01-4 4H7z"/>
    <path d="M8 7h8M8 11h6" stroke-linecap="round"/>
  </svg>
</button>

<div id="ai-panel" class="ai-panel" role="dialog" aria-modal="true" aria-labelledby="ai-title">
  <div class="ai-header">
    <div class="ai-avatar" aria-hidden="true">
      <svg viewBox="0 0 24 24" width="18" height="18" fill="white" opacity=".95">
        <circle cx="12" cy="9" r="4"></circle><path d="M4 20a8 8 0 0116 0" fill="white" opacity=".75"></path>
      </svg>
    </div>
    <div>
      <div id="ai-title" class="ai-title">Trợ lý AI</div>
      <div class="ai-sub"><span class="ai-dot"></span>Đang sẵn sàng hỗ trợ</div>
    </div>
    <div class="ai-actions">
      <button class="ai-iconbtn" id="ai-popout" title="Mở trang chat">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M14 3h7v7"/><path d="M21 3l-7 7"/><path d="M16 21H5a2 2 0 0 1-2-2V8"/>
        </svg>
      </button>
      <button class="ai-iconbtn" id="ai-close" title="Đóng">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M18 6L6 18M6 6l12 12"/>
        </svg>
      </button>
    </div>
  </div>

  <div id="ai-body" class="ai-body" aria-live="polite" aria-busy="false"></div>

  @if($isLogged)
  <div class="ai-input">
    <textarea id="ai-text" class="ai-textarea" placeholder="Nhập câu hỏi của bạn... (Shift+Enter để xuống dòng)"></textarea>
    <button id="ai-send" class="ai-send" type="button">Gửi</button>
  </div>
  @else
  <div class="ai-input">
    <button class="ai-send" style="flex:1" onclick="window.location='{{ route('login') }}?next={{ urlencode(route('chat.index')) }}'">Đăng nhập để chat</button>
  </div>
  @endif
</div>

<script>
(function(){
  const isLogged = {{ $isLogged ? 'true':'false' }};
  const routes = { stream: "{{ $isLogged ? route('chat.stream') : '' }}", index: "{{ route('chat.index') }}" };
  const csrf = (document.querySelector('meta[name="csrf-token"]')||{}).content || '';

  const fab = document.getElementById('ai-fab');
  const panel = document.getElementById('ai-panel');
  const closeB = document.getElementById('ai-close');
  const popout = document.getElementById('ai-popout');
  const bodyEl = document.getElementById('ai-body');
  const sendBt = document.getElementById('ai-send');
  const input = document.getElementById('ai-text');

  let streaming = false;

  function escapeHTML(s){
    return String(s||'').replace(/[&<>"']/g, m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
  }

  function showPanel(v){
    panel.style.display = v ? 'block':'none';
    if(v && isLogged && bodyEl.childElementCount===0){
      botSay('Xin chào 👋 Mình là trợ lý AI của Shop. Bạn cần hỗ trợ gì?');
    }
  }
  function bubble(text, mine=false){
    const wrap = document.createElement('div');
    wrap.className = 'ai-msg ' + (mine ? 'ai-me':'ai-bot');
    wrap.innerHTML = escapeHTML(text) + `<div class="ai-time">${new Date().toLocaleTimeString()}</div>`;
    bodyEl.appendChild(wrap); bodyEl.scrollTop = bodyEl.scrollHeight; return wrap;
  }
  function botSay(text){ return bubble(text, false); }

  function setTyping(v){
    const id = 'ai-typing';
    if(v){
      const t = document.createElement('div');
      t.className = 'ai-msg ai-bot'; t.id = id;
      t.innerHTML = `<span class="ai-typing"><span class="dot"></span><span class="dot"></span><span class="dot"></span></span>`;
      bodyEl.appendChild(t);
    } else { const el = document.getElementById(id); if(el) el.remove(); }
    bodyEl.scrollTop = bodyEl.scrollHeight;
  }

  // Parse bullet lines → thẻ sản phẩm
  function parseProductLines(text){
    const lines = text.split(/\n+/).map(s=>s.trim()).filter(Boolean);
    const items = [];
    for(const ln of lines){
      if(!ln.startsWith('•')) continue;
      const parts = ln.replace(/^•\s*/, '').split(/\s+—\s+| - /);
      if(parts.length>=2){
        items.push({ name: parts[0], price: parts[1] || '', url: parts[2] || '' });
      }
    }
    return items;
  }
  function renderProductCards(container, items){
    if(!items.length) return;
    const wrap = document.createElement('div'); wrap.className = 'ai-cards';
    for(const it of items){
      const card = document.createElement('div'); card.className = 'ai-card';
      const thumb = (it.url && it.url.match(/\.(jpg|jpeg|png|webp|gif)(\?.*)?$/i)) ? it.url : '';
      card.innerHTML = `
        <div class="ai-thumb">${thumb ? `<img src="${escapeHTML(thumb)}" alt="">` : ''}</div>
        <div class="info"><div class="t">${escapeHTML(it.name)}</div><div class="p">${escapeHTML(it.price)}</div></div>
        <div class="actions">${it.url ? `<a href="${escapeHTML(it.url)}">Xem</a>` : ''}</div>`;
      wrap.appendChild(card);
    }
    container.appendChild(wrap);
  }
  function applyRichRendering(msgDiv, text){
    const items = parseProductLines(text);
    if(items.length){
      msgDiv.innerHTML = `<div>Gợi ý theo cửa hàng:</div><div class="ai-time">${new Date().toLocaleTimeString()}</div>`;
      renderProductCards(msgDiv, items);
    } else {
      msgDiv.innerHTML = escapeHTML(text) + `<div class="ai-time">${new Date().toLocaleTimeString()}</div>`;
    }
  }

  async function send(){
    if(!isLogged){ window.location = "{{ route('login') }}?next={{ urlencode(route('chat.index')) }}"; return; }
    if(streaming) return;

    const text = (input.value||'').trim(); if(!text) return;
    bubble(text, true); input.value='';

    const botDiv = bubble('', false);
    streaming = true; if (sendBt) sendBt.disabled = true; setTyping(true);

    const ac = new AbortController(); const to = setTimeout(()=>ac.abort('timeout'), 60000);

    try{
      const res = await fetch(routes.stream, {
        method:'POST',
        headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrf },
        credentials:'same-origin',
        body: JSON.stringify({ message: text }),
        signal: ac.signal
      });

      if(!res.ok){
        const errTxt = await res.text().catch(()=> '');
        botDiv.innerHTML = `⚠️ Lỗi máy chủ: ${res.status}${errTxt ? ' — '+escapeHTML(errTxt):''}<div class="ai-time">${new Date().toLocaleTimeString()}</div>`;
        return;
      }

      const reader = res.body.getReader();
      const decoder = new TextDecoder();
      let full = '';
      let buffer = '';

      while(true){
        const { value, done } = await reader.read();
        if(done) break;

        buffer += decoder.decode(value, {stream:true});
        const blocks = buffer.split('\n\n');
        buffer = blocks.pop() || '';

        for(const block of blocks){
          const lines = block.split(/\r?\n/);
          const evtLine = lines.find(l=>l.startsWith('event: '));
          const dataLine = lines.find(l=>l.startsWith('data: '));
          const evt = evtLine ? evtLine.slice(7).trim() : '';

          if (evt === 'ping') continue;
          if (!dataLine) continue;

          const raw = dataLine.slice(6).trim();
          if (raw === '[DONE]') continue;
          if (evt === 'error'){
            try{
              const er = JSON.parse(raw);
              botDiv.innerHTML = `⚠️ ${escapeHTML(er.error || ('HTTP ' + (er.http||'')))}<div class="ai-time">${new Date().toLocaleTimeString()}</div>`;
            }catch{ botDiv.innerHTML = `⚠️ Có lỗi xảy ra.<div class="ai-time">${new Date().toLocaleTimeString()}</div>`; }
            continue;
          }

          // Hỗ trợ nhiều kiểu payload (Gemini / OpenAI)
          try{
            const p = JSON.parse(raw);

            // Gemini: candidates[].content.parts[].text (có thể stream từng mẩu)
            if (Array.isArray(p.candidates)) {
              for (const c of p.candidates) {
                const parts = (c.content && Array.isArray(c.content.parts)) ? c.content.parts : [];
                for (const part of parts) {
                  if (typeof part.text === 'string') full += part.text;
                }
              }
            }
            // OpenAI Responses (kiểu mới)
            else if (p.type) {
              if (p.type === 'response.output_text.delta') {
                const piece = (p.delta ?? p.text ?? '');
                full += (typeof piece === 'string' ? piece : '');
              } else if (p.type === 'response.output_text') {
                full = p.output_text || p.text || full;
              }
            }
            // OpenAI Responses (kiểu cũ)
            else if (typeof p.output_text === 'string') {
              full = p.output_text;
            } else if (p.delta && (p.delta.text || typeof p.delta === 'string')) {
              full += (p.delta.text || p.delta);
            } else if (Array.isArray(p.output)) {
              for (const item of p.output) {
                if (item?.content) {
                  for (const part of item.content) {
                    if (typeof part?.text === 'string') full += part.text;
                  }
                }
              }
            } else {
              full += raw; // fallback thô
            }
          }catch{ full += raw; }

          applyRichRendering(botDiv, full);
          bodyEl.scrollTop = bodyEl.scrollHeight;
        }
      }
    }catch(e){
      const msg = (e && e.name === 'AbortError') ? 'Kết nối quá hạn (timeout).' : 'Không kết nối được tới máy chủ.';
      botSay('⚠️ ' + msg); console.debug('AI stream error:', e);
    }finally{
      clearTimeout(to); streaming = false; if (sendBt) sendBt.disabled = false; setTyping(false);
    }
  }

  fab?.addEventListener('click', ()=> showPanel(panel.style.display!=='block'));
  closeB?.addEventListener('click', ()=> showPanel(false));
  popout?.addEventListener('click', ()=> window.location = routes.index);
  sendBt?.addEventListener('click', send);
  input?.addEventListener('keydown', e=>{ if(e.key==='Enter' && !e.shiftKey){ e.preventDefault(); send(); } });
})();
</script>
