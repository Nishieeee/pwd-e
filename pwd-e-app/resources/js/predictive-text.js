import { suggest, currentWordInfo, addHistory, getContext } from './predictive-text-core.js';

if (typeof document !== 'undefined') {
    let dropdown = null;
    let activeInput = null;
    let activeIndex = -1;
    let currentSuggestions = [];

    function ensureDropdown() {
        if (dropdown) return dropdown;
        dropdown = document.createElement('div');
        dropdown.id = 'predictive-dropdown';
        dropdown.setAttribute('role', 'listbox');
        dropdown.setAttribute('aria-label', 'Word suggestions');
        dropdown.style.cssText = 'position:absolute;z-index:9999;background:#fff;border:1px solid #B8D0E2;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,.12);padding:4px;min-width:120px;display:none;';
        document.body.appendChild(dropdown);

        const live = document.createElement('div');
        live.id = 'predictive-live';
        live.setAttribute('aria-live', 'polite');
        live.setAttribute('aria-atomic', 'true');
        live.style.cssText = 'position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden;';
        document.body.appendChild(live);
        return dropdown;
    }

    function positionDropdown(input) {
        const rect = input.getBoundingClientRect();
        dropdown.style.left = (window.scrollX + rect.left) + 'px';
        dropdown.style.top = (window.scrollY + rect.bottom + 4) + 'px';
        dropdown.style.minWidth = Math.max(rect.width, 120) + 'px';
    }

    function renderSuggestions(input, suggestions) {
        ensureDropdown();
        dropdown.innerHTML = '';
        currentSuggestions = suggestions;
        activeIndex = -1;
        if (!suggestions.length) { dropdown.style.display = 'none'; return; }
        suggestions.forEach((s, i) => {
            const item = document.createElement('div');
            item.setAttribute('role', 'option');
            item.setAttribute('id', 'pred-opt-' + i);
            item.setAttribute('aria-selected', 'false');
            item.textContent = s;
            item.style.cssText = 'padding:6px 10px;cursor:pointer;border-radius:6px;font-size:14px;';
            item.addEventListener('mousedown', (e) => { e.preventDefault(); accept(input, i); });
            item.addEventListener('mouseenter', () => setActive(i));
            dropdown.appendChild(item);
        });
        positionDropdown(input);
        dropdown.style.display = 'block';
        const live = document.getElementById('predictive-live');
        if (live) live.textContent = suggestions.length + ' suggestions available';
    }

    function setActive(i) {
        const items = dropdown.querySelectorAll('[role="option"]');
        items.forEach((el, idx) => {
            el.style.background = idx === i ? '#DCEAF4' : '';
            el.setAttribute('aria-selected', idx === i ? 'true' : 'false');
        });
        activeIndex = i;
        if (activeInput && i >= 0) {
            activeInput.setAttribute('aria-activedescendant', 'pred-opt-' + i);
            activeInput.setAttribute('aria-controls', 'predictive-dropdown');
        }
    }

    function accept(input, idx) {
        if (idx < 0 || idx >= currentSuggestions.length) idx = 0;
        const s = currentSuggestions[idx];
        if (!s) return;
        const info = currentWordInfo(input.value, input.selectionStart || input.value.length);
        const val = input.value;
        const newVal = val.slice(0, info.start) + s + val.slice(info.pos);
        input.value = newVal;
        const newPos = info.start + s.length;
        input.setSelectionRange(newPos, newPos);
        addHistory(s, getContext(input));
        hideDropdown();
        input.dispatchEvent(new Event('input', { bubbles: true }));
    }

    function hideDropdown() {
        if (dropdown) dropdown.style.display = 'none';
        currentSuggestions = [];
        activeIndex = -1;
        if (activeInput) activeInput.removeAttribute('aria-activedescendant');
    }

    function isTextInput(el) {
        if (!el) return false;
        if (el.tagName === 'TEXTAREA') return true;
        if (el.tagName === 'INPUT') {
            const t = (el.type || 'text').toLowerCase();
            return t === 'text' || t === 'search' || t === 'url' || t === 'email';
        }
        return false;
    }

    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    function addVoiceButton(input) {
        if (!SpeechRecognition) return;
        if (input._syncVoiceBtn) {
            input._syncVoiceBtn();
            return;
        }
        if (input.dataset.voiceBound) return;
        input.dataset.voiceBound = '1';
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.setAttribute('aria-label', 'Voice input');
        btn.title = 'Voice input';
        btn.textContent = '🎤';
        btn.style.cssText = 'position:absolute;right:8px;top:50%;transform:translateY(-50%);border:none;background:transparent;cursor:pointer;font-size:16px;padding:2px 4px;line-height:1;z-index:2;';
        const parent = input.parentElement;
        if (getComputedStyle(parent).position === 'static') parent.style.position = 'relative';
        const syncPos = () => {
            if (input.offsetHeight > 0) {
                btn.style.top = (input.offsetTop + input.offsetHeight / 2) + 'px';
            }
        };
        input._syncVoiceBtn = syncPos;
        parent.appendChild(btn);
        requestAnimationFrame(syncPos);
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const rec = new SpeechRecognition();
            rec.lang = 'en-PH';
            rec.interimResults = false;
            rec.onresult = (ev) => {
                const text = ev.results[0][0].transcript;
                input.value = (input.value ? input.value + ' ' : '') + text;
                input.dispatchEvent(new Event('input', { bubbles: true }));
            };
            rec.start();
            btn.textContent = '🔴';
            rec.onend = () => { btn.textContent = '🎤'; };
        });
    }

    document.addEventListener('focusin', (e) => {
        const el = e.target;
        if (!isTextInput(el)) return;
        if (document.documentElement.classList.contains('a11y-no-predictive')) return;
        activeInput = el;
        el.setAttribute('aria-autocomplete', 'list');
        el.setAttribute('aria-haspopup', 'listbox');
        addVoiceButton(el);
    });

    document.addEventListener('input', (e) => {
        const el = e.target;
        if (!isTextInput(el) || el !== activeInput) return;
        const info = currentWordInfo(el.value, el.selectionStart || el.value.length);
        const ctx = getContext(el);
        const s = suggest(info.word, ctx);
        renderSuggestions(el, s);
    });

    document.addEventListener('keydown', (e) => {
        if (!activeInput || !dropdown || dropdown.style.display === 'none') return;
        if (e.key === 'Tab') {
            if (currentSuggestions.length) {
                e.preventDefault();
                accept(activeInput, activeIndex >= 0 ? activeIndex : 0);
            }
        } else if (e.key === 'Escape') {
            hideDropdown();
        } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            const next = activeIndex < currentSuggestions.length - 1 ? activeIndex + 1 : 0;
            setActive(next);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            const prev = activeIndex > 0 ? activeIndex - 1 : currentSuggestions.length - 1;
            setActive(prev);
        } else if (e.key === 'Enter' && activeIndex >= 0) {
            e.preventDefault();
            accept(activeInput, activeIndex);
        }
    });

    document.addEventListener('focusout', (e) => {
        if (e.target === activeInput) {
            setTimeout(hideDropdown, 150);
        }
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', ensureDropdown);
    } else {
        ensureDropdown();
    }
}