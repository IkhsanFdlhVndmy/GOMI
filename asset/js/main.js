// let nav = document.querySelector(".navbar-nav");
// window.onscroll = function () {
//   if (document.documentElement.scrollTop > 50) {
//     nav.classList.add("header-scrolled");
//   } else {
//     nav.classList.remove("header-scrolled");
//   }
// };

// let navBar = document.querySelectorAll(".nav-link");
// let navCollapse = document.querySelector(".navbar-collapse.collapse");
// navBar.forEach(function (e) {
//   e.addEventListener("click", function () {
//     navCollapse.classList.remove("show");
//   });
// });


// untuk slide 

let currentIndex = 0;

function showSlide(index) {
    const slides = document.querySelectorAll('.slider-item');
    const totalSlides = slides.length;

    // Cek agar index tetap dalam rentang yang benar
    if (index >= totalSlides) {
        currentIndex = 0;
    } else if (index < 0) {
        currentIndex = totalSlides - 1;
    } else {
        currentIndex = index;
    }

    // Pindahkan slider ke gambar yang sesuai
    const slider = document.querySelector('.slider');
    slider.style.transform = `translateX(-${currentIndex * 100}%)`;
}

// Fungsi untuk navigasi
function moveSlide(direction) {
    showSlide(currentIndex + direction);
}

// Tampilkan slide pertama saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    showSlide(currentIndex);
});

// Menambahkan event listeners pada tombol
document.querySelector('.prev').addEventListener('click', function() {
    moveSlide(-1); // Pindah ke slide sebelumnya
});
document.querySelector('.next').addEventListener('click', function() {
    moveSlide(1); // Pindah ke slide berikutnya
});
console.log("Tombol Next diklik");

window.addEventListener("scroll", function () {
  const header = document.querySelector(".fixed-header");
  if (window.scrollY > 50) {
      header.style.backgroundColor = "#024b50"; // Ubah warna header
      header.style.padding = "5px 20px"; // Kurangi padding saat scroll
  } else {
      header.style.backgroundColor = "#062c30";
      header.style.padding = "10px 20px"; // Kembalikan padding
  }
});

$(function (){
  $(document).scroll(function () {
      var $nav = $(".navbar");
      if($(this).scrollTop() > $nav.height()){
          $nav.addClass("scrolled");
          $nav.removeClass("navbar-dark");
          $nav.addClass("navbar-light");
      } else {
          $nav.removeClass("scrolled");
          $nav.addClass("navbar-dark");
          $nav.removeClass("navbar-light");
      }
  }); 
});
