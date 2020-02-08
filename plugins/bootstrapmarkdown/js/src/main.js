;(function ($, window, document, undefined) {

  window.Editor = window.Editor || function (options) {
    this.options = {
      textarea: '.js-text-box'
    };

    if (options) {
      $.extend(this.options, options);
    }
  };

  Editor.prototype.attachEditor = function (textarea) {
    var $textarea = $(textarea);

    // If an editor is already attached, bail out
    if ($textarea.data('editor')) {
      return;
    }

    $textarea.markdown({
      iconlibrary: 'fa'
    , hiddenButtons: [
        'cmdPreview'
      ]
    , onShow: function (e) {
        // Initialize @mention autocompletion
        if (gdn.atCompleteInit) {
          gdn.atCompleteInit(e.$textarea);
        }
      }
    });

    // Remember that a textarea has been attached
    $textarea.data('editor', true);
  };

  Editor.prototype.attachEditorHandler = function () {
    var self = this;

    $(this.options.textarea).each(function () {
      self.attachEditor(this);
    });
  };

  // These events will trigger an editor attachment
  var attachTriggers = [
    'ready'
  , 'EditCommentFormLoaded'
  ];

  // Intialize the Editor plugin
  var editor = new Editor({
    textarea: '.BodyBox'
  });

  // Attach event handlers
  $(document)
    .on(attachTriggers.join(' '), editor.attachEditorHandler.bind(editor));

  // Noop jQuery Autogrow so things don't break
  $.fn.autogrow = $.noop;

})(jQuery, window, document);
