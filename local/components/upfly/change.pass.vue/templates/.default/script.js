$(function () {
  $(".item .label").click(function () {
    if ($(this).parent().children().is("select"))
      $(this).parent().children("select").trigger("click");
    if ($(this).parent().children().is("input"))
      $(this).parent().children("input").focus();

    $(this).remove();
  });

  $(".item input").on("focus", function () {
    if ($(this).parent().children().is("span"))
      $(this).parent().children("span").remove();
  });
});
