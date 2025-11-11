document.addEventListener("DOMContentLoaded", function () {
  const search = new URLSearchParams(window.location.search).get("search");
  if (!search) return;

  const searchLower = search.trim().toLowerCase();
  const carousel = document.getElementById("doctorCarousel");
  if (!carousel) return;

  const items = carousel.querySelectorAll(".carousel-item");

  items.forEach((item, index) => {
    const titles = item.querySelectorAll(".card-title");
    for (let title of titles) {
      if (title.textContent.toLowerCase().includes(searchLower)) {
        const bsCarousel = bootstrap.Carousel.getInstance(carousel) || new bootstrap.Carousel(carousel);
        bsCarousel.to(index);
        return;
      }
    }
  });
});