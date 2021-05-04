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

    document.querySelectorAll('[data-micromodal-close]').forEach((link) => {
      link.addEventListener('click', (ev) => function ( element ) {
        var iframe = element.querySelector( 'iframe');
        var video = element.querySelector( 'video' );
        if ( iframe !== null ) {
          var iframeSrc = iframe.src;
          iframe.src = iframeSrc;
        }
        if ( video !== null ) {
          video.pause();
        }
        window.alert('close');
      });
    });



  },
};
