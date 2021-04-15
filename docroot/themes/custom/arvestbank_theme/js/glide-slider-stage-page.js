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
import Glide from '@glidejs/glide';
/* ------------------------------------ *\
  02 - Functions
  Useful functions for production usage.
\* ------------------------------------ */
function getCookie(name) {
  let returnedValue = null;
  // Split cookie string and get all individual name=value pairs in an array.
  const cookieArr = document.cookie.split(';');
  // Loop through the array elements.
  cookieArr.forEach((e) => {
    const cookiePair = e.split('=');
    // Removing whitespace at the beginning of the cookie name
    // and compare it with the given string.
    const [cookieName, cookieValue] = cookiePair;
    if (name === cookieName.trim()) {
      // Get the cookie value and return.
      returnedValue = cookieValue;
    }
  });
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
  'block--quote-carousel': {
    options: {
      type: 'slider',
      rewind: false,
      perView: 1,
    },
  },
  'paragraph--type--quote-carousel': {
    options: {
      type: 'slider',
      rewind: false,
      perView: 1,
    },
  },
  'block--card-slider': {
    options: {
      breakpoints: {
        640: {
          perView: 1,
        },
        896: {
          perView: 2,
        },
      },
      gap: 32,
      perView: 3,
      rewind: false,
      type: 'slider',
    },
  },
  'block--feature-carousel': {
    options: {
      gap: 32,
      perView: 1,
      rewind: false,
      type: 'slider',
    },
  },
  'block--story-builder': {
    options: {
      gap: 200,
      perView: 1,
      type: 'slider',
    },
  },
  'paragraph--type--customer-story-explorer': {
    options: {
      bound: true,
      breakpoints: {
        600: {
          peek: {
            before: 0,
            after: 24,
          },
          perView: 1,
        },
        800: {
          peek: {
            before: 0,
            after: 24,
          },
          perView: 2,
        },
      },
      gap: 32,
      peek: 0,
      perView: 3,
      rewind: false,
      type: 'slider',
    },
  },
  'block--customer-story-explorer': {
    options: {
      bound: true,
      breakpoints: {
        600: {
          peek: {
            before: 0,
            after: 24,
          },
          perView: 1,
        },
        800: {
          peek: {
            before: 0,
            after: 24,
          },
          perView: 2,
        },
      },
      gap: 32,
      peek: 0,
      perView: 3,
      rewind: false,
      type: 'slider',
    },
  },
  'paragraph--type--resource-explorer': {
    options: {
      bound: true,
      breakpoints: {
        600: {
          peek: {
            before: 0,
            after: 24,
          },
          perView: 1,
        },
        800: {
          peek: {
            before: 0,
            after: 24,
          },
          perView: 2,
        },
      },
      gap: 32,
      peek: 0,
      perView: 3,
      rewind: false,
      type: 'slider',
    },
  },
  'block--resource-explorer': {
    options: {
      bound: true,
      breakpoints: {
        600: {
          peek: {
            before: 0,
            after: 24,
          },
          perView: 1,
        },
        800: {
          peek: {
            before: 0,
            after: 24,
          },
          perView: 2,
        },
      },
      gap: 32,
      peek: 0,
      perView: 3,
      rewind: false,
      type: 'slider',
    },
  },
  'block--webinar-explorer': {
    options: {
      bound: true,
      breakpoints: {
        600: {
          peek: {
            before: 0,
            after: 24,
          },
          perView: 1,
        },
        800: {
          peek: {
            before: 0,
            after: 24,
          },
          perView: 2,
        },
      },
      gap: 32,
      peek: 0,
      perView: 3,
      rewind: false,
      type: 'slider',
    },
  },
  'paragraph--type--webinar-explorer': {
    options: {
      bound: true,
      breakpoints: {
        600: {
          peek: {
            before: 0,
            after: 24,
          },
          perView: 1,
        },
        800: {
          peek: {
            before: 0,
            after: 24,
          },
          perView: 2,
        },
      },
      gap: 32,
      peek: 0,
      perView: 3,
      rewind: false,
      type: 'slider',
    },
  },
  'view--conga-upcoming-webinars': {
    options: {
      bound: true,
      breakpoints: {
        600: {
          peek: {
            before: 0,
            after: 24,
          },
          perView: 1,
        },
        800: {
          peek: {
            before: 0,
            after: 24,
          },
          perView: 2,
        },
      },
      gap: 32,
      peek: 0,
      perView: 3,
      rewind: false,
      type: 'slider',
    },
  },
};
/* ------------------------------------ *\
  04 - Glide
  Do not alter the code below, as it
  mounts each individual slider unit
  set by the object above. Define
  necessary classes and options there.
\* ------------------------------------ */
Drupal.behaviors.congaSliders = {
  attach(context) {
    if (typeof sliders !== 'undefined') {
      Object.keys(sliders).forEach((component) => {
        // Find Elements and stop program if non-existent
        const sliderWrapper = context.classList && context.classList.contains(component)
          ? [context] : context.querySelectorAll(`.${component}`);
        if (sliderWrapper.length === 0) { return; }
        // Run Program on each element
        sliderWrapper.forEach((element) => {
          const glideElement = element.querySelector('.glide');
          if (!glideElement) { return; }
          const glideSlides = glideElement.querySelectorAll('.glide__slide');
          const glideArrows = glideElement.querySelector('.glide__arrows');
          const sliderControlLeft = glideElement.querySelector('.glide__arrow--left');
          const sliderControlRight = glideElement.querySelector('.glide__arrow--right');
          const sliderControls = glideElement.querySelectorAll('.glide__control');
          const journeyStageCookie = getCookie('conga_journey_stage');
          const glide = new Glide(glideElement, sliders[component].options);
          glide.mount();
          // Left arrow initially disabled and Adjust disabled arrows depending on index
          if (sliders[component].options.rewind === false && glideArrows !== null) {
            sliderControlLeft.setAttribute('disabled', '');
            glide.on('run.after', () => {
              if (glide.index > 0) {
                sliderControlLeft.removeAttribute('disabled');
              } else if (glide.index === 0) {
                sliderControlLeft.setAttribute('disabled', '');
              }
              if (glide.index === (glideSlides.length - 1)) {
                sliderControlRight.setAttribute('disabled', '');
              } else {
                sliderControlRight.removeAttribute('disabled');
              }
            });
          }
          // Only one card per view on smaller column layouts
          if (!element.parentElement.parentElement.parentElement.classList.contains('onecol-layout')
            && component === 'block--card-slider') {
            glide.update({ perView: 1 });
          }
          // Set active slide based on cookie
          if (journeyStageCookie !== null) {
            sliderControls.forEach((control) => {
              if (journeyStageCookie === control.dataset.journeyStage) {
                const controlIndex = parseInt(control.dataset.glideDir.substr(1, 1), 10);
                glide.update({ startAt: controlIndex });
              }
            });
          }
        });
      });
    }
  },
};
