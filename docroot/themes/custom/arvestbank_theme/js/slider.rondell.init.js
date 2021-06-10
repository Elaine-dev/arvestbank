(function ($, Drupal) {

  Drupal.behaviors.rondellSlider = {

    attach: function (context) {

      $(document).once('weberAjaxViews').ajaxComplete(function (event, xhr, settings) {

        var totalSlides = $('#rondellCarousel > div').length,
          allowMultipleFilters = false;

        $('#rondellCarousel').removeClass('hidden');
        var width = $('#rondellCarousel').width();

        /**
         * radius value seems to need to be different depending on number of slides
         */
        var radius = ( width / 2 ) - 100;	// 209
        if ( totalSlides > 100 ) {
          radius = 300;
        }
        if ( totalSlides < 25 ) {
          radius = 150;
        }
        if ( totalSlides < 13 ) {
          radius = 100;
        }

        // Set default sizes for desktop.
        let position_top = 170;
        let size_height = 390;
        let item_size_focus_width = 300;
        let item_size_focus_height = 400;
        let item_prop_size_width = 300;
        let item_prop_size_height = 185;
        let item_prop_size_focused_width = 389;
        let item_prop_size_focused_height = 244;
        let visible_items = 1;

        // Update dimensions with ratios for smaller screens.
        if (width < 400) {
          let mobile_modifier = .65; // Looks good until below ~420px screen devices
          if (width < 276) {
            mobile_modifier = .4;
            size_height = 1.15 * size_height; // Allow more space below slides for captions
          }
          // Scale all of these back by the modifier.
          radius = radius * mobile_modifier;
          position_top = position_top * mobile_modifier;
          size_height = size_height * mobile_modifier;
          item_size_focus_width = item_size_focus_width * mobile_modifier;
          item_size_focus_height = item_size_focus_height * mobile_modifier;
          item_prop_size_width = item_prop_size_width * mobile_modifier;
          item_prop_size_height = item_prop_size_height * mobile_modifier;
          item_prop_size_focused_width = item_prop_size_focused_width * mobile_modifier;
          item_prop_size_focused_height = item_prop_size_focused_height * mobile_modifier;
          // Static adjustments.
          visible_items = 0;
        }

        var options = {
          alwaysShowCaption: true,
          center: {
            top: position_top,
            left: width / 2
          },
          controls: {
            enabled: true,
            fadeTime: 0,
            margin: {
              x: 0,
              y: position_top - 10
            }
          },
          currentLayer: 1, // 0 will set to middle

          item_size_focus:{
            width: item_size_focus_width,
            height: item_size_focus_height
          },
          fadeTime: 100,
          keyDelay: 100,
          itemProperties: {
            delay: 100,
            cssClass: 'rondellItem',
            size: {
              width: item_prop_size_width,
              height: item_prop_size_height
            },
            sizeFocused: {
              width: item_prop_size_focused_width,
              height: item_prop_size_focused_height
            }
          },
          lightbox: {
            enabled: false
          },
          radius: {
            x: radius,
            y: 0
          },
          repeating: true,
          size: {
            width:  width,  // 380
            height: size_height
          },
          touch: {
            enabled: true,
            preventDefaults: true,
            threshold: 50
          },
          visibleItems: visible_items,
          funcOpacity: function (l, r, i) {
            /**
             * Lazy Load hack!
             */
            var o = 0,
              $img = null,
              src = null;
            if ( r.visibleItems > 1 ) {
              o = Math.max(0, 1.0 - Math.pow(l / r.visibleItems, 2));
            } else if ( r.visibleItems === 1 ) {
              o = 1;
            }
            if ( o > 0 ) {
              $img = $('#item' + i).find('img'),
                src = $img.data('src');
              if ( src !== undefined ) {
                $img.prop('src', src).addClass('rondell-item-resizeable').removeData('src');
                $('#item' + i).removeClass('rondell-item-loading');
              }
            }
            return o;
          }
        };
        if ( totalSlides == 1 ) {
          options.controls.enabled = false;
        }

        $('#rondellCarousel').rondell(options);

      });

    }

  }

}(jQuery, Drupal));
