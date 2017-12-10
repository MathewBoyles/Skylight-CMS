$(document).ready(function() {
  $("#hero .btn-video").click(function() {
    $("#videoModal .modal-body").html($("#hero .hero-video").html());
    $("#videoModal").modal();
  });

  $("#videoModal").bind("hidden.bs.modal", function() {
    $("#videoModal .modal-body").empty();
  });

  $("#cartCheckout").click(function(event) {
    $("#cartForm input[name=\"checkout\"]").val("1");
    $("#cartForm").submit();

    event.preventDefault();
  });
});
