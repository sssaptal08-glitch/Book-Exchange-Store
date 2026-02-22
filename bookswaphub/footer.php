<section class="footer">
        <div class="box-container">
            <div class="box">
                <h3>location</h3>
                <a href="#"><i class="fas fa-map-marker-alt"></i> India</a>
                <a href="#"><i class="fas fa-map-marker-alt"></i> USA</a>
                <a href="#"><i class="fas fa-map-marker-alt"></i> Japan</a>
                <a href="#"><i class="fas fa-map-marker-alt"></i> England</a>
                <a href="#"><i class="fas fa-map-marker-alt"></i> Russia</a>
                <a href="#"><i class="fas fa-map-marker-alt"></i> France</a>
            </div>

            <div class="box">
                <h3>quick links</h3>
                <a href="#"><i class="fas fa-arrow-right"></i> Home</a>
                <a href="#"><i class="fas fa-arrow-right"></i> Featured</a>
                <a href="#"><i class="fas fa-arrow-right"></i> Arrivals</a>
                <a href="#"><i class="fas fa-arrow-right"></i> Reviews</a>
                <a href="#"><i class="fas fa-arrow-right"></i> Blogs</a>
            </div>
            <div class="box">
                <h3>extra links</h3>
                <a href="#"><i class="fas fa-arrow-right"></i> Account Info</a>
                <a href="#"><i class="fas fa-arrow-right"></i> Ordered Items</a>
                <a href="#"><i class="fas fa-arrow-right"></i> Privacy Policy</a>
                <a href="#"><i class="fas fa-arrow-right"></i> Payment Method</a>
                <a href="#"><i class="fas fa-arrow-right"></i> Our Service</a>
            </div>
            <div class="box">
                <h3>contact info</h3>
                <a href="tel:+919552265955"><i class="fas fa-phone"></i> +91-95522-65955</a>
                <a href="tel:+12362659455"><i class="fas fa-phone"></i> +123-626-594-55</a>
                <a href="mailto:bookswaphub@gmail.com"><i class="fas fa-envelope"></i> bookswaphub@gmail.com</a>
                <img src="image/map.png" alt="Map" class="map">
            </div>
        </div>
        <div class="share">
            <a href="#" class="fab fa-facebook-f"></a>
            <a href="#" class="fab fa-instagram"></a>
            <a href="#" class="fab fa-twitter"></a>
            <a href="#" class="fab fa-linkedin"></a>
        </div>
        <div class="credit">created by <span>BookSwapHub</span> | all rights reserved!</div>
    </section>

    <!-- end footer section -->

    <!-- swiper link -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></scrip>
    <!-- custom js file link -->
     <script>
        function confirmLogout() {
    return confirm("Are you sure you want to logout?");
}
     </script>
     <script>
    const isLoggedIn = <?php echo isset($_SESSION['username']) ? 'true' : 'false'; ?>;
</script>
    <script src="js/script.js"></script>
    <script src="js/wishlist.js"></script>
</body>
</html>