import './bootstrap';
import './echo';

import Sortable from 'sortablejs';

import Toastify from 'toastify-js';
import 'toastify-js/src/toastify.css';

document.addEventListener('livewire:init', () => {
    Livewire.hook('morph.updated', () => {
        initSortable();
    });

    initSortable();
});

function initSortable() {
    const el = document.getElementById('sortable-exercises');
    if (!el) return;
    new Sortable(el, {
        animation: 150,
        handle: '.drag-handle',
        onEnd: function (evt) {
            let order = [];
            el.querySelectorAll('[data-id]').forEach((item, index) => {
                order.push({ id: item.dataset.id, order: index + 1 });
            });
            Livewire.dispatch('reorderExercises', { order });
        }
    });
}

window.toast = function(message, type = 'success') {
    let bg = type === 'success'
        ? 'linear-gradient(to right, #10b981, #059669)'
        : 'linear-gradient(to right, #ef4444, #dc2626)';
    Toastify({
        text: message,
        duration: 3000,
        gravity: 'top',
        position: 'right',
        style: { background: bg, color: 'white', borderRadius: '14px', padding: '12px 18px' }
    }).showToast();
}
