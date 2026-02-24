import './bootstrap';
import Sortable from 'sortablejs';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

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
                order.push({
                    id: item.dataset.id,
                    order: index + 1
                });
            });

            Livewire.dispatch('reorderExercises', { order });
        }
    });
}
