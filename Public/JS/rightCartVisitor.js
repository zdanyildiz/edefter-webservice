
    let asideRightVisitor = document.querySelector(".aside-right-visitor");
    let asideRightVisitorClose = document.querySelector(".aside-right-visitor-close");
    asideRightVisitorClose.addEventListener("click", function () {
        asideRightVisitor.classList.remove("active");
    });


    let showAsideRightVisitor = document.querySelector(".show-aside-right-visitor");
    showAsideRightVisitor.addEventListener("click", function () {
        let asideRightVisitor = document.querySelector(".aside-right-visitor");
        asideRightVisitor.classList.add("active");
    });
