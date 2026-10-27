    </main>

    <footer class="footer">
        <div class="container footer-grid">
            <div>
                <p class="logo">Store</p>
                <p>Small-batch t-shirts, screen-printed by hand on our own press. Heavy cotton, honest prices.</p>
            </div>

            <div>
                <h3>Shop</h3>
                <ul>
                    <li><a href="products.php">All tees</a></li>
                    <li><a href="products.php?category=Oversized">Oversized</a></li>
                    <li><a href="products.php?category=Vintage+Wash">Vintage Wash</a></li>
                    <li><a href="gallery.php">Lookbook</a></li>
                </ul>
            </div>

            <div>
                <h3>Help</h3>
                <ul>
                    <li><a href="contact.php">Custom print requests</a></li>
                    <li><a href="about.php#fabric">Fabric and sizing</a></li>
                    <!-- Inline CSS example -->
                    <li style="color: var(--fluoro);">hello@store.com</li>
                </ul>
            </div>
        </div>

        <div class="container footer-bottom">
            <p>&copy; <?= date('Y') ?> Store. Printed in small batches.</p>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
