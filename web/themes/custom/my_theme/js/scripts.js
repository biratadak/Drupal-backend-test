(function ($, Drupal, once) {
  Drupal.behaviors.myModuleBehavior = {
    attach: function (context, settings) {
      $('.read-more-btn').click(function () {
        $this.preventDefault();
        alert("hello");
      });
    }
  };
})(jQuery, Drupal, once);
