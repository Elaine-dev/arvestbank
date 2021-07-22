/**
 * Sliders JavaScript
 * - 01 - Imports
 * - 02 - Functions
 * - 03 - Instances
 * - 04 - Glide
 */
/* ------------------------------------ *\
  01 - Imports
\* ------------------------------------ */
// This import depends on webpack to work.
// import Glide from '@glidejs/glide';
/* ------------------------------------ *\
  02 - Functions
  Useful functions for production usage.
\* ------------------------------------ */
function getCookie(name) {
  let returnedValue = null;
  // Split cookie string and get all individual name=value pairs in an array.
  const cookieArr = document.cookie.split(';');
  // Loop through the array elements.
  for (var i = 0; i < cookieArr.length; i++) {
    var e = cookieArr[i];
    const cookiePair = e.split('=');
    // Removing whitespace at the beginning of the cookie name
    // and compare it with the given string.
    const cookieName = cookiePair[0];
    const cookieValue = cookiePair[1];
    if (name === cookieName.trim()) {
      // Get the cookie value and return.
      returnedValue = cookieValue;
    }
  }

  // Return null if not found.
  return returnedValue;
}
/* ------------------------------------ *\
  03 - Instances
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
  04 - Glide
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
          const journeyStageCookie = getCookie('conga_journey_stage');

          // Instantiate and mount slider.
          const glide = new Glide(glideElement, sliders[slideClass].options);
          glide.mount();

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

          // Set active slide based on cookie.
          if (journeyStageCookie !== null) {
            sliderControls.forEach((control) => {
              if (journeyStageCookie === control.dataset.journeyStage) {
                const controlIndex = parseInt(control.dataset.glideDir.substr(1, 1), 10);
                glide.update({ startAt: controlIndex });
              }
            });
          }

        }


      }

    }
  },
};
