$(document).bind("editor.summernote", function() {
  $.summernote.options.fontNames.push("Raleway");
});

$(document).bind("editor.init", function() {
  $("#heroVideoModal")
    .bind("show.bs.modal", function(event) {
      $("#heroVideoInput").val(editor.data("hero_video") ? ("https://www.youtube.com/watch?v=" + editor.data("hero_video")) : "");
    })
    .bind("hide.bs.modal", function(event) {
      var editorValue = $("#heroVideoInput").val().match(/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/);
      editorValue = (editorValue && editorValue[7].length == 11) ? editorValue[7] : false;

      if ($("#heroVideoInput").val() && !editorValue) {
        alert("Invalid YouTube URL.");

        return false;
      } else {
        $("#hero").addClass("hero-lg");

        $("#hero .hero-video iframe").attr("data-value", editorValue ? editorValue : "");

        if (editorValue) {
          $("#hero").removeClass("hero-lg");
          $("#hero .hero-video iframe").attr("src", "https://www.youtube.com/embed/" + editorValue + "?rel=0&amp;controls=0&amp;showinfo=0");
        }
      }
    });

  $("#heroImageModal")
    .bind("shown.bs.modal", function(event) {
      $("#heroImageModal .modal-body > div").text("Loading...");

      $.getJSON("/admin/ajax/getimages", function(data) {
        $("#heroImageModal .modal-body > div").empty();

        if (data.status == "success") {
          $.each(data.data, function(editorItemID, editorItem) {
            var $el = $("<img />");
            $el
              .addClass("img-thumbnail")
              .attr("alt", editorItem)
              .attr("src", "/assets/uploads/thumbnail/" + editorItem)
              .attr("data-value", editorItem)
              .attr("data-editor", "['action','','hero_image']");

            $("#heroImageModal .modal-body > div").append($el);
          });
        } else {
          $("#heroImageModal").modal("hide");
          alert(data.message);
        }

        editor.bind();
      });
    });

  editor.functions["hero_image"] = function() {
    $("#hero")
      .attr("data-value", $(this).attr("data-value"))
      .css("background-image", "url(/assets/uploads/" + $(this).attr("data-value") + ")");
    $("#heroImageModal").modal("hide");
  };
});
