var lastScrollTop = 0;
window.addEventListener('scroll', function(){
    var st = window.pageYOffset || document.documentElement.scrollTop;
    const elementStyle = document.getElementById('scrollNav').style;
    if (st > lastScrollTop) {
        elementStyle.top ='-62px';
    } else {
        elementStyle.top='0';
    }
    lastScrollTop = st <= 0 ? 0 : st;
}, false);