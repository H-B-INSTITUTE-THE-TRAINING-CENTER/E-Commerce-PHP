let cartCount = 0;

const cartCounter = document.getElementById("cartCount");
const addToCartButtons = document.querySelectorAll(".add-to-cart");
const productItems = document.querySelectorAll(".product-item");
const searchForm = document.getElementById("searchForm");
const searchInput = document.getElementById("searchInput");
const clearSearch = document.getElementById("clearSearch");


/* =========================
   ADD TO CART
========================= */

addToCartButtons.forEach(function(button) {
    button.addEventListener("click", function () {
        cartCount ++;
        cartCounter.textContent = cartCount;
        alert("Product added to cart!");

    })
})

/* =========================
   PRODUCT SEARCH
========================= */

searchForm.addEventListener("submit", function (event) {
    event.preventDefault();
    const searchTerm = searchInput.value.toLowerCase().trim();

    productItems.forEach(function (product) {
        const productName = product.dataset.name;
        const productCategory = product.dataset.category;

        if (productName.includes(searchTerm) || productCategory.includes(searchTerm)) {
            product.style.display = ""
        }else{
            product.style.display = "none"
        }

    })
    
})

/* =========================
   CLEAR SEARCH
========================= */

clearSearch.addEventListener("click",function () {
    searchInput.value = "";

    productItems.forEach(function (product) {
        product.style.display = "";
    })
})