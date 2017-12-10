$(document).ready(function() {
  $("#logout,#logoutSM").click(function(event) {
    $("#logoutForm").submit();

    event.preventDefault();
  });

  if ($().sortable) {
    $(".sortable").sortable({
      placeholder: "ui-state-highlight"
    });
    $(".sortable").disableSelection();
  }

  $("#menuSort > li > i.fa-trash").click(function() {
    if (confirm("Remove this link?")) {
      $el = $("<input />");
      $el
        .attr("type", "hidden")
        .attr("name", "remove[]")
        .val($(this).parent().attr("data-id"));
      $("#menuSort").append($el);

      $(this).parent().remove();
    }
  });

  $("[data-picker]").each(function() {
    $el = $(this);

    $(this).find("[data-value]").click(function() {
      $el.find("[data-value]").removeClass("active");
      $(this).addClass("active");
      $("input[name=\"value[" + $el.attr("data-picker") + "]\"]").val($(this).attr("data-value"));
    });
  });

  if ($(".summernote").length) {
    $.getScript("/node_modules/summernote/dist/summernote-bs4.js", function() {
      $.summernote.options.fontNames.push("Raleway");

      $(".summernote").summernote();

      $(".note-editable").trigger("input");
    });
  }

});
