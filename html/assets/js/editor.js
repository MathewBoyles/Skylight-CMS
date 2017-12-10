var editor = {
  id: "",
  title: "",
  functions: {},
  saved: {},
  init: function() {
    $(document).trigger("editor.init");

    $("a:not(.editor-link)").click(function(event) {
      alert("Links disabled in editor.");
      event.preventDefault();
    });

    $.getScript("/node_modules/summernote/dist/summernote-bs4.js", function() {
      $(document).trigger("editor.summernote");

      $("[data-editor]").each(function() {
        var editorData = editor.parse(this);

        if (editorData[0] == "value") {
          if (editorData[2] == "content") $(this).attr("contenteditable", "true");
          if (editorData[2] == "summernote") $(this).summernote();
        }
      });

      $(".note-editable").trigger("input");

      editor.saved = editor.data();

      setTimeout(function() {
        $(document).trigger("editor.ready");

        $(".note-editable").trigger("input");
        $("#editorSave").removeAttr("disabled", "disabled");
        editor.status("Editor ready", true);
      }, 1500);
    });

    $(window)
      .bind("keydown", function(event) {
        if (event.ctrlKey || event.metaKey) {
          switch (String.fromCharCode(event.which).toLowerCase()) {
            case "s":
              event.preventDefault();
              $("#editorSave").click();
              break;
          }
        }
      })
      .bind("beforeunload", function() {
        if (editor.changed()) {
          return "Close editor?\nAny unsaved changes will be lost.";
        }
      });

    editor.bind();
  },
  bind: function() {
    $("html").trigger("editor.bind");

    $("[data-editor]").each(function() {
      if ($(this).data("editorBind")) return;

      $(this).data("editorBind", "1").click(function() {
        var editorData = editor.parse(this);

        if (editorData[0] == "action") {
          if ($(this).attr("disabled")) return;

          if (editorData[2] == "close") {
            return editor.changed() ? confirm("Close editor?\nAny unsaved changes will be lost.") : true;
          }
          if (editorData[2] == "save") {
            $("#editorClose,#editorSave").attr("disabled", "disabled");
            $("#editorSave").text("Saving...");
            editor.status("Saving changes...");

            $.ajax({
              method: "POST",
              url: "/admin/ajax/savepage",
              data: {
                id: editor.id,
                data: editor.data(),
                csrf: editor.csrf
              },
              error: function() {
                $("#editorClose,#editorSave").removeAttr("disabled", "disabled");
                $("#editorSave").text("Save");

                alert("An error has occurred. Changes not saved.\nPlease try again.");

                editor.status("ERROR: Changes not saved!", true);
              },
              success: function(data) {
                $("#editorClose,#editorSave").removeAttr("disabled", "disabled");
                $("#editorSave").text("Save");

                if (data.status == "success") {
                  editor.saved = editor.data();
                  editor.status("Changes saved!", true);
                } else {
                  editor.status("ERROR: Changes not saved!", true);
                  alert(data.message);
                }
              }
            });
          }
          if (editorData[2] == "open") {
            var editorModal = editorData[1];

            $("[data-editor]").each(function() {
              var editorData = editor.parse(this);

              if (editorData[0] == "modal" && editorData[1] == editorModal) $(this).modal();
            });
          }

          if (editor.functions[editorData[2]]) editor.functions[editorData[2]].call(this);
        }
        if (editorData[0] == "value" && editorData[2] == "input") {
          var editorValue = prompt("", $(this).text());
          if (!editorValue) editorValue = $(this).text();

          $(this).text(editorValue);
        }
      });
    });
  },
  data: function(getData) {
    var editorSave = {};

    $("[data-editor]").each(function() {
      var editorData = editor.parse(this);

      if (editorData[0] == "value") {
        var editorValue = "";

        if (editorData[2] == "attr") editorValue = $(this).attr("data-value");
        if (editorData[2] == "content") editorValue = $(this).html();
        if (editorData[2] == "input") editorValue = $(this).text();
        if (editorData[2] == "summernote") editorValue = $(this).summernote("code");
        if (editorData[2] == "value") editorValue = $(this).val();

        editorSave[editorData[1]] = editorValue;
      }
    });

    return getData ? editorSave[getData] : editorSave;
  },
  parse: function(el) {
    var editorData = $(el).attr("data-editor");
    editorData = editorData.replace(/\'/g, "\"");
    editorData = JSON.parse(editorData);

    return editorData;
  },
  changed: function() {
    var arr1 = Object.values(editor.data());
    var arr2 = Object.values(editor.saved);

    if (arr1.length !== arr2.length) {
      return true;
    }

    for (var i = 0; i < arr1.length; i++) {
      if (arr1[i] !== arr2[i]) {
        return true;
      }
    }

    return false;
  },
  status: function(msg, alt) {
    return;

    if (editor.timeout) clearTimeout(editor.timeout);

    $("#editor-nav .editor-status > span").text(msg);

    if (alt) {
      editor.timeout = setTimeout(function() {
        editor.status("Editor");
      }, 3000);
    }
  }
};

$(document).ready(function() {
  editor.init();
});
