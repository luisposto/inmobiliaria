document.querySelectorAll('input[type="number"][name^="precio_"]').forEach((input) => {
    const wrapper = document.createElement('div');
    wrapper.className = 'price-control';
    input.before(wrapper);
    wrapper.append(input);

    const actions = document.createElement('div');
    actions.className = 'price-control-actions';
    wrapper.append(actions);

    [1, -1].forEach((direction) => {
        const button = document.createElement('button');
        button.type = 'button';
        const field = input.name === 'precio_min' ? 'precio mínimo' : 'precio máximo';
        button.setAttribute('aria-label', `${direction > 0 ? 'Aumentar' : 'Disminuir'} ${field}`);
        button.innerHTML = `<svg viewBox="0 0 16 16" aria-hidden="true"><path d="${direction > 0 ? 'M4 10l4-4 4 4' : 'M4 6l4 4 4-4'}"/></svg>`;
        button.addEventListener('click', () => {
            if (direction > 0) input.stepUp();
            else input.stepDown();
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
        });
        actions.append(button);
    });
});
