function showPopup(type, message) {
    //dokumanın dilini alalım
    var languageCode = document.documentElement.lang;
    //mesaj url ile gönderilirken bozulmasın
    message = encodeURIComponent(message);
    const xhr = new XMLHttpRequest();
    //console.log(`/?/control/popup/get/show&type=${type}&message=${message}&languageCode=${languageCode}`);
    xhr.open("GET", `/?/control/popup/get/show&languageCode=${languageCode}&type=${type}&message=${message}`);
    xhr.send();

    xhr.onload = function () {
        if (xhr.status === 200) {
            document.body.insertAdjacentHTML("beforeend", xhr.responseText);
        }
    };
}