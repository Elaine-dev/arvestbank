/**
 * Sliders JavaScript
 * - 01 - Instances
 * - 02 - Glide
 */
/* ------------------------------------ *\
  01 - Instances
  Add any instances of sliders to this
  object using the class of the block
  wrapper, followed by the options
  as defined by Glide.js
\* ------------------------------------ */
const sliders = {
  'card-slider': {
    selector: '.glide__slide',
    options: {
      type: 'slider',
      startAt: Math.floor(Math.random() * 4),
      gap: 0,
      perView: 1,
      keyboard: false,
      animationDuration: 1000,
      focusAt: 'center',
    },
  },
};
/* ------------------------------------ *\
  02 - Glide
  Mounts slider.
\* ------------------------------------ */
Drupal.behaviors.arvestSliders = {
  attach: function (context, settings) {
    if (typeof sliders !== 'undefined') {

      // Loop over slides.
      for (var i = 0; i < Object.keys(sliders).length; i++) {

        // Get slide class.
        var slideClass = Object.keys(sliders)[i];

        // Get slide wrapper object.
        const sliderWrapper = context.getElementsByClassName(slideClass);

        // Return if no slider wrapper.
        if (typeof sliderWrapper == 'undefined' || sliderWrapper.length === 0) {
          return;
        }

        // Loop over slides.
        for (var i = 0; i < sliderWrapper.length; i++) {

          // Get slide object.
          var slide = sliderWrapper[i];
          // Get glide element for slide.
          const glideElement = slide.querySelector('.glide');
          // Return if slide doesn't contain a glide element.
          if (!glideElement) {
            return;
          }

          // Get DOM objects for later use.
          const glideSlides = glideElement.querySelectorAll('.glide__slide');
          const glideArrows = glideElement.querySelector('.glide__arrows');
          const sliderControlLeft = glideElement.querySelector('.glide__arrow--left');
          const sliderControlRight = glideElement.querySelector('.glide__arrow--right');
          const sliderControls = glideElement.querySelectorAll('.glide__control');

          // Instantiate and mount slider.
          const glide = new Glide(glideElement, sliders[slideClass].options);

          // Set heights before build
          glide.on('build.before', function () {
            glideHandleHeight();
          });
          // Adjust heights on browser resize
          glide.on('resize', function () {
            glideHandleHeight();
          });

          glide.mount();

          // Resize slide heights based on text objects
          function glideHandleHeight() {
            for (var j = 0; j < glideSlides.length; j++) {
              const sliderBlock = glideSlides[j].querySelector('.left-block-padding'); // Get text object
              const blockTop = sliderBlock.offsetTop; // Get top offset
              const blockHeight = sliderBlock.offsetHeight; // Get current height

              // If width < 950px set specific slide heights
              if (slide.offsetWidth < 950) {
                glideSlides[j].style.height = blockTop + blockHeight + 10 + 'px'; // Set new height + extra padding height with px
              } else {
                glideSlides[j].style.height = '100%';
              }
            }
          }

          // If slider can't go back and we have arrows.
          if (sliders[slideClass].options.rewind === false && glideArrows !== null) {

            // Disable back arrow.
            sliderControlLeft.setAttribute('disabled', '');

            // Attach "run.after" event to slider.
            glide.on('run.after', function () {

              // If this is not the first slide.
              if (glide.index > 0) {
                // Enable back arrow.
                sliderControlLeft.removeAttribute('disabled');
              }
              // If this is the first slide.
              else if (glide.index === 0) {
                // Disable the back arrow.
                sliderControlLeft.setAttribute('disabled', '');
              }
              // If this is the last slide.
              if (glide.index === (glideSlides.length - 1)) {
                // Disable the forward arrow.
                sliderControlRight.setAttribute('disabled', '');
              }
              // If this is not the last slide.
              else {
                // Enable the right arrow.
                sliderControlRight.removeAttribute('disabled');
              }
            });

          }

          // If this is a card slider block in a one collumn layout.
          if (
            !slide.parentElement.parentElement.parentElement.classList.contains('onecol-layout')
            && slideClass === 'block--card-slider'
          ) {
            // Show one slide.
            glide.update({ perView: 1 });
          }

        }

      }

    }
  },
};
