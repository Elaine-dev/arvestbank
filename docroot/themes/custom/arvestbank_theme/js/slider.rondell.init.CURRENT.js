/**
 * @todo this could all be accomplished client-side and/or ajax
 */

(function($){
  {

    var carousel = 'init';

    // see http://projects.sebastianhelzle.net/jquery.rondell/examples/options.html
    var totalSlides = $('#rondellCarousel > div').length,
      allowMultipleFilters = false;

    $('#rondellCarousel > div').each(function(i, node) {
      var $node = $(node),
        $slideCount = $node.find('.slide-count'),
        src = null;
      i++;
      $slideCount.html( i + " OF " +  totalSlides );
      $node.prop('id', 'item' + i);
      if ( i ==  1 ) {
        src = $node.find('img').data('src');
        $node.find('img').prop('src', src).removeData('src');	// addClass('rondell-item-resizeable')
      } else {
        // rondell script seems to do this for #item1 on init... needs done to others
        $('#item'+i).find('.slide-caption').wrap('<div />').addClass('rondell-caption rondell-item-overlay');
      }
    });
    $('.cards-wrap').on('focus', 'input[type=text]', function(){
      console.log('focus', this);
      $('input[name="focused"]').val( $(this).prop('name') );
      if ( !allowMultipleFilters ) {
        console.warn('clear the others');
        $('.cards-wrap select[name=category]').
        selectbox('option', 'onChange', function(){}).	// need to specify an onChange trigger, or selectbox.js fires change
          selectbox('change', 'all').
        selectbox('option', 'onChange', null);
        if ( $(this).prop('name') === 'keyword' ) {
          $('.cards-wrap input[name=zipcode]').val('');
        } else {
          $('.cards-wrap input[name=keyword]').val('');
        }
      }
    });
    $('.cards-wrap select').on('change', function () {
      console.log('select changed');
      $('input[name="focused"]').val( $(this).prop('name') );
      this.form.submit();
    });
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

    var options = {
      alwaysShowCaption: true,
      center: {
        top: 170,
        //left: 300		// 340
        left: width / 2
      },
      controls: {
        enabled: true,
        fadeTime: 0,
        margin: {
          x: 0,
          y: 160
        }
      },
      currentLayer: 1,		// 0 will set to middle
      item_size_focus:{
        width: 300,
        height: 400
      },
      fadeTime: 100,
      keyDelay: 100,
      itemProperties: {
        delay: 100,
        cssClass: 'rondellItem',
        size: {
          width: 300,
          height: 185
        },
        sizeFocused: {
          width: 389,
          height: 244
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
        width:  width,		// 380
        height: 390
      },
      touch: {
        enabled: true,
        preventDefaults: true,
        threshold: 50
      },
      visibleItems: 1,
      funcOpacity: function(l, r, i) {
        /**
         * Lazy Load hack!
         */
          //console.log('l', l);
          //console.log('i', i);
        var o = 0,
          $img = null,
          src = null;
        if ( r.visibleItems > 1 ) {
          o = Math.max(0, 1.0 - Math.pow(l / r.visibleItems, 2));
        } else if ( r.visibleItems === 1 ) {
          o = 1;
        }
        if ( o > 0 ) {
          //console.log('node', );
          $img = $('#item'+i).find('img'),
            src = $img.data('src');
          //console.log('src',src);
          if ( src !== undefined ) {
            $img.prop('src', src).addClass('rondell-item-resizeable').removeData('src');
            $('#item'+i).removeClass('rondell-item-loading');
            //$('#item'+i).find('.slide-caption').wrap('<div />').addClass('rondell-caption rondell-item-overlay');
            //$('#item'+i).find('.slide-caption').addClass('rondell-caption rondell-item-overlay');

          }
        }
        return o;
      }
    };
    if ( totalSlides == 1 ) {
      options.controls.enabled = false;
    }
    console.log('width', width);
    /*
    if ( carousel.maxItems > 150 ) {
      options.radius.x = 550;
    } else if ( carousel.maxItems > 100 ) {
      options.radius.x = 400;
    } else if ( carousel.maxItems > 50 ) {
      options.radius.x = 250;
    } else if ( carousel.maxItems > 20 ) {
      options.radius.x = 210;
    } else if ( carousel.maxItems > 10 ) {
      options.radius.x = 175;
    } else {
      options.radius.x = 150;
    }
    */
    carousel = $('#rondellCarousel').rondell(options);


  }

})(jQuery);
