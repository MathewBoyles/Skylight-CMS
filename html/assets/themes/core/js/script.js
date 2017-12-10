$(document).ready(function() {
  $("#hero .btn-video").click(function() {
    $("#videoModal .modal-body").html($("#hero .hero-video").html());
    $("#videoModal").modal();
  });

  $("#videoModal").bind("hidden.bs.modal", function() {
    $("#videoModal .modal-body").empty();
  });
});
