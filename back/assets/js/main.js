!function(){function e(){document.querySelector(".preloader").style.opacity="0",document.querySelector(".preloader").style.display="none"}window.onload=function(){window.setTimeout(e,300)},window.onscroll=function(){var e=document.querySelector(".hero-section-wrapper-5 .header"),o=e.offsetTop;window.pageYOffset>o?e.classList.add("sticky"):e.classList.remove("sticky");var t=document.querySelector(".scroll-top");document.body.scrollTop>50||document.documentElement.scrollTop>50?t.style.display="flex":t.style.display="none"};let o=document.querySelector(".header-2 .navbar-toggler");var t=document.querySelector(".header-2 .navbar-collapse");document.querySelectorAll(".header-2 .page-scroll").forEach(e=>e.addEventListener("click",()=>{o.classList.remove("active"),t.classList.remove("show")})),window.document.addEventListener("scroll",(function(e){for(var o=document.querySelectorAll(".page-scroll"),t=window.pageYOffset||document.documentElement.scrollTop||document.body.scrollTop,r=0;r<o.length;r++){var c=o[r],l=c.getAttribute("href"),n=document.querySelector(l),s=t+73;n.offsetTop<=s&&n.offsetTop+n.offsetHeight>s?(document.querySelector(".page-scroll").classList.remove("active"),c.classList.add("active")):c.classList.remove("active")}})),new counterUp({start:0,duration:2e3,intvalues:!0,interval:100,append:" "}).start(),(new WOW).init()}();