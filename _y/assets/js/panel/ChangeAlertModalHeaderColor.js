function changeAlertModalHeaderColor(color){
    let alertModalCardHead = $("#alertModal .card-head");
    alertModalCardHead.removeClass("style-danger").removeClass("style-success").removeClass("style-warning");
    alertModalCardHead.addClass("style-"+color);
}