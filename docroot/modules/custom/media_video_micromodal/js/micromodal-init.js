Drupal.behaviors.clnMicroModal = {
  attach(context) {

    const mm = context.classList && context.classList.contains('modal')
      ? [context] : context.querySelectorAll('.micromodal');
    if (mm.length === 0) { return; }

    MicroModal.init();

    document.querySelectorAll('[data-micromodal-trigger]').forEach((link) => {
      link.addEventListener('click', (ev) => {
        ev.preventDefault();
      });
    });
  },
};
