import './bootstrap';
import './echo';
import Chart from 'chart.js/auto';
window.Chart = Chart;

import Sortable from 'sortablejs';

import Toastify from 'toastify-js';
import 'toastify-js/src/toastify.css';

const sortableInstances = {};

function setupSortable(id, eventName) {
    const el = document.getElementById(id);
    if (!el) return;

    if (sortableInstances[id]) {
        sortableInstances[id].destroy();
        delete sortableInstances[id];
    }

    sortableInstances[id] = new Sortable(el, {
        animation: 150,
        handle: '.drag-handle',
        ghostClass: 'opacity-30',
        onEnd: function () {
            const order = [];
            el.querySelectorAll('[data-id]').forEach((item, index) => {
                order.push({ id: parseInt(item.dataset.id), order: index + 1 });
            });
            Livewire.dispatch(eventName, { order });
        }
    });
}

function initSortable() {
    setupSortable('sortable-exercises', 'reorderExercises');
    setupSortable('sortable-routines', 'reorderRoutines');
}

document.addEventListener('livewire:init', () => {
    Livewire.hook('morph.updated', () => {
        initSortable();
    });

    initSortable();
});

// PWA service worker
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(() => {});
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
