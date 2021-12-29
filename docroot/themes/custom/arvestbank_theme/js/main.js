(function ($, Drupal) {
  var regexp = /[®]/;
  $('body :not(script,sup)').contents().filter(function() {
    return this.nodeType === 3 && (regexp.test(this.nodeValue));
  }).replaceWith(function() {
    return this.nodeValue.replace("®", "<sup>&reg;</sup>");
  });

  // Inject the sidebar menu markup to the appropriate place based on screen
  // size.
  Drupal.behaviors.arvestbankThemeSidebarMenu = {
    // Property that is a jQuery that contains one copy of the sidebar nav elem.
    $sidebarMenuNav: null,
    injectSidebarMenuContent: function(context) {
      context = context || 'body';
      var behavior = this;
      $('.block-sidebar-menu-block', context).each(function (index, elem) {
        var $sidebarMenuBlock = $(elem);
        console.log($sidebarMenuBlock);
        // CSS `display: none` is set on ancestor of the sidebar block in both
        // locations. Based on which ancestor is visible, inject or remove
        // sidebar menu HTML.
        if ($sidebarMenuBlock.closest('.coh-style-sidebar-navigation').is(':visible') ||
            $sidebarMenuBlock.closest('.coh-style-sidebar-accordion-menu').is(':visible')) {
          if (!Number($sidebarMenuBlock.attr('data-has-nav')) &&
              behavior.$sidebarMenuNav) {
            // Only inject markup if not already there.
            $sidebarMenuBlock.attr('data-has-nav', '1');
            behavior.$sidebarMenuNav.appendTo($sidebarMenuBlock);
          }
        }
        else {
          if (Number($sidebarMenuBlock.attr('data-has-nav'))) {
            // Initialize the sidebar menu property by finding the copy in the
            // hidden region.
            if (!behavior.$sidebarMenuNav) {
              behavior.$sidebarMenuNav = $sidebarMenuBlock.find('nav#block-sidebar-menu-main-navigation');
            }
            // Remove the sidebar menu nav element from the DOM if it is still
            // within the hidden region. Need to do this check to prevent
            // removing the nav if it was already appended to the correct
            // region.
            if ($.contains($sidebarMenuBlock[0], behavior.$sidebarMenuNav[0])) {
              behavior.$sidebarMenuNav.detach();
            }
            $sidebarMenuBlock.html('').attr('data-has-nav', '0');
          }
        }
      });

    },
    attach: function (context, settings) {
      this.injectSidebarMenuContent(context);
    }
  };
  $(window).resize(function () {
    Drupal.behaviors.arvestbankThemeSidebarMenu.injectSidebarMenuContent();
  });
})(jQuery, Drupal);
